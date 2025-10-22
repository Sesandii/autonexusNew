<?php
namespace app\model\supervisor;

use PDO;

class WorkOrder
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = db(); // use your global db() connection helper
    }

    /** ✅ Fetch all work orders with related info */
    // public function getall(): array
    // {
    //     $sql = "SELECT 
    //                 w.*, 
    //                 a.appointment_date, 
    //                 a.appointment_time, 
    //                 s.name AS service_name, 
    //                 m.mechanic_code
    //             FROM work_orders w
    //             LEFT JOIN appointments a ON w.appointment_id = a.appointment_id
    //             LEFT JOIN services s ON a.service_id = s.service_id
    //             LEFT JOIN mechanics m ON w.mechanic_id = m.mechanic_id
    //             ORDER BY w.work_order_id DESC";
    //     return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    // }

    /** ✅ Create new work order */
    public function create(array $data): void
    {
        $sql = "INSERT INTO work_orders 
                (appointment_id, mechanic_id, service_summary, total_cost, status)
                VALUES (:appointment_id, :mechanic_id, :service_summary, :total_cost, :status)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'appointment_id' => $data['appointment_id'],
            'mechanic_id'    => $data['mechanic_id'] ?: null,
            'service_summary'=> $data['service_summary'],
            'total_cost'     => $data['total_cost'],
            'status'         => $data['status']
        ]);
    }

 

    /** ✅ Update work order */
    public function update(int $id, array $data): void
{
    $sql = "UPDATE work_orders 
            SET appointment_id = :appointment_id,
                mechanic_id    = :mechanic_id,
                service_summary= :service_summary,
                total_cost     = :total_cost,
                status         = :status
            WHERE work_order_id = :id";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
        'appointment_id'  => $data['appointment_id'],
        'mechanic_id'     => $data['mechanic_id'] ?: null,
        'service_summary' => $data['service_summary'],
        'total_cost'      => $data['total_cost'],
        'status'          => $data['status'],
        'id'              => $id,
    ]);
}

    /** ✅ Delete a work order */
    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM work_orders WHERE work_order_id = :id");
        $stmt->execute(['id' => $id]);
    }

    /** ✅ Check if a work order exists for an appointment */
    public function getAppointmentExists(int $appointment_id): bool
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM work_orders WHERE appointment_id = :id");
        $stmt->execute(['id' => $appointment_id]);
        return $stmt->fetchColumn() > 0;
    }

    /** ✅ Get appointments available for new work orders */
    public function getAvailableAppointments(): array
    {
        $sql = "SELECT 
                    a.*, 
                    s.name AS service_name 
                FROM appointments a
                LEFT JOIN customers c USING(customer_id)
                LEFT JOIN services s USING(service_id)
                WHERE a.status IN ('requested', 'confirmed')";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /** ✅ Get all active mechanics */
    public function getActiveMechanics(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM mechanics WHERE status = 'active'");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** ✅ Get all services */
    public function getServices(): array
    {
        return $this->pdo->query("SELECT * FROM services")->fetchAll(PDO::FETCH_ASSOC);
    }

    /** ✅ Get all service types */
    public function getServiceTypes(): array
    {
        return $this->pdo->query("SELECT * FROM service_types")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAll(): array
{
    $sql = "SELECT 
                w.*, 
                a.appointment_date, 
                a.appointment_time, 
                s.name AS service_name, 
                m.mechanic_code
            FROM work_orders w
            LEFT JOIN appointments a ON w.appointment_id = a.appointment_id
            LEFT JOIN services s ON a.service_id = s.service_id
            LEFT JOIN mechanics m ON w.mechanic_id = m.mechanic_id
            ORDER BY w.work_order_id DESC";
    return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

public function find(int $id): ?array
{
    $sql = "SELECT 
                w.*,
                a.appointment_date, a.appointment_time,
                s.service_id, s.name AS service_name, s.default_price,
                m.mechanic_code
            FROM work_orders w
            LEFT JOIN appointments a ON w.appointment_id = a.appointment_id
            LEFT JOIN services s ON a.service_id = s.service_id
            LEFT JOIN mechanics m ON w.mechanic_id = m.mechanic_id
            WHERE w.work_order_id = :id";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

}
