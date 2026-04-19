<?php
declare(strict_types=1);

namespace app\model\admin;

use PDO;

class PackageItem
{
    private PDO $pdo;

    // Initialize model dependencies and database access.
    public function __construct()
    {
        $this->pdo = db();
    }

    // Handle replaceItems operation.
    public function replaceItems(int $packageId, array $items): void
    {
        $del = $this->pdo->prepare("DELETE FROM service_package_items WHERE package_id = :package_id");
        $del->execute(['package_id' => $packageId]);

        if (empty($items)) {
            return;
        }

        $ins = $this->pdo->prepare("
            INSERT INTO service_package_items (package_id, service_id, quantity)
            VALUES (:package_id, :service_id, :quantity)
        ");

        foreach ($items as $row) {
            $serviceId = (int)($row['service_id'] ?? 0);
            $quantity = max(1, (int)($row['quantity'] ?? 1));

            if ($serviceId <= 0) {
                continue;
            }

            $ins->execute([
                'package_id' => $packageId,
                'service_id' => $serviceId,
                'quantity'   => $quantity,
            ]);
        }
    }

    // Handle itemsForPackage operation.
    public function itemsForPackage(int $packageId): array
    {
        $sql = "
            SELECT
                spi.id,
                spi.package_id,
                spi.service_id,
                spi.quantity,
                s.service_code,
                s.name,
                s.description,
                s.base_duration_minutes,
                s.default_price
            FROM service_package_items spi
            INNER JOIN services s ON s.service_id = spi.service_id
            WHERE spi.package_id = :package_id
            ORDER BY s.name ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['package_id' => $packageId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}