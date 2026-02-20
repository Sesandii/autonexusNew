<?php

namespace app\model\supervisor;

class Appointment
{
    private $db;
    public function __construct($db = null)
{
    $this->db = $db ?? new \app\core\Database();
}


    public function allAvailable()
    {
        $sql = "SELECT a.*, s.name AS service_name
                FROM appointments a
                LEFT JOIN services s USING(service_id)
                WHERE a.status IN ('requested', 'confirmed')";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getVehicleByLicense($licensePlate)
{
    $sql = "SELECT v.*, CONCAT(u.first_name,' ', u.last_name) AS owner_name
    FROM vehicles v
    JOIN customers c ON v.customer_id = c.customer_id
    JOIN users u ON c.user_id = u.user_id
    WHERE license_plate = :plate LIMIT 1";
    $stmt = $this->db->prepare($sql);
    $stmt->execute(['plate' => $licensePlate]);
    return $stmt->fetch(\PDO::FETCH_ASSOC);
}

public function getVehicleHistoryByLicense($licensePlate, $fromDate = null, $toDate = null)
{
    $sql = "
        SELECT 
            a.appointment_id,
            a.appointment_date,
            a.appointment_time,
            a.status,
            a.updated_at,
            s.name AS service_name,
            s.description AS service_description,
            s.default_price,
            a.branch_id
        FROM appointments a
        JOIN vehicles v ON v.vehicle_id = a.vehicle_id
        JOIN services s ON s.service_id = a.service_id
        WHERE v.license_plate = :plate
          AND a.status = 'completed'
    ";

    $params = ['plate' => $licensePlate];

    // ✅ Filter by completion (updated_at)
    if (!empty($fromDate)) {
        $sql .= " AND DATE(a.updated_at) >= :fromDate";
        $params['fromDate'] = $fromDate;
    }

    if (!empty($toDate)) {
        $sql .= " AND DATE(a.updated_at) <= :toDate";
        $params['toDate'] = $toDate;
    }

    $sql .= " ORDER BY a.updated_at DESC";

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}


public function getAppointmentDetails($appointmentId)
{
    $sql = "
        SELECT 
            a.appointment_id,
            a.appointment_date,
            a.appointment_time,
            a.status AS appointment_status,
            a.notes,

            -- Service Info
            s.name AS service_name,
            s.description AS service_description,
            s.default_price,

            -- Customer Info
            c.customer_code,

            -- Work Order Info
            wo.mechanic_id,
            wo.service_summary,
            wo.total_cost,
            wo.status AS work_order_status,
            wo.started_at,
            wo.completed_at,

            -- Vehicle Info
            v.vehicle_id,
            v.license_plate,
            v.make,
            v.model,
            v.year,
            v.color,
            v.status AS vehicle_status

        FROM appointments a
        LEFT JOIN services s ON a.service_id = s.service_id
        LEFT JOIN customers c ON a.customer_id = c.customer_id
        LEFT JOIN work_orders wo ON a.appointment_id = wo.appointment_id
        LEFT JOIN vehicles v ON a.vehicle_id = v.vehicle_id
        WHERE a.appointment_id = :id
    ";

    $stmt = db()->prepare($sql);
    $stmt->execute(['id' => $appointmentId]);
    return $stmt->fetch(\PDO::FETCH_ASSOC);
}  

public function getVehicleHistoryByLicenseWithDateRange(
    string $licensePlate,
    string $fromDate,
    string $toDate
) {
    $sql = "
        SELECT 
            a.appointment_id,
            a.appointment_date,
            a.appointment_time,
            a.status,
            s.name AS service_name,
            s.description AS service_description,
            s.default_price,
            a.branch_id,
            a.updated_at
        FROM appointments a
        JOIN vehicles v ON v.vehicle_id = a.vehicle_id
        JOIN services s ON s.service_id = a.service_id
        WHERE v.license_plate = :plate
          AND a.status = 'completed'
          AND DATE(a.updated_at) BETWEEN :fromDate AND :toDate
        ORDER BY a.updated_at DESC
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([
        'plate' => $licensePlate,
        'fromDate' => $fromDate,
        'toDate' => $toDate
    ]);

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}

}