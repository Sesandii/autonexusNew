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
        return $stmt->fetchAll();
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
        $packages = $stmt->fetchAll();

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
