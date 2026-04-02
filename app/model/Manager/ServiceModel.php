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

    public function getActiveServices(): array
    {
        return $this->db
            ->query("SELECT * FROM services WHERE status='active' ORDER BY name")
            ->fetchAll();
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
        SELECT package_id, package_code, name, description, total_duration_minutes, total_price, service_type_id
        FROM packages
        WHERE status = 'active'
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
            INNER JOIN package_services ps ON s.service_id = ps.service_id
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
            FROM package_services ps
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
            $stmt = $this->db->prepare("
                INSERT INTO packages
                (package_code, name, description, status, total_duration_minutes, total_price)
                VALUES (:code, :name, :desc, 'active', :dur, :price)
            ");

            $stmt->execute([
                'code'  => $data['package_code'],
                'name'  => $data['name'],
                'desc'  => $data['description'],
                'dur'   => $data['total_duration'],
                'price' => $data['total_price'],
            ]);

            $link = $this->db->prepare("
                INSERT INTO package_services (package_id, service_id, quantity)
                VALUES (:pkg, :srv, :qty)
            ");

            foreach ($data['services'] as $serviceCode) {
                $link->execute([
                   'pkg' => $packageId,
                    'srv' => $serviceId,
                    'qty' => $qty ?: null
                ]);
            }

            $this->db->commit();

        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
