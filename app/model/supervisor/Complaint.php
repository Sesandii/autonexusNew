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
                c.customer_name,
                c.phone,
                c.email,
                c.vehicle,
                c.vehicle_number,
                c.complaint_date,
                c.complaint_time,
                c.description,
                c.priority,
                c.status,
                c.assigned_to,
                a.service_id,
                s.name AS service_name
            FROM complaints c
            LEFT JOIN appointments a ON c.appointment_id = a.appointment_id
            LEFT JOIN services s ON a.service_id = s.service_id
            ORDER BY c.complaint_date DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
