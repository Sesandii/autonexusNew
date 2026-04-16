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
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->requireAdmin();
    }

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
                $packageIdForService = (new Service())->getPackageIdForService((int) $row['service_id']);
                $items = $packageIdForService ? $packageModel->itemsForPackage((int) $packageIdForService) : [];
                $summary = $serviceModel->packageSummary((int) $row['service_id']);
                $stats = $packageStats[(int) $row['service_id']] ?? [
                    'usage_count' => 0,
                    'last_booked_date' => null,
                    'estimated_revenue' => 0,
                ];

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

    public function create(): void
    {
        $serviceModel = new Service();
        $types = (new ServiceType())->all();
        $branches = (new Branch())->allActive();
        $nextCode = $serviceModel->nextCode();
        $servicesForPackage = $serviceModel->allAtomicServices();
        $packageTypeId = $serviceModel->findPackageTypeId();

        $this->view('admin/admin-viewservices/create', [
            'types' => $types,
            'branches' => $branches,
            'nextCode' => $nextCode,
            'base' => BASE_URL,
            'current' => 'services',
            'servicesForPackage' => $servicesForPackage,
            'packageTypeId' => $packageTypeId,
        ]);
    }

    public function store(): void
    {
        $pdo = db();
        $pdo->beginTransaction();

        try {
            $serviceModel = new Service();
            $branchModel = new Branch();
            $bsModel = new BranchService();
            $pkgModel = new PackageItem();

            $data = $this->sanitize($_POST);
            $data['service_code'] = $serviceModel->nextCode();

            $isPackage = $serviceModel->isPackageType($data['type_id']);

            $packageItems = $this->normalizePackageItems($_POST['package_items'] ?? []);

            if ($isPackage) {
                $totals = $this->computePackageTotals($packageItems, $serviceModel->allAtomicServices());
                $pricing = $this->applyPackagePricingRule(
                    $totals['price'],
                    $_POST['pricing_mode'] ?? 'auto',
                    $_POST['discount_type'] ?? 'none',
                    $_POST['discount_value'] ?? '0',
                    $_POST['manual_price'] ?? ''
                );

                $data['base_duration_minutes'] = $totals['duration'];
                $data['default_price'] = number_format($pricing, 2, '.', '');
            }

            $errors = $this->validate($data, $isPackage, $packageItems);
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

            if ($isPackage) {
                $packageId = $serviceModel->createPackageRecord($data);
                $pkgModel->replaceItems($packageId, $packageItems);
            }

            $pdo->commit();
            header('Location: ' . rtrim(BASE_URL, '/') . '/admin/admin-viewservices');
            exit;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo 'Create failed: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        }
    }

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

        $types = (new ServiceType())->all();
        $branches = (new Branch())->allActive();
        $bsModel = new BranchService();
        $attached = $bsModel->branchIdsForService($id);
        $allActive = array_map(fn($b) => (int) $b['branch_id'], $branches);
        $servicesForPackage = $serviceModel->allAtomicServices();
        $packageItems = (new PackageItem())->itemsForPackage($id);
        $packageTypeId = $serviceModel->findPackageTypeId();
        $isAll = !array_diff($allActive, $attached) && !empty($allActive);

        $summary = $serviceModel->packageSummary($id);
        $isPackage = $serviceModel->isPackageType($row['type_id'] ?? null);
        $packageCode = $isPackage ? $serviceModel->getPackageCodeForService($id) : null;

        $this->view('admin/admin-viewservices/edit', [
            'row' => $row,
            'types' => $types,
            'branches' => $branches,
            'attached' => $attached,
            'applyAll' => $isAll,
            'base' => BASE_URL,
            'current' => 'services',
            'servicesForPackage' => $servicesForPackage,
            'packageItems' => $packageItems,
            'packageTypeId' => $packageTypeId,
            'packageSummary' => $summary,
            'isPackage' => $isPackage,
            'packageCode' => $packageCode,
        ]);
    }

    public function update($id): void
    {
        $id = (int) $id;
        $pdo = db();
        $pdo->beginTransaction();

        try {
            $serviceModel = new Service();
            $branchModel = new Branch();
            $bsModel = new BranchService();
            $pkgModel = new PackageItem();

            $existing = $serviceModel->findById($id);
            if (!$existing) {
                $pdo->rollBack();
                http_response_code(404);
                echo 'Service not found';
                return;
            }

            $data = $this->sanitize($_POST);
            unset($data['created_at']);

            $isPackage = $serviceModel->isPackageType($data['type_id']);
            $packageItems = $this->normalizePackageItems($_POST['package_items'] ?? []);

            if ($isPackage) {
                $totals = $this->computePackageTotals($packageItems, $serviceModel->allAtomicServices());
                $pricing = $this->applyPackagePricingRule(
                    $totals['price'],
                    $_POST['pricing_mode'] ?? 'auto',
                    $_POST['discount_type'] ?? 'none',
                    $_POST['discount_value'] ?? '0',
                    $_POST['manual_price'] ?? ''
                );

                $data['base_duration_minutes'] = $totals['duration'];
                $data['default_price'] = number_format($pricing, 2, '.', '');
            }

            $errors = $this->validate($data, $isPackage, $packageItems);
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

            if ($isPackage) {
                $packageId = $serviceModel->getPackageIdForService($id);
                if ($packageId) {
                    $serviceModel->updatePackageRecord($packageId, $data);
                    $pkgModel->replaceItems($packageId, $packageItems);
                } else {
                    $packageId = $serviceModel->createPackageRecord($data);
                    $pkgModel->replaceItems($packageId, $packageItems);
                }
            } else {
                $pkgModel->replaceItems($id, []);
            }

            $pdo->commit();
            header('Location: ' . rtrim(BASE_URL, '/') . '/admin/admin-viewservices');
            exit;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo 'Update failed: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        }
    }

    public function destroy($id): void
    {
        $id = (int) $id;
        $pdo = db();
        $pdo->beginTransaction();

        try {
            $serviceModel = new Service();
            $bsModel = new BranchService();
            $pkgModel = new PackageItem();

            if (!$serviceModel->findById($id)) {
                $pdo->rollBack();
                http_response_code(404);
                echo 'Service not found';
                return;
            }

            // Remove branch associations first
            $bsModel->replaceForService($id, []);

            // If it's a package, remove package items and package record
            $packageId = $serviceModel->getPackageIdForService($id);
            if ($packageId) {
                $pkgModel->replaceItems($packageId, []);
                $stmt = $pdo->prepare("DELETE FROM packages WHERE package_id = :id");
                $stmt->execute(['id' => $packageId]);
            }

            // Finally delete the service
            $serviceModel->deleteById($id);

            $pdo->commit();
            header('Location: ' . rtrim(BASE_URL, '/') . '/admin/admin-viewservices');
            exit;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo 'Delete failed: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        }
    }

    public function createPackage(): void
    {
        $serviceModel = new Service();
        $branches = (new Branch())->allActive();
        $nextCode = $serviceModel->nextPackageCode();
        $servicesForPackage = $serviceModel->allAtomicServices();

        $this->view('admin/admin-viewservices/create-package', [
            'branches' => $branches,
            'nextCode' => $nextCode,
            'base' => BASE_URL,
            'current' => 'services',
            'servicesForPackage' => $servicesForPackage,
        ]);
    }

    public function storePackage(): void
    {
        $pdo = db();
        $pdo->beginTransaction();

        try {
            $serviceModel = new Service();
            $branchModel = new Branch();
            $bsModel = new BranchService();
            $pkgModel = new PackageItem();
            $packageTypeId = $serviceModel->findPackageTypeId();

            $data = $this->sanitize($_POST);
            $data['type_id'] = $packageTypeId;
            $data['service_code'] = $serviceModel->nextCode();

            $packageItems = $this->normalizePackageItems($_POST['package_items'] ?? []);

            $totals = $this->computePackageTotals($packageItems, $serviceModel->allAtomicServices());
            $pricing = $this->applyPackagePricingRule(
                $totals['price'],
                $_POST['pricing_mode'] ?? 'auto',
                $_POST['discount_type'] ?? 'none',
                $_POST['discount_value'] ?? '0',
                $_POST['manual_price'] ?? ''
            );

            $data['base_duration_minutes'] = $totals['duration'];
            $data['default_price'] = number_format($pricing, 2, '.', '');

            $errors = $this->validate($data, true, $packageItems);
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

            $packageId = $serviceModel->createPackageRecord($data);
            $pkgModel->replaceItems($packageId, $packageItems);

            $pdo->commit();
            header('Location: ' . rtrim(BASE_URL, '/') . '/admin/admin-viewservices');
            exit;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo 'Create failed: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        }
    }

    public function editPackage($id): void
    {
        $id = (int) $id;

        $serviceModel = new Service();
        $row = $serviceModel->findById($id);

        if (!$row) {
            http_response_code(404);
            echo 'Package not found';
            return;
        }

        $branches = (new Branch())->allActive();
        $bsModel = new BranchService();
        $attached = $bsModel->branchIdsForService($id);
        $allActive = array_map(fn($b) => (int) $b['branch_id'], $branches);
        $servicesForPackage = $serviceModel->allAtomicServices();
        $packageItems = (new PackageItem())->itemsForPackage($id);
        $isAll = !array_diff($allActive, $attached) && !empty($allActive);

        $packageCode = $serviceModel->getPackageCodeForService($id);

        $this->view('admin/admin-viewservices/edit-package', [
            'row' => $row,
            'branches' => $branches,
            'attached' => $attached,
            'applyAll' => $isAll,
            'base' => BASE_URL,
            'current' => 'services',
            'servicesForPackage' => $servicesForPackage,
            'packageItems' => $packageItems,
            'packageCode' => $packageCode,
        ]);
    }

    public function updatePackage($id): void
    {
        $id = (int) $id;
        $pdo = db();
        $pdo->beginTransaction();

        try {
            $serviceModel = new Service();
            $branchModel = new Branch();
            $bsModel = new BranchService();
            $pkgModel = new PackageItem();

            $existing = $serviceModel->findById($id);
            if (!$existing) {
                $pdo->rollBack();
                http_response_code(404);
                echo 'Package not found';
                return;
            }

            $data = $this->sanitize($_POST);
            unset($data['created_at']);
            $data['type_id'] = $existing['type_id'];

            $packageItems = $this->normalizePackageItems($_POST['package_items'] ?? []);

            $totals = $this->computePackageTotals($packageItems, $serviceModel->allAtomicServices());
            $pricing = $this->applyPackagePricingRule(
                $totals['price'],
                $_POST['pricing_mode'] ?? 'auto',
                $_POST['discount_type'] ?? 'none',
                $_POST['discount_value'] ?? '0',
                $_POST['manual_price'] ?? ''
            );

            $data['base_duration_minutes'] = $totals['duration'];
            $data['default_price'] = number_format($pricing, 2, '.', '');

            $errors = $this->validate($data, true, $packageItems);
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

            $packageId = $serviceModel->getPackageIdForService($id);
            if ($packageId) {
                $serviceModel->updatePackageRecord($packageId, $data);
                $pkgModel->replaceItems($packageId, $packageItems);
            } else {
                $packageId = $serviceModel->createPackageRecord($data);
                $pkgModel->replaceItems($packageId, $packageItems);
            }

            $pdo->commit();
            header('Location: ' . rtrim(BASE_URL, '/') . '/admin/admin-viewservices');
            exit;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo 'Update failed: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        }
    }

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

    private function validate(array $data, bool $isPackage, array $packageItems): array
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

        if ($isPackage && empty($packageItems)) {
            $errors[] = 'A package must contain at least one service item.';
        }

        return $errors;
    }

    private function normalizePackageItems(array $raw): array
    {
        $items = [];

        foreach ($raw as $row) {
            $serviceId = (int) ($row['service_id'] ?? 0);
            $quantity = max(1, (int) ($row['quantity'] ?? 1));

            if ($serviceId <= 0) {
                continue;
            }

            $items[] = [
                'service_id' => $serviceId,
                'quantity' => $quantity,
            ];
        }

        return $items;
    }

    private function computePackageTotals(array $packageItems, array $availableServices): array
    {
        $serviceMap = [];
        foreach ($availableServices as $service) {
            $serviceMap[(int) $service['service_id']] = $service;
        }

        $duration = 0;
        $price = 0.00;

        foreach ($packageItems as $item) {
            $serviceId = (int) $item['service_id'];
            $qty = max(1, (int) $item['quantity']);

            if (!isset($serviceMap[$serviceId])) {
                continue;
            }

            $duration += ((int) $serviceMap[$serviceId]['base_duration_minutes']) * $qty;
            $price += ((float) $serviceMap[$serviceId]['default_price']) * $qty;
        }

        return [
            'duration' => $duration,
            'price' => round($price, 2),
        ];
    }

    private function applyPackagePricingRule(
        float $baseTotal,
        string $pricingMode,
        string $discountType,
        string $discountValue,
        string $manualPrice
    ): float {
        if ($pricingMode === 'manual' && is_numeric($manualPrice)) {
            return max(0, round((float) $manualPrice, 2));
        }

        $discount = 0.0;
        $value = is_numeric($discountValue) ? (float) $discountValue : 0.0;

        if ($discountType === 'percent') {
            $discount = $baseTotal * ($value / 100);
        } elseif ($discountType === 'fixed') {
            $discount = $value;
        }

        return max(0, round($baseTotal - $discount, 2));
    }

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