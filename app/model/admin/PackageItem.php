<?php
namespace app\model\admin;

use PDO;

class PackageItem
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = db();
    }

    public function replaceItems(int $packageId, array $serviceIdsWithQty): void
    {
        $del = $this->pdo->prepare("DELETE FROM service_package_items WHERE package_id = :package_id");
        $del->execute(['package_id' => $packageId]);

        if (empty($serviceIdsWithQty)) {
            return;
        }

        $ins = $this->pdo->prepare("
            INSERT INTO service_package_items (package_id, service_id, quantity)
            VALUES (:package_id, :service_id, :quantity)
        ");

        foreach ($serviceIdsWithQty as $row) {
            $serviceId = (int)($row['service_id'] ?? 0);
            $qty       = (int)($row['quantity'] ?? 1);

            if ($serviceId <= 0 || $qty <= 0) {
                continue;
            }

            $ins->execute([
                'package_id' => $packageId,
                'service_id' => $serviceId,
                'quantity'   => $qty,
            ]);
        }
    }

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
                s.default_price,
                st.type_name
            FROM service_package_items spi
            INNER JOIN services s ON s.service_id = spi.service_id
            LEFT JOIN service_types st ON st.type_id = s.type_id
            WHERE spi.package_id = :package_id
            ORDER BY s.name ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['package_id' => $packageId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function childIds(int $packageId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT service_id
            FROM service_package_items
            WHERE package_id = :package_id
        ");
        $stmt->execute(['package_id' => $packageId]);

        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN, 0));
    }

    public function packageTotalsFromItems(array $items): array
    {
        $duration = 0;
        $price    = 0.00;

        foreach ($items as $item) {
            $qty      = max(1, (int)($item['quantity'] ?? 1));
            $minutes  = (int)($item['base_duration_minutes'] ?? 0);
            $itemCost = (float)($item['default_price'] ?? 0);

            $duration += ($minutes * $qty);
            $price    += ($itemCost * $qty);
        }

        return [
            'duration' => $duration,
            'price'    => round($price, 2),
        ];
    }
}