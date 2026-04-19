<?php
namespace app\model\Manager;

use PDO;

class ServiceModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = db();
    }

    public function getAllServices(): array
{
    return $this->db
        ->query("
            SELECT * FROM services 
            WHERE service_id NOT IN (
                SELECT service_id FROM packages WHERE service_id IS NOT NULL
            )
            ORDER BY name
        ")
        ->fetchAll(PDO::FETCH_ASSOC);
}

    public function getServiceTypes(): array
    {
        return $this->db
            ->query("SELECT * FROM service_types ORDER BY type_name")
            ->fetchAll();
    }

  public function getLastCode(): string
{
    $stmt = $this->db->prepare("
        SELECT service_code 
        FROM services 
        WHERE service_code LIKE 'SER%' 
        ORDER BY CAST(SUBSTRING(service_code, 4) AS UNSIGNED) DESC
        LIMIT 1
    ");
    $stmt->execute();
    $code = $stmt->fetchColumn();

    if (!$code) return 'SER001';

    $number = (int) substr($code, 3);
    return 'SER' . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
}

    public function create(array $data): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO services
            (service_code, name, description, base_duration_minutes, default_price, status, type_id)
            VALUES (:code, :name, :desc, :dur, :price, 'pending', :type)
        ");

        $stmt->execute([
            'code'  => $data['service_code'],
            'name'  => $data['name'],
            'desc'  => $data['description'],
            'dur'   => $data['duration'],
            'price' => $data['price'],
            'type'  => $data['type_id'],
        ]);
    }

public function getServiceById(int $id): array
{
    $stmt = $this->db->prepare("SELECT * FROM services WHERE service_id = ?");
    $stmt->execute([$id]);
    $service = $stmt->fetch(PDO::FETCH_ASSOC);

    return $service ?: [];
}

public function update(int $id, array $data): void
{
    $stmt = $this->db->prepare("
        UPDATE services
        SET name                  = :name,
            description           = :desc,
            base_duration_minutes = :dur,
            default_price         = :price,
            type_id               = :type,
            updated_at            = NOW()
        WHERE service_id = :id
    ");

    $stmt->execute([
        'name'  => $data['name'],
        'desc'  => $data['description'],
        'dur'   => $data['duration'],
        'price' => $data['price'],
        'type'  => $data['type_id'],
        'id'    => $id,
    ]);
}

public function updateStatus(int $id, string $status): void
{
    $stmt = $this->db->prepare("
        UPDATE services 
        SET status = ?, updated_at = NOW() 
        WHERE service_id = ?
    ");
    $stmt->execute([$status, $id]);
}

}


class PackageModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = db();
    }

    public function getAllPackages(): array
{
    $stmt = $this->db->prepare("
        SELECT package_id, package_code, name, description, total_duration_minutes, total_price, service_type_id, status
        FROM packages
        ORDER BY created_at DESC
    ");
    $stmt->execute();
    $packages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($packages as &$pkg) {

        // Get all services for this package
        $stmt2 = $this->db->prepare("
            SELECT s.service_id, s.service_code, s.name, s.description,
                   s.base_duration_minutes, s.default_price
            FROM services s
            INNER JOIN service_package_items ps ON s.service_id = ps.service_id
            WHERE ps.package_id = ?
        ");
        $stmt2->execute([$pkg['package_id']]);
        $pkg['services'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        if (!$pkg['services']) {
            $pkg['services'] = [];
        }

        // Get all service types for this package
        $stmt3 = $this->db->prepare("
            SELECT DISTINCT st.type_name
            FROM service_package_items ps
            JOIN services s ON ps.service_id = s.service_id
            JOIN service_types st ON s.type_id = st.type_id
            WHERE ps.package_id = ?
        ");
        $stmt3->execute([$pkg['package_id']]);
        $pkg['service_types'] = $stmt3->fetchAll(PDO::FETCH_COLUMN); // array of type names
    }

    return $packages;
}


public function create(array $data): void
{
    $this->db->beginTransaction();

    try {
        // 1. Insert into services table first
        $stmt = $this->db->prepare("
            INSERT INTO services
            (service_code, name, description, base_duration_minutes, default_price, status, type_id)
            VALUES (:code, :name, :desc, :dur, :price, 'active', :type)
        ");

        $stmt->execute([
            'code'  => $data['service_code'],
            'name'  => $data['name'],
            'desc'  => $data['description'],
            'dur'   => $data['duration'],
            'price' => $data['price'],
            'type'  => $data['type_id'],
        ]);

        $serviceId = $this->db->lastInsertId();

        // 2. Insert into packages table with the new service_id as FK
        $stmt2 = $this->db->prepare("
            INSERT INTO packages
            (service_id, package_code, name, description, status,
             total_duration_minutes, total_price, service_type_id)
            VALUES
            (:service_id, :code, :name, :desc, 'active', :dur, :price, :type)
        ");

        $stmt2->execute([
            'service_id' => $serviceId,
            'code'       => $data['service_code'],
            'name'       => $data['name'],
            'desc'       => $data['description'],
            'dur'        => $data['duration'],
            'price'      => $data['price'],
            'type'  => $data['type_id'],
        ]);

        $packageId = $this->db->lastInsertId();

        // 3. Insert package items
        $findService = $this->db->prepare("
            SELECT service_id FROM services WHERE service_code = ?
        ");

        $insertItem = $this->db->prepare("
            INSERT INTO service_package_items (package_id, service_id)
            VALUES (?, ?)
        ");

        foreach ($data['services'] as $serviceCode) {
            $findService->execute([$serviceCode]);
            $sid = $findService->fetchColumn();
            if ($sid) {
                $insertItem->execute([$packageId, $sid]);
            }
        }

        $this->db->commit();

    } catch (\Exception $e) {
        $this->db->rollBack();
        throw $e;
    }
}

public function getPackageById(int $id): array
{
    $stmt = $this->db->prepare("
        SELECT package_id, package_code, name, description,
               total_duration_minutes, total_price, service_type_id
        FROM packages
        WHERE package_id = :id
        LIMIT 1
    ");
    $stmt->execute(['id' => $id]);
    $package = $stmt->fetch(\PDO::FETCH_ASSOC);

    if (!$package) return [];

    // Get included services
    $stmt2 = $this->db->prepare("
        SELECT s.service_code, s.name
        FROM services s
        INNER JOIN service_package_items ps ON s.service_id = ps.service_id
        WHERE ps.package_id = :id
    ");
    $stmt2->execute(['id' => $id]);
    $package['services'] = $stmt2->fetchAll(\PDO::FETCH_ASSOC);

    return $package;
}


public function update(int $id, array $data): void
{
    $this->db->beginTransaction();

    try {
        // 1. Get the service_id FK from this package
        $stmt = $this->db->prepare("
            SELECT service_id FROM packages WHERE package_id = ?
        ");
        $stmt->execute([$id]);
        $serviceId = $stmt->fetchColumn();

        // 2. Update the parent service row
        if ($serviceId) {
            $stmt2 = $this->db->prepare("
                UPDATE services
                SET name                  = :name,
                    description           = :desc,
                    base_duration_minutes = :dur,
                    default_price         = :price,
                    type_id               = :type,
                    updated_at            = NOW()
                WHERE service_id = :id
            ");

            $stmt2->execute([
                'name'  => $data['name'],
                'desc'  => $data['description'],
                'dur'   => $data['total_duration'],
                'price' => $data['total_price'],
                'type'  => $data['type_id'],
                'id'    => $serviceId,
            ]);
        }

        // 3. Update the package row
        $stmt3 = $this->db->prepare("
            UPDATE packages
            SET name                   = :name,
                description            = :desc,
                total_duration_minutes = :dur,
                total_price            = :price,
                service_type_id        = :type
            WHERE package_id = :id
        ");

        $stmt3->execute([
            'name'  => $data['name'],
            'desc'  => $data['description'],
            'dur'   => $data['total_duration'],
            'price' => $data['total_price'],
            'type'  => $data['type_id'],
            'id'    => $id,
        ]);

        // 4. Delete and re-insert package items
        $this->db->prepare("
            DELETE FROM service_package_items WHERE package_id = ?
        ")->execute([$id]);

        $findService = $this->db->prepare("
            SELECT service_id FROM services WHERE service_code = ?
        ");

        $insertItem = $this->db->prepare("
            INSERT INTO service_package_items (package_id, service_id) VALUES (?, ?)
        ");

        foreach ($data['services'] as $serviceCode) {
            $findService->execute([$serviceCode]);
            $sid = $findService->fetchColumn();
            if ($sid) {
                $insertItem->execute([$id, $sid]);
            }
        }

        $this->db->commit();

    } catch (\Exception $e) {
        $this->db->rollBack();
        throw $e;
    }
}

public function updateStatus(int $id, string $status): void
{
    $this->db->beginTransaction();

    try {
        // Get the service_id FK
        $stmt = $this->db->prepare("
            SELECT service_id FROM packages WHERE package_id = ?
        ");
        $stmt->execute([$id]);
        $serviceId = $stmt->fetchColumn();

        // Update both tables
        if ($serviceId) {
            $this->db->prepare("
                UPDATE services SET status = ?, updated_at = NOW() WHERE service_id = ?
            ")->execute([$status, $serviceId]);
        }

        $this->db->prepare("
            UPDATE packages SET status = ? WHERE package_id = ?
        ")->execute([$status, $id]);

        $this->db->commit();

    } catch (\Exception $e) {
        $this->db->rollBack();
        throw $e;
    }
}

}



