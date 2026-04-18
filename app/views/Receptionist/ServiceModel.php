<?php
namespace app\model\Receptionist;

use PDO;

// ----------------------
// Service Model
// ----------------------
class ServiceModel
{
    private PDO $db;

    public function __construct()
    {
        // Uses global db() loaded by index.php
        $this->db = db();
    }

    public function getActiveServices(): array
    {
        $sql = "SELECT service_id, service_code, name, description,
                       base_duration_minutes, default_price, status
                FROM services
                WHERE status = 'active'
                ORDER BY name ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
         return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

     public function getLastServiceCode(): ?string
{
    $stmt = $this->db->query(
        "SELECT service_code FROM services ORDER BY created_at DESC LIMIT 1"
    );
    return $stmt->fetchColumn() ?: 'SRV001';
}

public function create(array $data): bool
{
    $sql = "INSERT INTO services 
        (service_code, name, description, base_duration_minutes, default_price, type_id, status, created_at)
        VALUES (:service_code, :name, :description, :duration, :price, :type_id, 'pending', NOW())";

    $stmt = $this->db->prepare($sql);

    return $stmt->execute([
        ':service_code' => $data['service_code'],
        ':name'         => $data['name'],
        ':description'  => $data['description'],
        ':duration'     => $data['duration'],
        ':price'        => $data['price'],
        ':type_id'      => $data['type_id'],
    ]);
}


}

// ----------------------
// Package Model
// ----------------------
class PackageModel
{
    private PDO $db;

    public function __construct()
    {
        // SAME global db() — do NOT require Database.php
        $this->db = db();
    }

    public function getAllPackages(): array
    {
        $stmt = $this->db->prepare("
            SELECT package_code, name, description,
                   total_duration_minutes, total_price
            FROM packages
            WHERE status = 'active'
            ORDER BY created_at DESC
        ");
        $stmt->execute();
        $packages = $stmt->fetchAll(PDO::FETCH_ASSOC);


        // Fetch package services
        foreach ($packages as &$pkg) {
            $stmt = $this->db->prepare("
                SELECT s.service_code, s.name, s.description,
                       s.base_duration_minutes, s.default_price
                FROM services s
                INNER JOIN package_services ps
                    ON s.service_code = ps.service_code
                WHERE ps.package_code = ?
            ");
            $stmt->execute([$pkg['package_code']]);
            $pkg['services'] = $stmt->fetchAll();

            // If total not stored, calculate
            if (empty($pkg['total_duration_minutes'])) {
                $pkg['total_duration_minutes'] = array_sum(
                    array_column($pkg['services'], 'base_duration_minutes')
                );
            }

            if (empty($pkg['total_price'])) {
                $pkg['total_price'] = array_sum(
                    array_column($pkg['services'], 'default_price')
                );
            }
        }

        return $packages;
    }

     public function getLastPackageCode(): ?string
{
    $stmt = $this->db->query(
        "SELECT package_code FROM packages ORDER BY created_at DESC LIMIT 1"
    );
    return $stmt->fetchColumn() ?: 'PKG001';
}

public function create(array $data): bool
{
    $this->db->beginTransaction();

    try {
        // 1. Insert package
        $sql = "INSERT INTO packages 
            (package_code, name, description, total_duration_minutes, total_price, status, created_at)
            VALUES (:package_code, :name, :description, :total_duration, :total_price, 'pending', NOW())";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':package_code' => $data['package_code'],
            ':name'         => $data['name'],
            ':description'  => $data['description'],
            ':total_duration' => $data['total_duration'],
            ':total_price'    => $data['total_price'],
        ]);

        // 2. Insert package services
        $sqlService = "INSERT INTO package_services (package_code, service_code) VALUES (:package_code, :service_code)";
        $stmtService = $this->db->prepare($sqlService);

        foreach ($data['services'] as $service_code) {
            $stmtService->execute([
                ':package_code' => $data['package_code'],
                ':service_code' => $service_code
            ]);
        }

        $this->db->commit();
        return true;

    } catch (\Exception $e) {
        $this->db->rollBack();
        error_log($e->getMessage());
        return false;
    }
}


}




/*namespace app\model\Receptionist;

use PDO;

class ServiceModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = db(); // use the global db() PDO connection
    }*/

    /** Get all active services */
   /* public function getActiveServices(): array
    {
        $sql = "SELECT service_id, service_code, name, description,
                       base_duration_minutes, default_price, status
                FROM services
                WHERE status = 'active'
                ORDER BY name ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

class PackageModel extends \app\core\Model

{
    public function getAllPackages(): array
    {
        $pdo = db();

        // Fetch all active packages
        $stmt = $pdo->prepare("
            SELECT package_code, name, description, total_duration_minutes, total_price
            FROM packages
            WHERE status = 'active'
            ORDER BY created_at DESC
        ");
        $stmt->execute();
        $packages = $stmt->fetchAll();

        // Fetch services for each package
        foreach ($packages as &$pkg) {
            $stmt = $pdo->prepare("
                SELECT s.service_code, s.name, s.description, s.base_duration_minutes, s.default_price
                FROM services 
                INNER JOIN package_services ps ON s.service_code = ps.service_code
                WHERE ps.package_code = ?
            ");
            $stmt->execute([$pkg['package_code']]);
            $pkg['services'] = $stmt->fetchAll();

            // Optional: calculate total duration and total price if not in table
            if (empty($pkg['total_duration_minutes'])) {
                $pkg['total_duration_minutes'] = array_sum(array_column($pkg['services'], 'base_duration_minutes'));
            }
            if (empty($pkg['total_price'])) {
                $pkg['total_price'] = array_sum(array_column($pkg['services'], 'default_price'));
            }
        }

        return $packages;
    }
}
*/
