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
        ->query("SELECT * FROM services ORDER BY name")
        ->fetchAll(PDO::FETCH_ASSOC);
}

    public function getServiceTypes(): array
    {
        return $this->db
            ->query("SELECT * FROM service_types ORDER BY type_name")
            ->fetchAll();
    }

    public function getLastServiceCode(): string
    {
        $code = $this->db
            ->query("SELECT service_code FROM services ORDER BY created_at DESC LIMIT 1")
            ->fetchColumn();

        return $code ?: 'SRV001';
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



    public function getLastPackageCode(): string
    {
        $code = $this->db
            ->query("SELECT package_code FROM packages ORDER BY created_at DESC LIMIT 1")
            ->fetchColumn();

        return $code ?: 'PKG001';
    }

public function create(array $data): void
{
    $this->db->beginTransaction();

    try {

        // 1️⃣ Insert Package
        $stmt = $this->db->prepare("
            INSERT INTO packages
            (package_code, name, description, status,
             total_duration_minutes, total_price, service_type_id)
            VALUES
            (:code, :name, :desc, 'active', :dur, :price, :type)
        ");

        $stmt->execute([
            'code' => $data['package_code'],
            'name' => $data['name'],
            'desc' => $data['description'],
            'dur'  => $data['total_duration'],
            'price'=> $data['total_price'],
            'type' => $data['service_type_id']
        ]);

        // get package id
        $packageId = $this->db->lastInsertId();

        // 2️⃣ Insert Package Items
        $findService = $this->db->prepare("
            SELECT service_id FROM services WHERE service_code = ?
        ");

        $insertItem = $this->db->prepare("
            INSERT INTO service_package_items
            (package_id, service_id)
            VALUES (?, ?)
        ");

        foreach ($data['services'] as $serviceCode) {

            $findService->execute([$serviceCode]);
            $serviceId = $findService->fetchColumn();

            if ($serviceId) {
                $insertItem->execute([$packageId, $serviceId]);
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
        // 1. Update package details
        $stmt = $this->db->prepare("
            UPDATE packages
            SET name                  = :name,
                description           = :desc,
                total_duration_minutes = :dur,
                total_price           = :price,
                service_type_id       = :type
            WHERE package_id = :id
        ");

        $stmt->execute([
            'name'  => $data['name'],
            'desc'  => $data['description'],
            'dur'   => $data['total_duration'],
            'price' => $data['total_price'],
            'type'  => $data['service_type_id'],
            'id'    => $id,
        ]);

        // 2. Delete old service items
        $del = $this->db->prepare("
            DELETE FROM service_package_items WHERE package_id = ?
        ");
        $del->execute([$id]);

        // 3. Re-insert new service items
        $findService = $this->db->prepare("
            SELECT service_id FROM services WHERE service_code = ?
        ");

        $insertItem = $this->db->prepare("
            INSERT INTO service_package_items (package_id, service_id) VALUES (?, ?)
        ");

        foreach ($data['services'] as $serviceCode) {
            $findService->execute([$serviceCode]);
            $serviceId = $findService->fetchColumn();

            if ($serviceId) {
                $insertItem->execute([$id, $serviceId]);
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
    $sql = "UPDATE packages SET status = :status WHERE package_id = :id";
    
    $stmt = db()->prepare($sql); // ✅ prepare statement
    
    $stmt->execute([
        'status' => $status,  // ✅ match :status
        'id'     => $id       // ✅ match :id
    ]);
}
}
