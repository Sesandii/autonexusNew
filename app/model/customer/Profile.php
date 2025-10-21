<?php
// app/model/customer/Profile.php
declare(strict_types=1);

namespace app\model\customer;

use PDO;

class Profile
{
    private PDO $pdo;
    public function __construct() { $this->pdo = db(); }

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
        // ONLY select columns that exist in your users schema
        $sql = "SELECT user_id, first_name, last_name, email, phone, alt_phone, street_address, city, state
                FROM users
                WHERE user_id = :uid
                LIMIT 1";
        $st = $this->pdo->prepare($sql);
        $st->execute(['uid' => $userId]);
        return $st->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public function updateProfile(int $userId, string $first, string $last, string $phone): bool
    {
        $sql = "UPDATE users
                   SET first_name = :fn,
                       last_name  = :ln,
                       phone      = :ph
                 WHERE user_id    = :uid";
        $st = $this->pdo->prepare($sql);
        return $st->execute([
            'fn'  => $first,
            'ln'  => $last,
            'ph'  => $phone,
            'uid' => $userId
        ]);
    }

    public function getVehicles(int $userId): array
    {
        $cid = $this->customerIdByUserId($userId);
        if (!$cid) return [];

        $sql = "SELECT vehicle_id, license_plate, make, model, year, color
                  FROM vehicles
                 WHERE customer_id = :cid
              ORDER BY license_plate";
        $st = $this->pdo->prepare($sql);
        $st->execute(['cid' => $cid]);
        return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function saveVehicle(int $userId, array $data): bool
    {
        $cid = $this->customerIdByUserId($userId);
        if (!$cid) return false;

        // normalize
        $vehId = isset($data['vehicle_id']) && $data['vehicle_id'] !== '' ? (int)$data['vehicle_id'] : null;

        if ($vehId) {
            // update
            $sql = "UPDATE vehicles
                       SET license_plate = :plate,
                           make          = :make,
                           model         = :model,
                           year          = :year,
                           color         = :color
                     WHERE vehicle_id    = :vid
                       AND customer_id   = :cid";
            $st = $this->pdo->prepare($sql);
            return $st->execute([
                'plate' => $data['license_plate'] ?? '',
                'make'  => $data['make'] ?? '',
                'model' => $data['model'] ?? '',
                'year'  => (int)($data['year'] ?? 0),
                'color' => $data['color'] ?? '',
                'vid'   => $vehId,
                'cid'   => $cid,
            ]);
        } else {
            // insert
            $sql = "INSERT INTO vehicles (license_plate, make, model, year, color, customer_id)
                    VALUES (:plate, :make, :model, :year, :color, :cid)";
            $st = $this->pdo->prepare($sql);
            return $st->execute([
                'plate' => $data['license_plate'] ?? '',
                'make'  => $data['make'] ?? '',
                'model' => $data['model'] ?? '',
                'year'  => (int)($data['year'] ?? 0),
                'color' => $data['color'] ?? '',
                'cid'   => $cid,
            ]);
        }
    }

    public function deleteVehicle(int $vehicleId): bool
    {
        $sql = "DELETE FROM vehicles WHERE vehicle_id = :vid";
        $st = $this->pdo->prepare($sql);
        return $st->execute(['vid' => $vehicleId]);
    }
}
