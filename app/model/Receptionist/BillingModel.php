<?php
namespace app\model\Receptionist;

use PDO;

class BillingModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = db(); // your singleton PDO from Database.php
    }

    /** Get customer details using phone number */

    public function getCustomerByPhone(string $phone)
    {
        $sql = "
            SELECT c.customer_id, u.user_id, u.first_name, u.last_name, u.email, u.phone
            FROM customers c
            JOIN users u ON u.user_id = c.user_id
            WHERE u.phone = :phone
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['phone' => $phone]);
        return $stmt->fetch();
    }

    public function getVehiclesByUser(int $userId)
{
    $sql = "
        SELECT v.vehicle_id, v.make, v.model, v.license_plate
        FROM vehicles v
        JOIN customers c ON v.customer_id = c.customer_id
        WHERE c.user_id = :user_id
        ORDER BY v.make, v.model
    ";
    $stmt = $this->db->prepare($sql);
    $stmt->execute(['user_id' => $userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    // Services and service price functions remain same...



    /** Get all service types */
    public function getServiceTypes()
    {
        $sql = "SELECT type_id, type_name FROM service_types ORDER BY type_name ASC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Get all services belonging to a service type */
    public function getServicesByType(int $typeId)
    {
        $sql = "
            SELECT service_id, name, default_price
            FROM services
            WHERE type_id = :id
            ORDER BY name ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $typeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

        /** Get all active packages */
    public function getPackages()
    {
        $sql = "
            SELECT package_code, name, total_price
            FROM packages
            WHERE status = 'active'
            ORDER BY name ASC
        ";

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Get all services included in a package */
    public function getServicesByPackage(string $packageCode)
    {
        $sql = "
            SELECT s.service_id, s.name AS service_name, s.default_price
            FROM services s
            JOIN package_services ps ON s.service_id = ps.service_id
            WHERE ps.package_code = :package_code
            ORDER BY s.name ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['package_code' => $packageCode]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /** Get service price */
    public function getServicePrice(int $serviceId)
    {
        $sql = "
            SELECT default_price 
            FROM services 
            WHERE service_id = :id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $serviceId]);
        return $stmt->fetchColumn();
    }


}