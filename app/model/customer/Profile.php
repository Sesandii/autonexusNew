<?php
// app/model/customer/Profile.php
declare(strict_types=1);

namespace app\model\customer;

use PDO;

class Profile
{
    private PDO $pdo;
    public function __construct() { $this->pdo = db(); }

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

     /* NEW: generate next VEH code like VEH001 */
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

    // update with more fields
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

        $sql = "SELECT vehicle_id, license_plate, make, model, year, color, status
                  FROM vehicles
                 WHERE customer_id = :cid
              ORDER BY license_plate";
        $st = $this->pdo->prepare($sql);
        $st->execute(['cid' => $cid]);
        return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getVehicleByIdForUser(int $userId, int $vehicleId): ?array
    {
        $cid = $this->customerIdByUserId($userId);
        if (!$cid) return null;
        $sql = "SELECT vehicle_id, license_plate, make, model, year, color, status
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

    public function saveVehicle(int $userId, array $data): bool
    {
        $cid = $this->customerIdByUserId($userId);
        if (!$cid) return false;

        $vehId = isset($data['vehicle_id']) && $data['vehicle_id'] !== '' ? (int)$data['vehicle_id'] : null;

        if ($vehId) {
            if (!$this->vehicleExistsForCustomer($cid, $vehId)) {
                return false;
            }

            $sql = "UPDATE vehicles
                       SET license_plate = :plate,
                           make          = :make,
                           model         = :model,
                           year          = :year,
                           color         = :color
                     WHERE vehicle_id    = :vid
                       AND customer_id   = :cid";
            $st = $this->pdo->prepare($sql);
            $ok = $st->execute([
                'plate' => $data['license_plate'] ?? '',
                'make'  => $data['make'] ?? '',
                'model' => $data['model'] ?? '',
                'year'  => (int)($data['year'] ?? 0),
                'color' => $data['color'] ?? '',
                'vid'   => $vehId,
                'cid'   => $cid,
            ]);
            return $ok;
        } else {
            // INSERT must include vehicle_code
            $code = $this->nextVehicleCode();
            $sql = "INSERT INTO vehicles (vehicle_code, license_plate, make, model, year, color, service_interval_km, customer_id)
                    VALUES (:code, :plate, :make, :model, :year, :color, :service_interval_km, :cid)";
            $st = $this->pdo->prepare($sql);
            return $st->execute([
                'code'  => $code,
                'plate' => $data['license_plate'] ?? '',
                'make'  => $data['make'] ?? '',
                'model' => $data['model'] ?? '',
                'year'  => (int)($data['year'] ?? 0),
                'color' => $data['color'] ?? '',
                'service_interval_km' => (int)($data['service_interval_km'] ?? 5000),
                'cid'   => $cid,
            ]);
        }
    }

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
