<?php
namespace app\model\Manager;

use PDO;

class PerformanceModel
{
    protected PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // =========================
    // STATS TILES
    // =========================

    public function getTotalCompletedJobs(int $branchId, ?string $date = null): int
    {
        $sql = "
            SELECT COUNT(*)
            FROM work_orders w
            JOIN appointments a ON w.appointment_id = a.appointment_id
            WHERE a.branch_id = :branchId
              AND w.status = 'completed'
              AND w.completed_at IS NOT NULL
        ";

        if ($date !== null) $sql .= " AND DATE(w.completed_at) = :date";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':branchId', $branchId, PDO::PARAM_INT);
        if ($date !== null) $stmt->bindValue(':date', $date);

        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public function getAvgCustomerSatisfaction(int $branchId, ?string $date = null): ?float
    {
        $sql = "
            SELECT ROUND(AVG(f.rating),1)
            FROM work_orders w
            JOIN appointments a ON w.appointment_id = a.appointment_id
            LEFT JOIN feedback f ON f.appointment_id = a.appointment_id
            WHERE a.branch_id = :branchId
              AND w.status = 'completed'
        ";

        if ($date !== null) $sql .= " AND DATE(w.completed_at) = :date";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':branchId', $branchId, PDO::PARAM_INT);
        if ($date !== null) $stmt->bindValue(':date', $date);

        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function getAvgServiceTime(int $branchId, ?string $date = null): int
    {
        $sql = "
            SELECT AVG(TIMESTAMPDIFF(MINUTE, w.started_at, w.completed_at))
            FROM work_orders w
            JOIN appointments a ON w.appointment_id = a.appointment_id
            WHERE a.branch_id = :branchId
              AND w.status = 'completed'
              AND w.started_at IS NOT NULL
              AND w.completed_at IS NOT NULL
        ";

        if ($date !== null) $sql .= " AND DATE(w.completed_at) = :date";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':branchId', $branchId, PDO::PARAM_INT);
        if ($date !== null) $stmt->bindValue(':date', $date);

        $stmt->execute();
        return (int)($stmt->fetchColumn() ?? 0);
    }

    public function getReturnRate(int $branchId, ?string $date = null): float
    {
        $sql = "
            SELECT 
                COUNT(DISTINCT CASE WHEN jobs > 1 THEN customer_id END)
                / COUNT(DISTINCT customer_id) * 100
            FROM (
                SELECT a.customer_id, COUNT(w.work_order_id) AS jobs
                FROM work_orders w
                JOIN appointments a ON w.appointment_id = a.appointment_id
                WHERE a.branch_id = :branchId
                  AND w.status = 'completed'
        ";

        if ($date !== null) $sql .= " AND DATE(w.completed_at) = :date";

        $sql .= " GROUP BY a.customer_id) t";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':branchId', $branchId, PDO::PARAM_INT);
        if ($date !== null) $stmt->bindValue(':date', $date);

        $stmt->execute();
        return round((float)$stmt->fetchColumn(), 1);
    }

    public function getTotalRevenue(int $branchId, ?string $date = null): float
    {
        $sql = "
            SELECT COALESCE(SUM(w.total_cost),0)
            FROM work_orders w
            JOIN appointments a ON w.appointment_id = a.appointment_id
            WHERE a.branch_id = :branchId
              AND w.status = 'completed'
        ";

        if ($date !== null) $sql .= " AND DATE(w.completed_at) = :date";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':branchId', $branchId, PDO::PARAM_INT);
        if ($date !== null) $stmt->bindValue(':date', $date);

        $stmt->execute();
        return (float)$stmt->fetchColumn();
    }

    // =========================
    // TEAM TABLE
    // =========================
 public function getTeamPerformance(int $branchId, ?string $date = null): array
{
    $sql = "
        SELECT 
            m.mechanic_id,
            u.first_name,
            u.last_name,
            m.specialization,
            COUNT(w.work_order_id) AS completed_jobs
        FROM mechanics m
        JOIN users u ON m.user_id = u.user_id
        LEFT JOIN work_orders w
            ON w.mechanic_id = m.mechanic_id
            AND w.status = 'completed'
    ";

    if ($date !== null) {
        $sql .= " AND DATE(w.completed_at) = :date";
    }

    $sql .= "
        WHERE m.branch_id = :branchId
        GROUP BY m.mechanic_id
        ORDER BY completed_jobs DESC
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':branchId', $branchId, PDO::PARAM_INT);
    if ($date !== null) {
        $stmt->bindValue(':date', $date);
    }
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



    public function getCompletedJobsByDay(int $branchId, ?string $date = null): array
    {
        $sql = "
            SELECT 
                DAYNAME(w.completed_at) AS day,
                COUNT(w.work_order_id) AS total
            FROM work_orders w
            JOIN appointments a ON w.appointment_id = a.appointment_id
            WHERE a.branch_id = :branchId
              AND w.status = 'completed'
        ";

        if ($date !== null) $sql .= " AND DATE(w.completed_at) = :date";

        $sql .= "
            GROUP BY DAYOFWEEK(w.completed_at)
            ORDER BY DAYOFWEEK(w.completed_at)
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':branchId', $branchId, PDO::PARAM_INT);
        if ($date !== null) $stmt->bindValue(':date', $date);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMechanicsWithCompletedJobs(int $branchId): array
{
    $sql = "
        SELECT 
            m.mechanic_id,
            u.first_name,
            u.last_name,
            m.specialization,
            m.experience_years,
            m.status,
            COUNT(w.work_order_id) AS completed_jobs
        FROM mechanics m
        JOIN users u ON m.user_id = u.user_id
        LEFT JOIN work_orders w 
            ON w.mechanic_id = m.mechanic_id 
            AND w.status = 'completed'
        WHERE m.branch_id = :branchId
        GROUP BY m.mechanic_id
        ORDER BY completed_jobs DESC
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':branchId', $branchId, \PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}

public function getMechanicById(int $mechanicId): ?array
{
    $sql = "
        SELECT 
            m.mechanic_id,
            u.first_name,
            u.last_name,
            m.specialization,
            m.experience_years,
            m.status,
            COUNT(w.work_order_id) AS completed_jobs
        FROM mechanics m
        JOIN users u ON m.user_id = u.user_id
        LEFT JOIN work_orders w 
            ON w.mechanic_id = m.mechanic_id 
            AND w.status = 'completed'
        WHERE m.mechanic_id = :mechanicId
        GROUP BY m.mechanic_id
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':mechanicId', $mechanicId, \PDO::PARAM_INT);
    $stmt->execute();

    $mechanic = $stmt->fetch(\PDO::FETCH_ASSOC);
    return $mechanic ?: null;
}

/* ==========================
   MECHANIC STATS
   ========================== */
public function getMechanicStats(int $mechanicId, string $start, string $end): array
{
    $sql = "
        SELECT
            COUNT(w.work_order_id) AS completed_jobs,
            ROUND(AVG(f.rating), 1) AS customer_satisfaction,
            ROUND(AVG(TIMESTAMPDIFF(MINUTE, w.started_at, w.completed_at))) AS avg_service_time,
            ROUND(
                COUNT(DISTINCT CASE 
                    WHEN customer_jobs.jobs > 1 THEN a.customer_id 
                END)
                / NULLIF(COUNT(DISTINCT a.customer_id), 0) * 100,
                1
            ) AS return_rate
        FROM work_orders w
        JOIN appointments a ON a.appointment_id = w.appointment_id
        LEFT JOIN feedback f ON f.appointment_id = a.appointment_id
        LEFT JOIN (
            SELECT 
                a.customer_id,
                COUNT(*) AS jobs
            FROM work_orders w
            JOIN appointments a ON a.appointment_id = w.appointment_id
            WHERE w.mechanic_id = :mid2
              AND w.status = 'completed'
              AND DATE(w.completed_at) BETWEEN :start2 AND :end2
            GROUP BY a.customer_id
        ) customer_jobs ON customer_jobs.customer_id = a.customer_id
        WHERE w.mechanic_id = :mid1
          AND w.status = 'completed'
          AND DATE(w.completed_at) BETWEEN :start1 AND :end1
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([
        ':mid1'   => $mechanicId,
        ':mid2'   => $mechanicId,
        ':start1'=> $start,
        ':end1'  => $end,
        ':start2'=> $start,
        ':end2'  => $end
    ]);

    return $stmt->fetch(PDO::FETCH_ASSOC) ?: [
        'completed_jobs' => 0,
        'customer_satisfaction' => null,
        'avg_service_time' => 0,
        'return_rate' => 0
    ];
}


/* ==========================
   MECHANIC JOBS BY DAY
   ========================== */
public function getMechanicJobsByDay(int $mechanicId, string $start, string $end): array
{
    $sql = "
        SELECT
            DAY(w.completed_at) AS day,
            COUNT(*) AS total
        FROM work_orders w
        WHERE w.mechanic_id = :mid
          AND w.status = 'completed'
          AND DATE(w.completed_at) BETWEEN :start AND :end
        GROUP BY DAY(w.completed_at)
        ORDER BY DAY(w.completed_at)
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([
        ':mid'   => $mechanicId,
        ':start'=> $start,
        ':end'  => $end
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


}
