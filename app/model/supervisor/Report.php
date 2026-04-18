<?php

namespace app\model\supervisor;

use PDO;

class Report
{
    private PDO $pdo;

    public function __construct(PDO $pdo = null)
    {
        if ($pdo !== null) {
            $this->pdo = $pdo; 
        } else {
            $this->pdo = db(); 
        }
    }

    public function getByWorkOrder(int $workOrderId): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM reports WHERE work_order_id = :id LIMIT 1"
        );
        $stmt->execute(['id' => $workOrderId]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function find(int $reportId): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM reports WHERE report_id = :id LIMIT 1"
        );
        $stmt->execute(['id' => $reportId]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function all(): array
{
    $sql = "
        SELECT 
            r.*,
            w.work_order_id,
            CONCAT(v.make, ' ', v.model) AS vehicle,
            CONCAT(u.first_name, ' ', u.last_name) AS customer_name,
            su.supervisor_code,
            -- FIX: Get the ID directly from the report table 
            -- to ensure it matches what was saved in store()
            r.supervisor_id, 
            s.name,
            m.mechanic_code
        FROM reports r
        JOIN work_orders w ON r.work_order_id = w.work_order_id
        JOIN appointments a ON w.appointment_id = a.appointment_id
        LEFT JOIN vehicles v ON a.vehicle_id = v.vehicle_id
        LEFT JOIN services s ON a.service_id = s.service_id
        -- JOIN on supervisor_id to get the code/name correctly
        LEFT JOIN supervisors su ON r.supervisor_id = su.supervisor_id
        LEFT JOIN customers c ON a.customer_id = c.customer_id
        LEFT JOIN users u ON c.user_id = u.user_id
        LEFT JOIN mechanics m ON w.mechanic_id = m.mechanic_id
        ORDER BY r.created_at DESC
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
    public function create(array $data): int
{
    $sql = "
        INSERT INTO reports (
            work_order_id,
            supervisor_id,
            inspection_notes,
            quality_rating,
            checklist_verified,
            test_driven,
            concerns_addressed,
            report_summary,
            status,
            created_at
        ) VALUES (
            :work_order_id,
            :supervisor_id,
            :inspection_notes,
            :quality_rating,
            :checklist_verified,
            :test_driven,
            :concerns_addressed,
            :report_summary,
            :status,
            NOW()
        )
    ";

    $stmt = $this->pdo->prepare($sql);
    
    $stmt->execute([
        'work_order_id'      => $data['work_order_id'],
        'supervisor_id'      => $data['supervisor_id'],
        'inspection_notes'   => $data['inspection_notes'],
        'quality_rating'     => $data['quality_rating'],
        'checklist_verified' => $data['checklist_verified'],
        'test_driven'        => $data['test_driven'],
        'concerns_addressed' => $data['concerns_addressed'],
        'report_summary'     => $data['report_summary'],
        'status'             => $data['status']
    ]);

    return (int)$this->pdo->lastInsertId();
}

public function updateVehicleServiceData(int $workOrderId, int $nextDue, int $interval): bool
{
    $stmt = $this->pdo->prepare("
        SELECT a.vehicle_id 
        FROM work_orders w
        JOIN appointments a ON w.appointment_id = a.appointment_id
        WHERE w.work_order_id = ?
    ");
    $stmt->execute([$workOrderId]);
    $vehicleId = $stmt->fetchColumn();

    if (!$vehicleId) {
        return false; 
    }

    $sql = "UPDATE vehicles 
            SET last_service_mileage = :next,
                service_interval_km = :interval
            WHERE vehicle_id = :v_id";
    
    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute([
        'next'     => $nextDue,
        'interval' => $interval,
        'v_id'     => $vehicleId
    ]);
}

public function update(int $reportId, array $data): bool
{
    $sql = "
        UPDATE reports SET
            inspection_notes = :inspection_notes,
            quality_rating = :quality_rating,
            checklist_verified = :checklist_verified,
            test_driven = :test_driven,
            concerns_addressed = :concerns_addressed,
            report_summary = :report_summary,
            next_service_recommendation = :next_service_recommendation,
            status = :status,    
            updated_at = NOW()
        WHERE report_id = :report_id
    ";

    $params = [
        'inspection_notes'            => $data['inspection_notes'],
        'quality_rating'              => $data['quality_rating'],
        'checklist_verified'          => $data['checklist_verified'],
        'test_driven'                 => $data['test_driven'],
        'concerns_addressed'          => $data['concerns_addressed'],
        'report_summary'              => $data['report_summary'],
        'next_service_recommendation' => $data['next_service_recommendation'] ?? null,
        'status'                      => $data['status'],
        'report_id'                   => $reportId
    ];

    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute($params);
}

    public function delete(int $reportId): bool
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM reports WHERE report_id = :id"
        );

        return $stmt->execute(['id' => $reportId]);
    }

    public function savePhoto(int $reportId, string $path): void
{
    $stmt = $this->pdo->prepare(
        "INSERT INTO report_photos (report_id, file_path) VALUES (?, ?)"
    );
    $stmt->execute([$reportId, $path]);
}

public function getPhotos(int $reportId): array
{
    $stmt = $this->pdo->prepare("SELECT file_path FROM report_photos WHERE report_id = ?");
    $stmt->execute([$reportId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function getLastInsertId(): int
{
    return (int)$this->pdo->lastInsertId();
}

public function getPhotosByReportId(int $reportId)
{
    $sql = "SELECT * FROM report_photos WHERE report_id = :report_id";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['report_id' => $reportId]);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}


public function getPhotoById(int $id)
{
    $stmt = $this->pdo->prepare("SELECT * FROM report_photos WHERE photo_id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

public function deletePhoto(int $id)
{
    $stmt = $this->pdo->prepare("DELETE FROM report_photos WHERE photo_id = ?");
    return $stmt->execute([$id]);
}

public function getDailyJobCompletion(?string $date = null, ?string $mechanicCode = null, ?int $branchId = null): array
{
    $where = ["w.status = 'completed'"];
    $params = [];

    if ($branchId) {
        $where[] = "m.branch_id = :branch_id";
        $params['branch_id'] = $branchId;
    }

    if ($date) {
        $where[] = "DATE(w.completed_at) = :report_date";
        $params['report_date'] = $date;
    }

    if ($mechanicCode) {
        $where[] = "m.mechanic_code = :mechanic_code";
        $params['mechanic_code'] = $mechanicCode;
    }

    $whereSql = implode(' AND ', $where);

    $sql = "
        SELECT
            DATE(w.completed_at) AS report_date,
            m.mechanic_code,
            COUNT(*) AS total_completed,
            SUM(CASE WHEN w.completed_at <= DATE_ADD(w.started_at, INTERVAL s.base_duration_minutes MINUTE) THEN 1 ELSE 0 END) AS on_time,
            SUM(CASE WHEN w.completed_at > DATE_ADD(w.started_at, INTERVAL s.base_duration_minutes MINUTE) THEN 1 ELSE 0 END) AS delayed_count,
            ROUND(AVG(TIMESTAMPDIFF(MINUTE, w.started_at, w.completed_at)), 2) AS avg_completion_time
        FROM work_orders w
        JOIN appointments a ON w.appointment_id = a.appointment_id
        JOIN services s ON a.service_id = s.service_id
        LEFT JOIN mechanics m ON w.mechanic_id = m.mechanic_id
        WHERE {$whereSql}
        GROUP BY DATE(w.completed_at), m.mechanic_code
        ORDER BY DATE(w.completed_at) DESC
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function getAllMechanics(?int $branchId = null): array
{
    $sql = "SELECT DISTINCT mechanic_code 
            FROM mechanics 
            WHERE branch_id = :branch_id
            ORDER BY mechanic_code ASC";
    
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['branch_id' => $branchId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function getMechanicActivity(?string $date = null, ?string $mechanicCode = null, ?int $branchId = null): array
{
    $params = [];
    $where = [];

    if ($branchId) {
        $where[] = "m.branch_id = :branch_id";
        $params['branch_id'] = $branchId;
    }

    if ($date) {
        $where[] = "DATE(w.started_at) = :date";
        $params['date'] = $date;
    }

    if ($mechanicCode) {
        $where[] = "m.mechanic_code = :m_code";
        $params['m_code'] = $mechanicCode;
    }

    $whereSql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

    $sql = "
        SELECT 
            m.mechanic_code,
            CONCAT(u.first_name, ' ', u.last_name) AS mechanic_name,
            COUNT(DISTINCT w.work_order_id) AS total_assigned,
            SUM(CASE WHEN w.status = 'completed' THEN 1 ELSE 0 END) AS completed,
            SUM(CASE WHEN w.status = 'in_progress' THEN 1 ELSE 0 END) AS in_progress,
            SUM(CASE WHEN w.status = 'open' OR w.status = 'pending' THEN 1 ELSE 0 END) AS open,
            ROUND(AVG(CASE WHEN w.status = 'completed' THEN TIMESTAMPDIFF(MINUTE, w.started_at, w.completed_at) END), 1) AS avg_duration_mins
        FROM mechanics m
        LEFT JOIN users u ON m.user_id = u.user_id
        LEFT JOIN work_orders w ON w.mechanic_id = m.mechanic_id
        {$whereSql}
        GROUP BY m.mechanic_id, m.mechanic_code, mechanic_name
        ORDER BY mechanic_name ASC
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}

public function getBranchPerformanceSummary(int $branchId, string $date): array
{
    $sql = "SELECT 
        IFNULL(ROUND(AVG(TIMESTAMPDIFF(MINUTE, w.started_at, w.completed_at)), 0), 0) as avg_comp,
        IFNULL(ROUND(AVG(i.total_amount), 2), 0) as avg_invoice,
        IFNULL(ROUND(AVG(TIMESTAMPDIFF(HOUR, a.created_at, w.started_at)), 1), 0) as avg_appr
        FROM work_orders w
        INNER JOIN appointments a ON w.appointment_id = a.appointment_id
        LEFT JOIN invoices i ON w.work_order_id = i.work_order_id
        WHERE a.branch_id = :branch_id 
        AND DATE(w.completed_at) = :date
        AND w.status = 'completed'"; 
    
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['branch_id' => $branchId, 'date' => $date]);
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return [
        'avg_comp' => $result['avg_comp'] ?? 0,
        'avg_invoice' => $result['avg_invoice'] ?? 0,
        'avg_appr' => $result['avg_appr'] ?? 0
    ];
}

public function getAppointmentStatusStats(int $branchId, string $date): array
{
    $sql = "SELECT status as label, COUNT(*) as count 
            FROM appointments 
            WHERE branch_id = :branch_id AND DATE(appointment_date) = :date
            GROUP BY status";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['branch_id' => $branchId, 'date' => $date]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return [
        'labels' => array_column($results, 'label'),
        'counts' => array_column($results, 'count')
    ];
}

public function getHourlyJobStats(int $branchId, string $date): array
{
    $sql = "SELECT HOUR(completed_at) as hour, COUNT(*) as count 
            FROM work_orders w
            JOIN appointments a ON w.appointment_id = a.appointment_id
            WHERE a.branch_id = :branch_id AND DATE(w.completed_at) = :date
            GROUP BY hour ORDER BY hour ASC";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['branch_id' => $branchId, 'date' => $date]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return [
        'labels' => array_map(fn($h) => str_pad($h, 2, '0', STR_PAD_LEFT) . ':00', array_column($results, 'hour')),
        'values' => array_column($results, 'count')
    ];
}

public function getWeeklyBookingTrend(int $branchId): array
{
    $sql = "SELECT DATE(created_at) as date, COUNT(*) as total 
            FROM appointments 
            WHERE branch_id = :branch_id 
            AND created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            GROUP BY date ORDER BY date ASC";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['branch_id' => $branchId]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return [
        'dates' => array_column($results, 'date'),
        'totals' => array_column($results, 'total')
    ];
}

public function getMechanicCompletionComparison(int $branchId, ?string $date = null, ?string $mechanicCode = null): array
{
    $sql = "SELECT m.mechanic_code, COUNT(w.work_order_id) as completed_count
            FROM mechanics m
            JOIN work_orders w ON m.mechanic_id = w.mechanic_id
            WHERE m.branch_id = :branch_id AND w.status = 'completed'";
    
    $params = ['branch_id' => $branchId];

    if ($date) {
        $sql .= " AND DATE(w.completed_at) = :selected_date"; 
        $params['selected_date'] = $date;
    }

    if ($mechanicCode) {
        $sql .= " AND m.mechanic_code = :mechanic_code";
        $params['mechanic_code'] = $mechanicCode;
    }

    $sql .= " GROUP BY m.mechanic_id, m.mechanic_code";
            
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}


public function getMechanicEfficiencyStats(int $branchId, ?string $date = null, ?string $mechanicCode = null): array
{
    $sql = "SELECT m.mechanic_code, 
            ROUND(AVG(TIMESTAMPDIFF(MINUTE, w.started_at, w.completed_at)), 0) as avg_mins
            FROM mechanics m
            JOIN work_orders w ON m.mechanic_id = w.mechanic_id
            WHERE m.branch_id = :branch_id AND w.status = 'completed'";
            
    $params = ['branch_id' => $branchId];

    if ($date) {
        $sql .= " AND DATE(w.completed_at) = :selected_date";
        $params['selected_date'] = $date;
    }

    if ($mechanicCode) {
        $sql .= " AND m.mechanic_code = :mechanic_code";
        $params['mechanic_code'] = $mechanicCode;
    }

    $sql .= " GROUP BY m.mechanic_id, m.mechanic_code";
            
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}


public function updateVehicleMileage(int $workOrderId, int $mileage): bool
{
    $sql = "UPDATE vehicles v
            JOIN work_orders w ON v.vehicle_id = w.vehicle_id
            SET v.last_service_mileage = :mileage
            WHERE w.work_order_id = :wo_id";
    
    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute([
        'mileage' => $mileage,
        'wo_id'   => $workOrderId
    ]);
}

public function getWorkOrderWithVehicleData(int $workOrderId)
{
    $sql = "SELECT w.*, v.last_service_mileage, v.service_interval_km, v.license_plate, s.name, m.mechanic_code,
                   u.first_name AS customer_first_name, u.last_name AS customer_last_name
            FROM work_orders w
            JOIN appointments a ON w.appointment_id = a.appointment_id
            JOIN vehicles v ON a.vehicle_id = v.vehicle_id
            LEFT JOIN customers c ON a.customer_id = c.customer_id
            LEFT JOIN services s ON a.service_id = s.service_id
            LEFT JOIN mechanics m ON w.mechanic_id = m.mechanic_id
            LEFT JOIN users u ON c.user_id = u.user_id
            WHERE w.work_order_id = ?";
            
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$workOrderId]);
    return $stmt->fetch();
}

public function getServiceTasks($workOrderId) {
    $sql = "SELECT item_name, status FROM checklist WHERE work_order_id = ?";
    
    $stmt = $this->pdo->prepare($sql);
    
    $stmt->execute([$workOrderId]);
    
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}

public function getReportPhotos($reportId) {
    $sql = "SELECT file_path FROM report_photos WHERE report_id = ?";
    
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$reportId]);
    
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}

}
