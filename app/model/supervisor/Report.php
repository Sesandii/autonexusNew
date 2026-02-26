<?php

namespace app\model\supervisor;

use PDO;

class Report
{
    private PDO $pdo;

    public function __construct(PDO $pdo = null)
    {
        if ($pdo !== null) {
            $this->pdo = $pdo; // use passed PDO
        } else {
            // fallback for existing usage
            $this->pdo = db(); 
        }
    }

    // ... rest of your methods ...


    /* =====================================================
       GET REPORTS
    ===================================================== */

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
                v.license_plate,
                u.first_name, 
                u.last_name,
                m.mechanic_code
            FROM reports r
            JOIN work_orders w ON r.work_order_id = w.work_order_id
            JOIN appointments a ON w.appointment_id = a.appointment_id
            LEFT JOIN vehicles v ON a.vehicle_id = v.vehicle_id
            LEFT JOIN customers c ON a.customer_id = c.customer_id
            LEFT JOIN users u ON c.user_id = u.user_id
            LEFT JOIN mechanics m ON w.mechanic_id = m.mechanic_id
            ORDER BY r.created_at DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =====================================================
       CREATE / UPDATE / DELETE
    ===================================================== */

    public function create(array $data): int
    {
        $sql = "
        INSERT INTO reports (
            work_order_id,
            inspection_notes,
            quality_rating,
            checklist_verified,
            test_driven,
            concerns_addressed,
            report_summary,
            next_service_recommendation,
            status,
            created_at
        ) VALUES (
            :work_order_id,
            :inspection_notes,
            :quality_rating,
            :checklist_verified,
            :test_driven,
            :concerns_addressed,
            :report_summary,
            :next_service_recommendation,
            :status,
            NOW()
        )
    ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);

        return (int)$this->pdo->lastInsertId();
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

        $data['report_id'] = $reportId;

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
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

// Get single photo by ID
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

/** Daily Job Completion Report */
public function getDailyJobCompletion(?string $date = null, ?string $mechanicCode = null): array
{
    $where = ["w.status = 'completed'", "w.started_at IS NOT NULL", "w.completed_at IS NOT NULL"];
    $params = [];

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

            SUM(
                CASE
                    WHEN w.completed_at <= DATE_ADD(
                        w.started_at,
                        INTERVAL s.base_duration_minutes MINUTE
                    )
                    THEN 1 ELSE 0
                END
            ) AS on_time,

            SUM(
                CASE
                    WHEN w.completed_at > DATE_ADD(
                        w.started_at,
                        INTERVAL s.base_duration_minutes MINUTE
                    )
                    THEN 1 ELSE 0
                END
            ) AS delayed_count,

            ROUND(
                AVG(
                    TIMESTAMPDIFF(
                        MINUTE,
                        w.started_at,
                        w.completed_at
                    )
                ),
                2
            ) AS avg_completion_time

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
/**
 * Get all mechanics for dropdown
 */
/**
 * Get all mechanics for dropdown without duplicates
 */
public function getAllMechanics(): array
{
    $stmt = $this->pdo->query("
        SELECT DISTINCT mechanic_code 
        FROM mechanics 
        ORDER BY mechanic_code ASC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// app/model/supervisor/Report.php

public function getMechanicActivity(): array
{
    $sql = "
        SELECT 
            m.mechanic_code,
            CONCAT(u.first_name, ' ', u.last_name) AS mechanic_name,
            COUNT(DISTINCT w.work_order_id) AS total_assigned,
            SUM(CASE WHEN w.status = 'completed' THEN 1 ELSE 0 END) AS completed,
            SUM(CASE WHEN w.status = 'in_progress' THEN 1 ELSE 0 END) AS in_progress,
            ROUND(
                AVG(
                    CASE 
                        WHEN w.completed_at IS NOT NULL THEN TIMESTAMPDIFF(MINUTE, w.started_at, w.completed_at)
                        ELSE NULL
                    END
                ), 2
            ) AS avg_duration_mins
        FROM mechanics m
        LEFT JOIN users u ON m.user_id = u.user_id
        LEFT JOIN work_orders w ON w.mechanic_id = m.mechanic_id
        GROUP BY m.mechanic_code, mechanic_name
        ORDER BY mechanic_name ASC
    ";

    $stmt = $this->pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}
