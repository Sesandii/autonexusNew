//profle controller - vehicle image

<?php
declare(strict_types=1);

namespace app\controllers\customer;

use app\core\Controller;
use app\model\customer\Profile;

/**
 * Handles customer profile and vehicle management actions.
 */
class ProfileController extends Controller
{
    /**
     * Show profile overview with owned vehicles.
     */
    public function index(): void
    {
        $this->requireCustomer();

        $userId   = $this->userId();        // uses parent Controller::userId()
        $model    = new Profile();
        $profile  = $model->getProfile($userId);
        $vehicles = $model->getVehicles($userId);

        $this->view('customer/profile/index', [
            'title'    => 'My Profile',
            'profile'  => $profile,
            'vehicles' => $vehicles,
            'flash'    => $_SESSION['flash'] ?? null,
        ]);
        unset($_SESSION['flash']);
    }

    /* ---------- Edit Profile (HTML form) ---------- */

    /**
     * Render profile edit form.
     */
    public function editForm(): void
    {
        $this->requireCustomer();

        $userId  = $this->userId();
        $model   = new Profile();
        $profile = $model->getProfile($userId);

        $this->view('customer/profile/edit', [
            'title'   => 'Edit Profile',
            'profile' => $profile,
            'flash'   => $_SESSION['flash'] ?? null,
        ]);
        unset($_SESSION['flash']);
    }

    /**
     * Persist profile edits including optional profile picture upload.
     */
    public function updateProfile(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo "Method Not Allowed"; return; }
        $this->requireCustomer();

        $userId = $this->userId();

        $model = new Profile();
        $current = $model->getProfile($userId);

        $first = trim($_POST['first_name'] ?? '');
        $last  = trim($_POST['last_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $alt   = trim($_POST['alt_phone'] ?? ($current['alt_phone'] ?? ''));
        $addr  = trim($_POST['street_address'] ?? '');
        $city  = trim($_POST['city'] ?? '');
        $state = trim($_POST['state'] ?? '');

        $profilePicturePath = null;
        if (!empty($_FILES['profile_picture']['name']) && ($_FILES['profile_picture']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            $tmpName = (string)($_FILES['profile_picture']['tmp_name'] ?? '');
            $fileName = (string)($_FILES['profile_picture']['name'] ?? '');
            $fileType = (string)($_FILES['profile_picture']['type'] ?? '');

            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($fileType, $allowedTypes, true)) {
                $_SESSION['flash'] = 'Profile picture must be a JPG, PNG, GIF, or WEBP image.';
                header('Location: ' . rtrim(BASE_URL,'/') . '/customer/profile/edit');
                exit;
            }

            $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
                $_SESSION['flash'] = 'Profile picture must be a JPG, PNG, GIF, or WEBP image.';
                header('Location: ' . rtrim(BASE_URL,'/') . '/customer/profile/edit');
                exit;
            }

            $uploadDir = dirname(__DIR__, 3) . '/public/assets/img/profile_pictures/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $newFileName = 'profile_' . $userId . '_' . time() . '.' . $ext;
            $targetPath  = $uploadDir . $newFileName;

            if (!move_uploaded_file($tmpName, $targetPath)) {
                $_SESSION['flash'] = 'Failed to upload profile picture.';
                header('Location: ' . rtrim(BASE_URL,'/') . '/customer/profile/edit');
                exit;
            }

            $profilePicturePath = 'assets/img/profile_pictures/' . $newFileName;
        }

        $ok = $model->updateProfileFull(
            $userId,
            $first,
            $last,
            $phone,
            $alt,
            $addr,
            $city,
            $state,
            $profilePicturePath
        );

        $_SESSION['flash'] = $ok ? 'Profile updated.' : 'Failed to update profile.';
        header('Location: ' . rtrim(BASE_URL,'/') . '/customer/profile');
        exit;
    }

    /* ---------- Vehicle add/edit (HTML forms) ---------- */

    /**
     * Render vehicle create/edit form for the current customer.
     */
    public function vehicleForm(): void
    {
        $this->requireCustomer();

        $userId = $this->userId();
        $vehId  = (int)($_GET['id'] ?? 0);

        $model = new Profile();
        $vehicle = $vehId ? $model->getVehicleByIdForUser($userId, $vehId) : null;

        if ($vehId > 0 && $vehicle === null) {
            $_SESSION['flash'] = 'Vehicle not found or access denied.';
            header('Location: ' . rtrim(BASE_URL,'/') . '/customer/profile');
            exit;
        }

        $this->view('customer/profile/vehicle_form', [
            'title'   => $vehId ? 'Edit Vehicle' : 'Add Vehicle',
            'vehicle' => $vehicle,
            'errors'  => [],
            'flash'   => $_SESSION['flash'] ?? null,
        ]);
        unset($_SESSION['flash']);
    }

    /**
     * Create or update a customer-owned vehicle.
     */
    public function saveVehicle(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo "Method Not Allowed"; return; }
        $this->requireCustomer();

        $userId = $this->userId();
        $vehicleId = isset($_POST['vehicle_id']) && $_POST['vehicle_id'] !== '' ? (int)$_POST['vehicle_id'] : null;
        $currentYear = (int)date('Y');

        $licensePlate = strtoupper(trim((string)($_POST['license_plate'] ?? '')));
        $make = trim((string)($_POST['make'] ?? ''));
        $modelName = trim((string)($_POST['model'] ?? ''));
        $yearRaw = trim((string)($_POST['year'] ?? ''));
        $color = trim((string)($_POST['color'] ?? ''));
        $vehicleImagePath = null;

        $errors = [];

        if ($licensePlate === '') {
            $errors['license_plate'] = 'License plate is required.';
        }

        if ($make === '') {
            $errors['make'] = 'Brand is required.';
        }

        if ($modelName === '') {
            $errors['model'] = 'Model is required.';
        }

        if ($yearRaw === '') {
            $errors['year'] = 'Year is required.';
        } elseif (!ctype_digit($yearRaw)) {
            $errors['year'] = 'Year must contain only numbers.';
        } else {
            $yearInt = (int)$yearRaw;
            if ($yearInt < 1950 || $yearInt > ($currentYear + 1)) {
                $errors['year'] = 'Year must be between 1950 and ' . ($currentYear + 1) . '.';
            }
        }

        $model = new Profile();
        if ($licensePlate !== '' && $model->licensePlateExists($licensePlate, $vehicleId)) {
            $errors['license_plate'] = 'This license plate is already registered.';
        }

        if (!empty($_FILES['vehicle_image']['name'])) {
            $uploadError = (int)($_FILES['vehicle_image']['error'] ?? UPLOAD_ERR_NO_FILE);
            if ($uploadError !== UPLOAD_ERR_OK) {
                $errors['vehicle_image'] = 'Failed to upload vehicle image.';
            } else {
                $tmpName = (string)($_FILES['vehicle_image']['tmp_name'] ?? '');
                $fileName = (string)($_FILES['vehicle_image']['name'] ?? '');
                $fileType = (string)($_FILES['vehicle_image']['type'] ?? '');

                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                if (!in_array($fileType, $allowedTypes, true) || !in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
                    $errors['vehicle_image'] = 'Vehicle image must be a JPG, PNG, GIF, or WEBP image.';
                } else {
                    $uploadDir = dirname(__DIR__, 3) . '/public/assets/img/vehicle_pictures/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    $vehicleRef = $vehicleId !== null ? $vehicleId : ('new_' . $userId);
                    $newFileName = 'vehicle_' . $vehicleRef . '_' . time() . '.' . $ext;
                    $targetPath = $uploadDir . $newFileName;

                    if (!move_uploaded_file($tmpName, $targetPath)) {
                        $errors['vehicle_image'] = 'Failed to save vehicle image.';
                    } else {
                        $vehicleImagePath = 'assets/img/vehicle_pictures/' . $newFileName;
                    }
                }
            }
        }

        if (!empty($errors)) {
            $this->view('customer/profile/vehicle_form', [
                'title'   => $vehicleId ? 'Edit Vehicle' : 'Add Vehicle',
                'vehicle' => [
                    'vehicle_id'    => $vehicleId,
                    'license_plate' => $licensePlate,
                    'make'          => $make,
                    'model'         => $modelName,
                    'year'          => $yearRaw,
                    'color'         => $color,
                    'vehicle_image' => $vehicleImagePath,
                ],
                'errors'  => $errors,
                'flash'   => null,
            ]);
            return;
        }

        $data = [
            'vehicle_id'    => $vehicleId,
            'license_plate' => $licensePlate,
            'make'          => $make,
            'model'         => $modelName,
            'year'          => (int)$yearRaw,
            'color'         => $color,
            'vehicle_image' => $vehicleImagePath,
        ];

        $ok = $model->saveVehicle($userId, $data);

        if ($ok) {
            $_SESSION['flash'] = 'Vehicle saved.';
        } elseif ($vehicleId !== null) {
            $_SESSION['flash'] = 'Vehicle not found or access denied.';
        } else {
            $_SESSION['flash'] = 'Failed to save vehicle.';
        }
        header('Location: ' . rtrim(BASE_URL,'/') . '/customer/profile');
        exit;
    }

    /**
     * Delete a customer vehicle when there is no appointment history.
     */
    public function deleteVehicle(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo "Method Not Allowed"; return; }
    $this->requireCustomer();

    $userId = $this->userId();
    $id = (int)($_POST['vehicle_id'] ?? 0);

    $model = new Profile();

    $vehicle = $model->getVehicleByIdForUser($userId, $id);
    if (!$vehicle) {
        $_SESSION['flash'] = 'Vehicle not found or access denied.';
        header('Location: ' . rtrim(BASE_URL,'/') . '/customer/profile');
        exit;
    }

    $ok = $model->deleteVehicleOwnedBy($userId, $id);

    $_SESSION['flash'] = $ok
      ? 'Vehicle removed.'
            : 'Cannot remove this vehicle because it has appointment history. Use "Mark as Sold" to keep your records.';

    header('Location: ' . rtrim(BASE_URL,'/') . '/customer/profile');
    exit;
}

        /**
         * Mark a vehicle as sold while preserving service history.
         */
        public function sellVehicle(): void
        {
                if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo "Method Not Allowed"; return; }
                $this->requireCustomer();

                $userId = $this->userId();
                $id = (int)($_POST['vehicle_id'] ?? 0);

                $model = new Profile();
                $result = $model->markVehicleSold($userId, $id);

                $_SESSION['flash'] = match ($result) {
                        'sold' => 'Vehicle marked as sold. Past service history is preserved.',
                        'already_sold' => 'This vehicle is already marked as sold.',
                        'has_active_appointments' => 'Cannot mark as sold while active appointments exist. Cancel or complete them first.',
                    'schema_missing_sold_status' => 'Unable to mark as sold because the database schema does not allow the sold status.',
                        'not_found' => 'Vehicle not found or access denied.',
                        default => 'Failed to mark vehicle as sold.',
                };

                header('Location: ' . rtrim(BASE_URL,'/') . '/customer/profile');
                exit;
        }

}

//profile .php - vehicle image display

<?php
// app/model/customer/Profile.php
declare(strict_types=1);

namespace app\model\customer;

use PDO;

/**
 * Data access for customer profile details and vehicle ownership operations.
 */
class Profile
{
    private PDO $pdo;
    public function __construct() { $this->pdo = db(); }

    private function hasVehicleImageColumn(): bool
    {
        $cols = $this->pdo->query("SHOW COLUMNS FROM vehicles")->fetchAll(PDO::FETCH_COLUMN, 0);
        return in_array('vehicle_image', $cols, true);
    }

    /**
     * Ensure vehicles.status enum supports the 'sold' value.
     */
    private function ensureVehicleSoldStatusSupported(): bool
    {
        $stmt = $this->pdo->query("SHOW COLUMNS FROM vehicles LIKE 'status'");
        $col = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$col) {
            return false;
        }

        $type = (string)($col['Type'] ?? '');
        if ($type === '' || stripos($type, 'enum(') !== 0) {
            return false;
        }

        preg_match_all("/'([^']*)'/", $type, $matches);
        $values = $matches[1] ?? [];
        if (in_array('sold', $values, true)) {
            return true;
        }

        $values[] = 'sold';
        $quoted = array_map(fn(string $v): string => $this->pdo->quote($v), $values);
        $enumSql = implode(',', $quoted);

        // Keep default as available to preserve existing inserts/behavior.
        $sql = "ALTER TABLE vehicles MODIFY COLUMN status ENUM($enumSql) NOT NULL DEFAULT 'available'";
        $ok = $this->pdo->exec($sql);

        return $ok !== false;
    }

    /**
     * Generate next vehicle code in the format VEH001.
     */
    private function nextVehicleCode(): string
    {
        $sql = "SELECT MAX(CAST(SUBSTRING(vehicle_code,4) AS UNSIGNED)) AS maxn
                  FROM vehicles
                 WHERE vehicle_code LIKE 'VEH%'";
        $st = $this->pdo->query($sql);
        $n  = (int)($st->fetchColumn() ?: 0);
        return 'VEH' . str_pad((string)($n + 1), 3, '0', STR_PAD_LEFT);
    }

    private function customerIdByUserId(int $userId): ?int
    {
        $sql = "SELECT customer_id FROM customers WHERE user_id = :uid LIMIT 1";
        $st  = $this->pdo->prepare($sql);
        $st->execute(['uid' => $userId]);
        $cid = $st->fetchColumn();
        return $cid !== false ? (int)$cid : null;
    }

    public function getProfile(int $userId): array
    {
        // Your users schema
        $sql = "SELECT user_id, username, first_name, last_name, email, phone, alt_phone,
                       street_address, city, state, profile_picture, role, status, created_at
                  FROM users
                 WHERE user_id = :uid
                 LIMIT 1";
        $st = $this->pdo->prepare($sql);
        $st->execute(['uid' => $userId]);
        return $st->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Update profile core fields and optional profile image path.
     */
    public function updateProfileFull(
        int $userId,
        string $first,
        string $last,
        string $phone,
        string $alt,
        string $addr,
        string $city,
        string $state,
        ?string $profilePicture = null
    ): bool {
        $sets = [
            'first_name     = :fn',
            'last_name      = :ln',
            'phone          = :ph',
            'alt_phone      = :alt',
            'street_address = :addr',
            'city           = :city',
            'state          = :state',
        ];

        if ($profilePicture !== null && $profilePicture !== '') {
            $sets[] = 'profile_picture = :profile_picture';
        }

        $sql = "UPDATE users
                   SET " . implode(",\n                       ", $sets) . "
                 WHERE user_id        = :uid";
        $st = $this->pdo->prepare($sql);
        $params = [
            'fn'   => $first,
            'ln'   => $last,
            'ph'   => $phone,
            'alt'  => $alt,
            'addr' => $addr,
            'city' => $city,
            'state'=> $state,
            'uid'  => $userId,
        ];

        if ($profilePicture !== null && $profilePicture !== '') {
            $params['profile_picture'] = $profilePicture;
        }

        return $st->execute($params);
    }

    public function getVehicles(int $userId): array
    {
        $cid = $this->customerIdByUserId($userId);
        if (!$cid) return [];

        $hasVehicleImage = $this->hasVehicleImageColumn();

        $selectCols = ['vehicle_id', 'license_plate', 'make', 'model', 'year', 'color', 'status'];
        if ($hasVehicleImage) {
            $selectCols[] = 'vehicle_image';
        }

        $sql = "SELECT " . implode(', ', $selectCols) . "
                  FROM vehicles
                 WHERE customer_id = :cid
              ORDER BY license_plate";
        $st = $this->pdo->prepare($sql);
        $st->execute(['cid' => $cid]);
        return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Get one vehicle only if it belongs to the given customer user.
     */
    public function getVehicleByIdForUser(int $userId, int $vehicleId): ?array
    {
        $cid = $this->customerIdByUserId($userId);
        if (!$cid) return null;

        $hasVehicleImage = $this->hasVehicleImageColumn();
        $selectCols = ['vehicle_id', 'license_plate', 'make', 'model', 'year', 'color', 'status'];
        if ($hasVehicleImage) {
            $selectCols[] = 'vehicle_image';
        }

        $sql = "SELECT " . implode(', ', $selectCols) . "
                  FROM vehicles
                 WHERE vehicle_id = :vid AND customer_id = :cid
                 LIMIT 1";
        $st = $this->pdo->prepare($sql);
        $st->execute(['vid' => $vehicleId, 'cid' => $cid]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    private function vehicleExistsForCustomer(int $customerId, int $vehicleId): bool
    {
        $st = $this->pdo->prepare(
            "SELECT 1
               FROM vehicles
              WHERE vehicle_id = :vid
                AND customer_id = :cid
              LIMIT 1"
        );
        $st->execute(['vid' => $vehicleId, 'cid' => $customerId]);
        return (bool)$st->fetchColumn();
    }

    /**
     * Insert a new vehicle or update an existing customer-owned vehicle.
     */
    public function saveVehicle(int $userId, array $data): bool
    {
        $cid = $this->customerIdByUserId($userId);
        if (!$cid) return false;

        $vehId = isset($data['vehicle_id']) && $data['vehicle_id'] !== '' ? (int)$data['vehicle_id'] : null;
        $hasVehicleImage = $this->hasVehicleImageColumn();
        $vehicleImage = $data['vehicle_image'] ?? null;

        if ($vehId) {
            if (!$this->vehicleExistsForCustomer($cid, $vehId)) {
                return false;
            }

            $sets = [
                'license_plate = :plate',
                'make          = :make',
                'model         = :model',
                'year          = :year',
                'color         = :color',
            ];

            if ($hasVehicleImage && is_string($vehicleImage) && $vehicleImage !== '') {
                $sets[] = 'vehicle_image = :vehicle_image';
            }

            $sql = "UPDATE vehicles
                       SET " . implode(",\n                           ", $sets) . "
                     WHERE vehicle_id    = :vid
                       AND customer_id   = :cid";
            $st = $this->pdo->prepare($sql);

            $params = [
                'plate' => $data['license_plate'] ?? '',
                'make'  => $data['make'] ?? '',
                'model' => $data['model'] ?? '',
                'year'  => (int)($data['year'] ?? 0),
                'color' => $data['color'] ?? '',
                'vid'   => $vehId,
                'cid'   => $cid,
            ];

            if ($hasVehicleImage && is_string($vehicleImage) && $vehicleImage !== '') {
                $params['vehicle_image'] = $vehicleImage;
            }

            $ok = $st->execute($params);
            return $ok;
        } else {
            // INSERT must include vehicle_code
            $code = $this->nextVehicleCode();

            $columns = ['vehicle_code', 'license_plate', 'make', 'model', 'year', 'color', 'service_interval_km', 'customer_id'];
            $values = [':code', ':plate', ':make', ':model', ':year', ':color', ':service_interval_km', ':cid'];

            if ($hasVehicleImage && is_string($vehicleImage) && $vehicleImage !== '') {
                $columns[] = 'vehicle_image';
                $values[] = ':vehicle_image';
            }

            $sql = "INSERT INTO vehicles (" . implode(', ', $columns) . ")
                    VALUES (" . implode(', ', $values) . ")";
            $st = $this->pdo->prepare($sql);

            $params = [
                'code'  => $code,
                'plate' => $data['license_plate'] ?? '',
                'make'  => $data['make'] ?? '',
                'model' => $data['model'] ?? '',
                'year'  => (int)($data['year'] ?? 0),
                'color' => $data['color'] ?? '',
                'service_interval_km' => (int)($data['service_interval_km'] ?? 5000),
                'cid'   => $cid,
            ];

            if ($hasVehicleImage && is_string($vehicleImage) && $vehicleImage !== '') {
                $params['vehicle_image'] = $vehicleImage;
            }

            return $st->execute($params);
        }
    }

    /**
     * Check whether a license plate exists (optionally excluding one vehicle id).
     */
    public function licensePlateExists(string $licensePlate, ?int $excludeVehicleId = null): bool
    {
        $sql = "SELECT COUNT(*)
                  FROM vehicles
                 WHERE UPPER(license_plate) = UPPER(:plate)";

        $params = ['plate' => $licensePlate];
        if ($excludeVehicleId !== null && $excludeVehicleId > 0) {
            $sql .= " AND vehicle_id <> :vid";
            $params['vid'] = $excludeVehicleId;
        }

        $st = $this->pdo->prepare($sql);
        $st->execute($params);

        return (int)$st->fetchColumn() > 0;
    }

    /**
     * Delete a vehicle only when it belongs to the user and has no appointments.
     */
    public function deleteVehicleOwnedBy(int $userId, int $vehicleId): bool
    {
        $cid = $this->customerIdByUserId($userId);
        if (!$cid || $vehicleId <= 0) {
            return false;
        }

        if (!$this->vehicleExistsForCustomer($cid, $vehicleId)) {
            return false;
        }

        // Block delete if any appointment references this vehicle
        $chk = $this->pdo->prepare(
            "SELECT COUNT(*)
               FROM appointments
              WHERE vehicle_id = :vid
                AND customer_id = :cid"
        );
        $chk->execute(['vid' => $vehicleId, 'cid' => $cid]);

        if ((int)$chk->fetchColumn() > 0) {
            return false; // caller will set a friendly flash message
        }

        $st = $this->pdo->prepare(
            "DELETE FROM vehicles WHERE vehicle_id = :vid AND customer_id = :cid"
        );

        $ok = $st->execute(['vid' => $vehicleId, 'cid' => $cid]);
        return $ok && $st->rowCount() > 0;
    }

    /**
     * Mark customer vehicle as sold after ownership and active-job checks.
     */
    public function markVehicleSold(int $userId, int $vehicleId): string
    {
        $cid = $this->customerIdByUserId($userId);
        if (!$cid || $vehicleId <= 0) {
            return 'not_found';
        }

        $st = $this->pdo->prepare(
            "SELECT status
               FROM vehicles
              WHERE vehicle_id = :vid
                AND customer_id = :cid
              LIMIT 1"
        );
        $st->execute(['vid' => $vehicleId, 'cid' => $cid]);
        $status = $st->fetchColumn();

        if ($status === false) {
            return 'not_found';
        }

        if (strtolower(trim((string)$status)) === 'sold') {
            return 'already_sold';
        }

        $activeCheck = $this->pdo->prepare(
            "SELECT COUNT(*)
               FROM appointments
              WHERE vehicle_id = :vid
                AND customer_id = :cid
                AND COALESCE(LOWER(TRIM(status)), 'requested') NOT IN ('cancelled','completed')"
        );
        $activeCheck->execute(['vid' => $vehicleId, 'cid' => $cid]);

        if ((int)$activeCheck->fetchColumn() > 0) {
            return 'has_active_appointments';
        }

        if (!$this->ensureVehicleSoldStatusSupported()) {
            return 'schema_missing_sold_status';
        }

        $upd = $this->pdo->prepare(
            "UPDATE vehicles
                SET status = 'sold'
              WHERE vehicle_id = :vid
                AND customer_id = :cid"
        );

        $ok = $upd->execute(['vid' => $vehicleId, 'cid' => $cid]);
        if (!$ok) {
            return 'failed';
        }

        if ($upd->rowCount() > 0) {
            return 'sold';
        }

        // Some drivers can report 0 affected rows even when the value is unchanged.
        $verify = $this->pdo->prepare(
            "SELECT status
               FROM vehicles
              WHERE vehicle_id = :vid
                AND customer_id = :cid
              LIMIT 1"
        );
        $verify->execute(['vid' => $vehicleId, 'cid' => $cid]);
        $newStatus = $verify->fetchColumn();

        return strtolower(trim((string)$newStatus)) === 'sold' ? 'sold' : 'failed';
    }


}

// views/index/profil

<?php $base = rtrim(BASE_URL, '/'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title><?= htmlspecialchars($title ?? 'Profile') ?> - AutoNexus</title>
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/sidebar.css">
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/profile.css?v=20260403" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

  <?php include APP_ROOT . '/views/layouts/customer-sidebar.php'; ?>

  <div class="main-content customer-layout-main">

    <?php if (!empty($flash)): ?>
      <div class="flash"><?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>

    <?php
      $fullName = trim(($profile['first_name'] ?? '') . ' ' . ($profile['last_name'] ?? ''));
      $username = (string)($profile['username'] ?? '—');
      $email = (string)($profile['email'] ?? '—');
      $role = (string)($profile['role'] ?? 'Customer');
      $status = (string)($profile['status'] ?? '—');
      $phone = (string)($profile['phone'] ?? '—');
      $altPhone = (string)($profile['alt_phone'] ?? '—');
      $streetAddress = (string)($profile['street_address'] ?? '—');
      $city = (string)($profile['city'] ?? '—');
      $state = (string)($profile['state'] ?? '—');
      $avatar = !empty($profile['profile_picture'])
        ? $base . '/public/' . ltrim((string)$profile['profile_picture'], '/')
        : $base . '/public/assets/img/User.PNG';
    ?>

    <div class="profile-shell">
      <div class="profile-hero">
        <img class="profile-avatar" src="<?= $avatar ?>" alt="Profile photo">

        <div class="profile-hero-text">
          <h1><?= htmlspecialchars($fullName ?: 'Customer Profile') ?></h1>
          <p class="username">@<?= htmlspecialchars($username) ?></p>
          <div class="profile-badges">
            <span class="badge role"><i class="fa fa-user"></i> Customer</span>
            <span class="badge mail"><i class="fa fa-envelope"></i> <?= htmlspecialchars($email) ?></span>
          </div>

          <a class="edit-link" href="<?= $base ?>/customer/profile/edit">
            <i class="fa fa-pen"></i> Edit
          </a>
        </div>
      </div>

      <div class="profile-grid">
        <section class="panel info-panel">
          <h2>Profile Information</h2>

          <div class="info-table">
            <div class="info-row"><span>User ID</span><span><?= htmlspecialchars((string)($profile['user_id'] ?? '—')) ?></span></div>
            <div class="info-row"><span>Full Name</span><span><?= htmlspecialchars($fullName ?: '—') ?></span></div>
            <div class="info-row"><span>Phone</span><span><?= htmlspecialchars($phone) ?></span></div>
            <div class="info-row"><span>Alt Phone</span><span><?= htmlspecialchars($altPhone) ?></span></div>
            <div class="info-row"><span>Street Address</span><span><?= htmlspecialchars($streetAddress) ?></span></div>
            <div class="info-row"><span>City</span><span><?= htmlspecialchars($city) ?></span></div>
            <div class="info-row"><span>State</span><span><?= htmlspecialchars($state) ?></span></div>
            <div class="info-row"><span>Role</span><span><?= htmlspecialchars($role) ?></span></div>
            <div class="info-row"><span>Status</span><span><?= htmlspecialchars($status) ?></span></div>
          </div>
        </section>

        <section class="panel vehicle-panel">
          <div class="panel-head">
            <h2>My Vehicles</h2>
            <button type="button" class="btn yellow" id="addVehicleBtn">
              <i class="fa fa-plus"></i> Add Vehicle
            </button>
          </div>

          <div class="vehicles-container">
            <?php foreach ($vehicles as $v): ?>
              <?php $vehicleStatus = strtolower((string)($v['status'] ?? 'available')); ?>
              <?php
                $vehicleImage = !empty($v['vehicle_image'])
                  ? $base . '/public/' . ltrim((string)$v['vehicle_image'], '/')
                  : $base . '/public/assets/img/User.PNG';
              ?>
              <article class="vehicle-card">
                <img class="vehicle-image" src="<?= htmlspecialchars($vehicleImage) ?>" alt="Vehicle image">
                <h3><?= htmlspecialchars(trim(($v['make'] ?? '') . ' ' . ($v['model'] ?? ''))) ?: 'Vehicle' ?></h3>

                <div class="vehicle-meta">
                  <div><span>License Plate</span><strong><?= htmlspecialchars($v['license_plate'] ?? '—') ?></strong></div>
                  <div><span><?= htmlspecialchars($v['color'] ?? '—') ?></span></div>
                  <div><span><?= htmlspecialchars($v['year'] ?? '—') ?></span></div>
                  <div><span>Status</span><strong><?= htmlspecialchars(ucfirst($vehicleStatus)) ?></strong></div>
                </div>
                
                <div class="vehicle-actions">
                  <?php if ($vehicleStatus !== 'sold'): ?>
                    <a class="btn edit" href="<?= $base ?>/customer/profile/vehicle?id=<?= (int)$v['vehicle_id'] ?>">
                      <i class="fa fa-pen"></i> Edit
                    </a>

                    <form method="post" action="<?= $base ?>/customer/profile/vehicle/sell" onsubmit="return confirm('Mark this vehicle as sold? It will be hidden from future bookings but past history will remain.');">
                      <input type="hidden" name="vehicle_id" value="<?= (int)$v['vehicle_id'] ?>">
                      <button type="submit" class="btn yellow" aria-label="Mark vehicle <?= htmlspecialchars($v['license_plate'] ?? '') ?> as sold">
                        <i class="fa fa-tag"></i> Mark as Sold
                      </button>
                    </form>
                  <?php endif; ?>

                  <form method="post" action="<?= $base ?>/customer/profile/vehicle/delete" onsubmit="return confirm('Delete this vehicle? This action cannot be undone.');">
                    <input type="hidden" name="vehicle_id" value="<?= (int)$v['vehicle_id'] ?>">
                    <button type="submit" class="btn delete" aria-label="Delete vehicle <?= htmlspecialchars($v['license_plate'] ?? '') ?>">
                      <i class="fa fa-trash"></i> Delete
                    </button>
                  </form>
                </div>
              </article>
            <?php endforeach; ?>

            <?php if (empty($vehicles)): ?>
              <p class="notes">No vehicles registered yet. Use “Add Vehicle” to register your car with AutoNexus.</p>
            <?php endif; ?>
          </div>
        </section>
      </div>
    </div>
  </div>

  <?php include APP_ROOT . '/views/customer/profile/modals.php'; ?>
  <script src="<?= $base ?>/public/assets/js/customer/profile.js?v=20260415"></script>
</body>
</html>


app/views/customer/profile/vehicleform.php

<?php
$base = rtrim(BASE_URL, '/');
$errors = isset($errors) && is_array($errors) ? $errors : [];
$editing = isset($vehicle['vehicle_id']) && (int)$vehicle['vehicle_id'] > 0;
$currentYear = (int)date('Y');
$vehicleImageUrl = !empty($vehicle['vehicle_image'])
  ? $base . '/public/' . ltrim((string)$vehicle['vehicle_image'], '/')
  : $base . '/public/assets/img/User.PNG';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title><?= $editing ? 'Edit your Vehicle' : 'Add Vehicle' ?> - AutoNexus</title>
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/profile.css?v=20260404c" />
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/sidebar.css">
</head>
<body>

  <?php include APP_ROOT . '/views/layouts/customer-sidebar.php'; ?>

  <div class="main-content vehicle-main-content customer-layout-main">

    <?php if (!empty($flash)): ?>
      <div class="flash"><?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>

    <div class="section-card vehicle-section-card">
      <div class="section-header">
        <div>
          <h2><?= $editing ? 'Edit your Vehicle' : 'Add Vehicle' ?></h2>
          <p class="section-subtitle">
            <?= $editing 
              ? 'Update your vehicle details used for bookings and service history.'
              : 'Add a vehicle to quickly book services and track its history.' ?>
          </p>
        </div>
      </div>

      <form class="form-card vehicle-form-card" method="post" action="<?= $base ?>/customer/profile/vehicle" enctype="multipart/form-data" novalidate>
        <?php if ($editing): ?>
          <input type="hidden" name="vehicle_id" value="<?= (int)$vehicle['vehicle_id'] ?>">
        <?php endif; ?>

        <div class="grid vehicle-grid vehicle-row-single">
          <label>License Plate
            <input
              type="text"
              name="license_plate"
              value="<?= htmlspecialchars($vehicle['license_plate'] ?? '') ?>"
              class="<?= isset($errors['license_plate']) ? 'input-error' : '' ?>"
              required
              maxlength="20"
              autocomplete="off"
            >
            <?php if (!empty($errors['license_plate'])): ?>
              <small class="field-error"><?= htmlspecialchars($errors['license_plate']) ?></small>
            <?php endif; ?>
          </label>
        </div>

        <div class="grid vehicle-grid">
          <label>Brand
            <input type="text" name="make" value="<?= htmlspecialchars($vehicle['make'] ?? '') ?>" class="<?= isset($errors['make']) ? 'input-error' : '' ?>" required>
            <?php if (!empty($errors['make'])): ?>
              <small class="field-error"><?= htmlspecialchars($errors['make']) ?></small>
            <?php endif; ?>
          </label>

          <label>Model
            <input type="text" name="model" value="<?= htmlspecialchars($vehicle['model'] ?? '') ?>" class="<?= isset($errors['model']) ? 'input-error' : '' ?>" required>
            <?php if (!empty($errors['model'])): ?>
              <small class="field-error"><?= htmlspecialchars($errors['model']) ?></small>
            <?php endif; ?>
          </label>
        </div>

        <div class="grid vehicle-grid">
          <label>Year
            <input
              type="number"
              name="year"
              value="<?= htmlspecialchars((string)($vehicle['year'] ?? '')) ?>"
              min="1950"
              max="<?= $currentYear + 1 ?>"
              inputmode="numeric"
              class="<?= isset($errors['year']) ? 'input-error' : '' ?>"
              required
            >
            <?php if (!empty($errors['year'])): ?>
              <small class="field-error"><?= htmlspecialchars($errors['year']) ?></small>
            <?php endif; ?>
          </label>

          <label>Color
            <input type="text" name="color" value="<?= htmlspecialchars($vehicle['color'] ?? '') ?>" class="<?= isset($errors['color']) ? 'input-error' : '' ?>">
            <?php if (!empty($errors['color'])): ?>
              <small class="field-error"><?= htmlspecialchars($errors['color']) ?></small>
            <?php endif; ?>
          </label>
        </div>

        <div class="grid vehicle-grid">
          <label>Vehicle Image
            <input
              type="file"
              name="vehicle_image"
              accept="image/jpeg,image/png,image/gif,image/webp"
              class="<?= isset($errors['vehicle_image']) ? 'input-error' : '' ?>"
            >
            <?php if (!empty($errors['vehicle_image'])): ?>
              <small class="field-error"><?= htmlspecialchars($errors['vehicle_image']) ?></small>
            <?php endif; ?>
          </label>

          <label>Current Image
            <img class="vehicle-image-preview" src="<?= htmlspecialchars($vehicleImageUrl) ?>" alt="Vehicle image preview">
          </label>
        </div>

        <div class="actions vehicle-actions-clean">
          <a class="btn-ghost" href="<?= $base ?>/customer/profile">Cancel</a>
          <button type="submit" class="btn-primary">
            <?= $editing ? 'Update Vehicle' : 'Save Vehicle' ?>
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    (function () {
      var form = document.querySelector('.vehicle-form-card');
      var plateInput = document.querySelector('input[name="license_plate"]');
      var brandInput = document.querySelector('input[name="make"]');
      var modelInput = document.querySelector('input[name="model"]');
      var yearInput = document.querySelector('input[name="year"]');

      if (!form || !plateInput || !brandInput || !modelInput || !yearInput) return;

      var maxYear = <?= $currentYear + 1 ?>;

      function fieldByName(name) {
        return form.querySelector('[name="' + name + '"]');
      }

      function ensureErrorNode(input) {
        var existing = input.parentElement.querySelector('.field-error');
        if (existing) return existing;

        var node = document.createElement('small');
        node.className = 'field-error';
        input.parentElement.appendChild(node);
        return node;
      }

      function setError(name, message) {
        var input = fieldByName(name);
        if (!input) return;
        input.classList.add('input-error');
        var node = ensureErrorNode(input);
        node.textContent = message;
      }

      function clearError(name) {
        var input = fieldByName(name);
        if (!input) return;
        input.classList.remove('input-error');

        var node = input.parentElement.querySelector('.field-error');
        if (!node) return;

        if (node.hasAttribute('data-server') && node.textContent.trim() !== '') {
          return;
        }

        node.textContent = '';
      }

      function validateLicensePlate() {
        var value = plateInput.value.trim();
        if (value === '') {
          setError('license_plate', 'License plate is required.');
          return false;
        }
        clearError('license_plate');
        return true;
      }

      function validateBrand() {
        if (brandInput.value.trim() === '') {
          setError('make', 'Brand is required.');
          return false;
        }
        clearError('make');
        return true;
      }

      function validateModel() {
        if (modelInput.value.trim() === '') {
          setError('model', 'Model is required.');
          return false;
        }
        clearError('model');
        return true;
      }

      function validateYear() {
        var value = yearInput.value.trim();
        if (value === '') {
          setError('year', 'Year is required.');
          return false;
        }
        if (!/^\d+$/.test(value)) {
          setError('year', 'Year must contain only numbers.');
          return false;
        }

        var year = parseInt(value, 10);
        if (year < 1950 || year > maxYear) {
          setError('year', 'Year must be between 1950 and ' + maxYear + '.');
          return false;
        }

        clearError('year');
        return true;
      }

      function markServerErrors() {
        form.querySelectorAll('.field-error').forEach(function (node) {
          if (node.textContent.trim() !== '') {
            node.setAttribute('data-server', '1');
          }
        });
      }

      markServerErrors();

      function normalizePlate() {
        plateInput.value = plateInput.value.trim().toUpperCase();
      }

      plateInput.addEventListener('input', function () {
        plateInput.value = plateInput.value.toUpperCase();
        validateLicensePlate();
      });

      plateInput.addEventListener('blur', normalizePlate);
      plateInput.addEventListener('blur', validateLicensePlate);
      brandInput.addEventListener('blur', validateBrand);
      modelInput.addEventListener('blur', validateModel);
      yearInput.addEventListener('blur', validateYear);

      yearInput.addEventListener('input', function () {
        yearInput.value = yearInput.value.replace(/[^0-9]/g, '');
        validateYear();
      });

      form.addEventListener('submit', function (e) {
        normalizePlate();

        var ok = true;
        ok = validateLicensePlate() && ok;
        ok = validateBrand() && ok;
        ok = validateModel() && ok;
        ok = validateYear() && ok;

        if (!ok) {
          e.preventDefault();
        }
      });

      normalizePlate();
    })();
  </script>

</body>
</html>

