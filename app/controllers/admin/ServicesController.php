<?php
declare(strict_types=1);

namespace app\controllers\admin;

use app\core\Controller;
use app\model\admin\Branch;
use app\model\admin\BranchService;
use app\model\admin\PackageItem;
use app\model\admin\Service;
use app\model\admin\ServiceType;

class ServicesController extends Controller
{
    // Initialize controller dependencies and request context.
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->requireAdmin();
    }

    // Display the main listing or dashboard page.
    public function index(): void
    {
        $serviceModel = new Service();
        $packageModel = new PackageItem();
        $allRows = $serviceModel->allWithTypeAndBranches();
        $tabs = $serviceModel->distinctTypesForTabs();
        $packageStats = $serviceModel->packageAnalytics();

        $services = [];
        $packages = [];

        foreach ($allRows as $row) {
            $isPackage = in_array(strtolower((string) ($row['type_name'] ?? '')), ['package', 'packages'], true);

            if ($isPackage) {
                $packageId = $serviceModel->getPackageIdForService((int) $row['service_id']);
                $items = $packageId ? $packageModel->itemsForPackage($packageId) : [];
                $summary = $serviceModel->packageSummary((int) $row['service_id']);
                $stats = $packageStats[(int) $row['service_id']] ?? [
                    'usage_count' => 0,
                    'last_booked_date' => null,
                    'estimated_revenue' => 0,
                ];

                $row['package_id'] = $packageId;
                $row['package_items'] = $items;
                $row['package_item_count'] = count($items);
                $row['package_duration'] = $summary['total_duration'];
                $row['package_base_total'] = $summary['base_total'];
                $row['usage_count'] = $stats['usage_count'];
                $row['last_booked_date'] = $stats['last_booked_date'];
                $row['estimated_revenue'] = $stats['estimated_revenue'];

                $packages[] = $row;
            } else {
                $services[] = $row;
            }
        }

        $this->view('admin/admin-viewservices/index', [
            'pageTitle' => 'Service & Package Management',
            'current' => 'services',
            'base' => BASE_URL,
            'services' => $services,
            'packages' => $packages,
            'tabs' => $tabs,
        ]);
    }

    // Render the form for creating a new record.
    public function create(): void
    {
        $serviceModel = new Service();
        $packageTypeId = $serviceModel->findPackageTypeId();

        $types = array_filter(
            (new ServiceType())->all(),
            fn($t) => (int) $t['type_id'] !== $packageTypeId
        );

        $branches = (new Branch())->allActive();
        $nextCode = $serviceModel->nextCode();

        $this->view('admin/admin-viewservices/create', [
            'types' => $types,
            'branches' => $branches,
            'nextCode' => $nextCode,
            'base' => BASE_URL,
            'current' => 'services',
            'servicesForPackage' => [],
            'packageTypeId' => $packageTypeId,
        ]);
    }

    // Validate input and save a new record.
    public function store(): void
    {
        $pdo = db();
        $pdo->beginTransaction();

        try {
            $serviceModel = new Service();
            $branchModel = new Branch();
            $bsModel = new BranchService();

            $data = $this->sanitize($_POST);
            $data['service_code'] = $serviceModel->nextCode();

            if ($serviceModel->isPackageType($data['type_id'])) {
                $pdo->rollBack();
                http_response_code(422);
                echo 'Packages must be created from the package module.';
                return;
            }

            $errors = $this->validate($data);
            if ($errors) {
                $pdo->rollBack();
                http_response_code(422);
                echo implode("<br>", $errors);
                return;
            }

            $serviceId = $serviceModel->create($data);

            $applyScope = $_POST['apply_scope'] ?? 'all';
            $branchIds = ($applyScope === 'all')
                ? $branchModel->idsOfActive()
                : array_map('intval', $_POST['branches'] ?? []);

            $bsModel->replaceForService($serviceId, $branchIds);

            $pdo->commit();
            header('Location: ' . rtrim(BASE_URL, '/') . '/admin/services');
            exit;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo 'Create failed: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        }
    }

    // Render the form for editing an existing record.
    public function edit($id): void
    {
        $id = (int) $id;

        $serviceModel = new Service();
        $row = $serviceModel->findById($id);

        if (!$row) {
            http_response_code(404);
            echo 'Service not found';
            return;
        }

        if ($serviceModel->isPackageType($row['type_id'] ?? null)) {
            header('Location: ' . rtrim(BASE_URL, '/') . '/admin/packages/' . $id . '/edit');
            exit;
        }

        $packageTypeId = $serviceModel->findPackageTypeId();
        $types = array_filter(
            (new ServiceType())->all(),
            fn($t) => (int) $t['type_id'] !== $packageTypeId
        );

        $branches = (new Branch())->allActive();
        $bsModel = new BranchService();
        $attached = $bsModel->branchIdsForService($id);
        $allActive = array_map(fn($b) => (int) $b['branch_id'], $branches);
        $isAll = !array_diff($allActive, $attached) && !empty($allActive);

        $this->view('admin/admin-viewservices/edit', [
            'row' => $row,
            'types' => $types,
            'branches' => $branches,
            'attached' => $attached,
            'applyAll' => $isAll,
            'base' => BASE_URL,
            'current' => 'services',
            'servicesForPackage' => [],
            'packageItems' => [],
            'packageTypeId' => $packageTypeId,
            'packageSummary' => null,
            'isPackage' => false,
            'packageCode' => null,
            'packageId' => null,
        ]);
    }

    // Validate input and update an existing record.
    public function update($id): void
    {
        $id = (int) $id;
        $pdo = db();
        $pdo->beginTransaction();

        try {
            $serviceModel = new Service();
            $branchModel = new Branch();
            $bsModel = new BranchService();

            $existing = $serviceModel->findById($id);
            if (!$existing) {
                $pdo->rollBack();
                http_response_code(404);
                echo 'Service not found';
                return;
            }

            if ($serviceModel->isPackageType($existing['type_id'] ?? null)) {
                $pdo->rollBack();
                http_response_code(422);
                echo 'Packages must be updated from the package module.';
                return;
            }

            $data = $this->sanitize($_POST);
            unset($data['created_at']);

            if ($serviceModel->isPackageType($data['type_id'])) {
                $pdo->rollBack();
                http_response_code(422);
                echo 'Cannot convert a normal service into a package here.';
                return;
            }

            $errors = $this->validate($data);
            if ($errors) {
                $pdo->rollBack();
                http_response_code(422);
                echo implode("<br>", $errors);
                return;
            }

            $serviceModel->updateById($id, $data);

            $applyScope = $_POST['apply_scope'] ?? 'all';
            $branchIds = ($applyScope === 'all')
                ? $branchModel->idsOfActive()
                : array_map('intval', $_POST['branches'] ?? []);

            $bsModel->replaceForService($id, $branchIds);

            $pdo->commit();
            header('Location: ' . rtrim(BASE_URL, '/') . '/admin/services');
            exit;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo 'Update failed: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        }
    }

    // Delete the selected record.
    public function destroy($id): void
    {
        $id = (int) $id;
        $pdo = db();
        $pdo->beginTransaction();

        try {
            $serviceModel = new Service();
            $bsModel = new BranchService();
            $pkgModel = new PackageItem();

            $row = $serviceModel->findById($id);
            if (!$row) {
                $pdo->rollBack();
                http_response_code(404);
                echo 'Service not found';
                return;
            }

            // Remove any package items that reference this service
            $stmt = $pdo->prepare("DELETE FROM service_package_items WHERE service_id = :id");
            $stmt->execute(['id' => $id]);

            // Remove branch associations
            $bsModel->replaceForService($id, []);

            // If it's a package service, also delete the package record
            if ($serviceModel->isPackageType($row['type_id'] ?? null)) {
                $packageId = $serviceModel->getPackageIdForService($id);
                if ($packageId) {
                    $stmt = $pdo->prepare("DELETE FROM packages WHERE package_id = :id");
                    $stmt->execute(['id' => $packageId]);
                }
            }

            $serviceModel->deleteById($id);

            $pdo->commit();
            header('Location: ' . rtrim(BASE_URL, '/') . '/admin/services');
            exit;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo 'Delete failed: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        }
    }

    // Handle sanitize operation.
    private function sanitize(array $src): array
    {
        $get = static fn(string $k, string $d = ''): string => trim((string) ($src[$k] ?? $d));

        $name = $get('name');
        $description = $get('description');
        $typeId = (int) $get('type_id', '0');
        $typeId = $typeId > 0 ? $typeId : null;

        $duration = (int) $get('base_duration_minutes', '0');
        if ($duration < 0) {
            $duration = 0;
        }

        $priceRaw = $get('default_price', '0');
        $price = is_numeric($priceRaw) ? number_format((float) $priceRaw, 2, '.', '') : '0.00';

        $status = $get('status', 'active');
        if (!in_array($status, ['active', 'inactive', 'pending', 'rejected'], true)) {
            $status = 'active';
        }

        return [
            'name' => $name,
            'description' => $description,
            'type_id' => $typeId,
            'base_duration_minutes' => $duration,
            'default_price' => $price,
            'status' => $status,
            'created_at' => date('Y-m-d H:i:s'),
        ];
    }

    // Handle validate operation.
    private function validate(array $data): array
    {
        $errors = [];

        if (($data['name'] ?? '') === '') {
            $errors[] = 'Service name is required.';
        }

        if (!is_null($data['type_id']) && !is_int($data['type_id'])) {
            $errors[] = 'Invalid service type.';
        }

        if (!preg_match('/^\d+(\.\d{1,2})?$/', (string) ($data['default_price'] ?? ''))) {
            $errors[] = 'Price must be numeric with up to 2 decimal places.';
        }

        if (!is_int($data['base_duration_minutes'] ?? null) || ($data['base_duration_minutes'] ?? -1) < 0) {
            $errors[] = 'Duration must be a non-negative integer.';
        }

        return $errors;
    }

    // Ensure the current session belongs to an admin user.
    private function requireAdmin(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $u = $_SESSION['user'] ?? null;
        if (!$u || (($u['role'] ?? '') !== 'admin')) {
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }
    }
}