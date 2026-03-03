<?php
namespace app\model\supervisor;

use PDO;

class Complaint
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllComplaints()
{
    $stmt = $this->db->prepare("
        SELECT 
            c.complaint_id,
            c.subject,
            c.description,
            c.priority,
            c.status,
            c.created_at,
            c.updated_at,
            c.resolved_at,
            u.user_id AS customer_id,
            CONCAT(u.first_name, ' ', u.last_name) AS customer_name,
            u.phone AS customer_phone,
            u.email AS customer_email,
            v.vehicle_id,
            CONCAT(v.make, ' ', v.model) AS vehicle,
            v.license_plate AS vehicle_number,
            b.branch_id,
            b.name AS branch_name,
            au.user_id AS assigned_user_id,
            CONCAT(au.first_name, ' ', au.last_name) AS assigned_user_name,
            a.appointment_id,
            s.service_id,
            s.name AS service_name
        FROM complaints c
        LEFT JOIN users u ON c.customer_id = u.user_id
        LEFT JOIN vehicles v ON c.vehicle_id = v.vehicle_id
        LEFT JOIN branches b ON c.branch_id = b.branch_id
        LEFT JOIN users au ON c.assigned_to_user_id = au.user_id
        LEFT JOIN appointments a ON c.appointment_id = a.appointment_id
        LEFT JOIN services s ON a.service_id = s.service_id
        ORDER BY c.created_at DESC
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}
