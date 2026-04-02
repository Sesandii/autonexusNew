<?php
namespace app\model\Manager;

use PDO;

class IndividualPerformanceModel
{
    protected PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getMechanic(int $mechanicId): ?array
    {
        $sql = "
            SELECT m.*, u.first_name, u.last_name
            FROM mechanic m
            JOIN users u ON u.user_id = m.user_id
            WHERE m.mechanic_id = :mechanicId
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':mechanicId', $mechanicId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }


    public function getStats(int $mechanicId, ?string $month): array
    {
        $dateFilter = $month ? "AND DATE_FORMAT(w.completed_at, '%Y-%m') = :month " : "";

        $stats = [];

        // Completed Jobs
        $sql = "
            SELECT COUNT(*) as total
            FROM work_orders w
            WHERE w.mechanic_id = :mechanicId
              AND w.status = 'completed'
              $dateFilter
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':mechanicId', $mechanicId, PDO::PARAM_INT);
        if ($month) $stmt->bindValue(':month', $month);
        $stmt->execute();
        $stats['completed_jobs'] = (int)$stmt->fetchColumn();


        // Avg Satisfaction
        $sql = "
            SELECT ROUND(AVG(f.rating),1) 
            FROM work_orders w
            JOIN feedback f ON f.appointment_id = w.appointment_id
            WHERE w.mechanic_id = :mechanicId
              AND w.status = 'completed'
              $dateFilter
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':mechanicId', $mechanicId, PDO::PARAM_INT);
        if ($month) $stmt->bindValue(':month', $month);
        $stmt->execute();
        $stats['customer_satisfaction'] = $stmt->fetchColumn();


        // Avg Service Time
        $sql = "
            SELECT AVG(TIMESTAMPDIFF(MINUTE, w.started_at, w.completed_at))
            FROM work_orders w
            WHERE w.mechanic_id = :mechanicId
              AND w.status = 'completed'
              $dateFilter
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':mechanicId', $mechanicId, PDO::PARAM_INT);
        if ($month) $stmt->bindValue(':month', $month);
        $stmt->execute();
        $stats['avg_service_time'] = (int)$stmt->fetchColumn();


        // Return Rate
        $sql = "
            SELECT 
                COUNT(DISTINCT CASE WHEN jobs > 1 THEN a.customer_id END) /
                COUNT(DISTINCT a.customer_id) * 100
            FROM (
                SELECT a.customer_id, COUNT(w.work_order_id) AS jobs
                FROM work_orders w
                JOIN appointments a ON a.appointment_id = w.appointment_id
                WHERE w.mechanic_id = :mechanicId
                $dateFilter
                GROUP BY a.customer_id
            ) t
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':mechanicId', $mechanicId, PDO::PARAM_INT);
        if ($month) $stmt->bindValue(':month', $month);
        $stmt->execute();
        $stats['return_rate'] = round((float)$stmt->fetchColumn(), 1);

        return $stats;
    }

    public function getJobsByDay(int $mechanicId, ?string $month): array
    {
        $dateFilter = $month ? "AND DATE_FORMAT(w.completed_at, '%Y-%m') = :month " : "";

        $sql = "
            SELECT DAYNAME(w.completed_at) as day, COUNT(*) as total
            FROM work_orders w
            WHERE w.mechanic_id = :mechanicId
              AND w.status = 'completed'
              $dateFilter
            GROUP BY DAYOFWEEK(w.completed_at)
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':mechanicId', $mechanicId, PDO::PARAM_INT);
        if ($month) $stmt->bindValue(':month', $month);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
