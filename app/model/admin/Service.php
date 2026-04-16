<?php
declare(strict_types=1);

namespace app\model\admin;

use PDO;

class Service
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = db();
    }

    public function nextCode(): string
    {
        $sql = "
            SELECT MAX(CAST(SUBSTRING(service_code, 4) AS UNSIGNED)) AS max_num
            FROM services
            WHERE service_code REGEXP '^SER[0-9]+$'
        ";
        $row = $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
        $next = (int)($row['max_num'] ?? 0) + 1;

        return 'SER' . str_pad((string)$next, 3, '0', STR_PAD_LEFT);
    }

    public function create(array $data): int
    {
        $cols = array_keys($data);
        $sql = "INSERT INTO services (" . implode(',', $cols) . ")
                VALUES (:" . implode(',:', $cols) . ")";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);

        return (int)$this->pdo->lastInsertId();
    }

    public function updateById(int $id, array $data): void
    {
        if (isset($data['service_code'])) {
            unset($data['service_code']);
        }

        if (empty($data)) {
            return;
        }

        $set = implode(', ', array_map(fn($k) => "$k = :$k", array_keys($data)));
        $sql = "UPDATE services SET {$set}, updated_at = NOW() WHERE service_id = :id";

        $params = $data;
        $params['id'] = $id;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
    }

    public function deleteById(int $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM services WHERE service_id = :id");
        $stmt->execute(['id' => $id]);
    }

    public function findById(int $id): ?array
    {
        $sql = "
            SELECT
                s.*,
                COALESCE(st.type_name, 'Uncategorized') AS type_name
            FROM services s
            LEFT JOIN service_types st ON st.type_id = s.type_id
            WHERE s.service_id = :id
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function allWithTypeAndBranches(): array
    {
        $sql = "
            SELECT
                s.service_id,
                s.service_code,
                s.name,
                s.description,
                s.base_duration_minutes,
                s.default_price,
                s.status,
                s.created_at,
                s.updated_at,
                st.type_id,
                COALESCE(st.type_name, 'Uncategorized') AS type_name,
                COUNT(DISTINCT bs.branch_id) AS branch_count,
                GROUP_CONCAT(
                    DISTINCT CONCAT(b.name, ' (', b.branch_code, ')')
                    ORDER BY b.name SEPARATOR ', '
                ) AS branches
            FROM services s
            LEFT JOIN service_types st ON st.type_id = s.type_id
            LEFT JOIN branch_services bs ON bs.service_id = s.service_id
            LEFT JOIN branches b ON b.branch_id = bs.branch_id
            GROUP BY s.service_id
            ORDER BY s.created_at DESC, s.name ASC
        ";

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function distinctTypesForTabs(): array
    {
        $sql = "
            SELECT st.type_id, st.type_name
            FROM service_types st
            JOIN services s ON s.type_id = st.type_id
            GROUP BY st.type_id, st.type_name
            ORDER BY st.type_name
        ";

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function allAtomicServices(): array
    {
        $packageTypeId = $this->findPackageTypeId();

        $sql = "
            SELECT
                s.service_id,
                s.service_code,
                s.name,
                s.description,
                s.base_duration_minutes,
                s.default_price,
                s.type_id,
                s.status,
                COALESCE(st.type_name, 'Uncategorized') AS type_name
            FROM services s
            LEFT JOIN service_types st ON st.type_id = s.type_id
            WHERE s.status = 'active'
              AND (s.type_id IS NULL OR s.type_id <> :package_type_id)
            ORDER BY s.name ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'package_type_id' => $packageTypeId ?? 0,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findPackageTypeId(): ?int
    {
        $stmt = $this->pdo->prepare("
            SELECT type_id
            FROM service_types
            WHERE LOWER(type_name) IN ('package', 'packages')
            LIMIT 1
        ");
        $stmt->execute();

        $value = $stmt->fetchColumn();
        return $value !== false ? (int)$value : null;
    }

    public function isPackageType(?int $typeId): bool
    {
        if (!$typeId) {
            return false;
        }

        $packageTypeId = $this->findPackageTypeId();
        return $packageTypeId !== null && (int)$typeId === $packageTypeId;
    }

    public function packageAnalytics(): array
    {
        $packageTypeId = $this->findPackageTypeId();
        if (!$packageTypeId) {
            return [];
        }

        $sql = "
            SELECT
                s.service_id,
                COUNT(a.appointment_id) AS usage_count,
                MAX(a.appointment_date) AS last_booked_date,
                COALESCE(SUM(
                    CASE
                        WHEN a.status IN ('confirmed', 'assigned', 'in_service', 'completed')
                        THEN s.default_price
                        ELSE 0
                    END
                ), 0) AS estimated_revenue
            FROM services s
            LEFT JOIN appointments a
                ON a.service_id = s.service_id
            WHERE s.type_id = :package_type_id
            GROUP BY s.service_id
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'package_type_id' => $packageTypeId,
        ]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $map = [];

        foreach ($rows as $row) {
            $map[(int)$row['service_id']] = [
                'usage_count' => (int)($row['usage_count'] ?? 0),
                'last_booked_date' => $row['last_booked_date'] ?? null,
                'estimated_revenue' => (float)($row['estimated_revenue'] ?? 0),
            ];
        }

        return $map;
    }

    public function packageSummary(int $serviceId): array
    {
        $packageId = $this->getPackageIdForService($serviceId);
        if (!$packageId) {
            return [
                'total_duration' => 0,
                'base_total' => 0.00,
            ];
        }

        $sql = "
            SELECT
                COALESCE(SUM(s.base_duration_minutes * spi.quantity), 0) AS total_duration,
                COALESCE(SUM(s.default_price * spi.quantity), 0) AS base_total
            FROM service_package_items spi
            INNER JOIN services s ON s.service_id = spi.service_id
            WHERE spi.package_id = :package_id
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['package_id' => $packageId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

        return [
            'total_duration' => (int)($row['total_duration'] ?? 0),
            'base_total' => (float)($row['base_total'] ?? 0),
        ];
    }

    public function nextPackageCode(): string
    {
        $sql = "
            SELECT MAX(CAST(SUBSTRING(package_code, 4) AS UNSIGNED)) AS max_num
            FROM packages
            WHERE package_code REGEXP '^PKG[0-9]+$'
        ";
        $row = $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
        $next = (int)($row['max_num'] ?? 0) + 1;

        return 'PKG' . str_pad((string)$next, 3, '0', STR_PAD_LEFT);
    }

    public function createPackageRecord(int $serviceId, array $data): int
    {
        $packageCode = $this->nextPackageCode();

        $sql = "
            INSERT INTO packages
            (
                service_id,
                package_code,
                name,
                description,
                total_duration_minutes,
                total_price,
                service_type_id,
                status,
                created_at
            )
            VALUES
            (
                :service_id,
                :package_code,
                :name,
                :description,
                :total_duration,
                :total_price,
                :service_type_id,
                :status,
                :created_at
            )
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'service_id' => $serviceId,
            'package_code' => $packageCode,
            'name' => $data['name'] ?? '',
            'description' => $data['description'] ?? '',
            'total_duration' => (int)($data['base_duration_minutes'] ?? 0),
            'total_price' => $data['default_price'] ?? 0,
            'service_type_id' => $data['type_id'] ?? 0,
            'status' => in_array(($data['status'] ?? 'active'), ['active', 'inactive'], true)
                ? $data['status']
                : 'active',
            'created_at' => $data['created_at'] ?? date('Y-m-d H:i:s'),
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function updatePackageRecord(int $packageId, int $serviceId, array $data): void
    {
        $sql = "
            UPDATE packages
            SET
                service_id = :service_id,
                name = :name,
                description = :description,
                total_duration_minutes = :total_duration,
                total_price = :total_price,
                service_type_id = :service_type_id,
                status = :status
            WHERE package_id = :package_id
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'package_id' => $packageId,
            'service_id' => $serviceId,
            'name' => $data['name'] ?? '',
            'description' => $data['description'] ?? '',
            'total_duration' => (int)($data['base_duration_minutes'] ?? 0),
            'total_price' => $data['default_price'] ?? 0,
            'service_type_id' => $data['type_id'] ?? 0,
            'status' => in_array(($data['status'] ?? 'active'), ['active', 'inactive'], true)
                ? $data['status']
                : 'active',
        ]);
    }

    public function deletePackageRecord(int $packageId): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM packages WHERE package_id = :package_id");
        $stmt->execute(['package_id' => $packageId]);
    }

    public function getPackageIdForService(int $serviceId): ?int
    {
        $stmt = $this->pdo->prepare("
            SELECT package_id
            FROM packages
            WHERE service_id = :service_id
            LIMIT 1
        ");
        $stmt->execute(['service_id' => $serviceId]);

        $value = $stmt->fetchColumn();
        return $value !== false ? (int)$value : null;
    }

    public function getPackageCodeForService(int $serviceId): ?string
    {
        $stmt = $this->pdo->prepare("
            SELECT package_code
            FROM packages
            WHERE service_id = :service_id
            LIMIT 1
        ");
        $stmt->execute(['service_id' => $serviceId]);

        $value = $stmt->fetchColumn();
        return $value !== false ? (string)$value : null;
    }
}