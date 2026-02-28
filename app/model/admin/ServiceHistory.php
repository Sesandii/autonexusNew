<?php
declare(strict_types=1);

namespace app\model\admin;

use app\core\Database;
use PDO;

class ServiceHistory
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = db(); // your global db() function
    }

    /**
     * List completed services with optional filters.
     *
     * @param array $filters
     * @return array
     */
    public function list(array $filters = []): array
{
    $sql = "
        SELECT
            w.work_order_id,
            w.completed_at,
            w.total_cost,

            a.appointment_date,
            a.appointment_time,

            s.service_id,
            s.name          AS service_name,
            st.type_name    AS service_type,

            b.branch_id,
            b.name          AS branch_name,

            c.customer_id,
            CONCAT(u.first_name, ' ', u.last_name) AS customer_name,

            v.vehicle_id,
            v.license_plate,
            v.make,
            v.model,
            v.year,

            m.mechanic_id,
            CONCAT(mu.first_name, ' ', mu.last_name) AS mechanic_name
        FROM work_orders w
        JOIN appointments a ON a.appointment_id = w.appointment_id
        JOIN services s     ON s.service_id     = a.service_id
        LEFT JOIN service_types st ON st.type_id = s.type_id
        JOIN branches b     ON b.branch_id      = a.branch_id
        JOIN customers c    ON c.customer_id    = a.customer_id
        JOIN users u        ON u.user_id        = c.user_id
        LEFT JOIN vehicles v ON v.vehicle_id    = a.vehicle_id
        LEFT JOIN mechanics m ON m.mechanic_id  = w.mechanic_id
        LEFT JOIN users mu    ON mu.user_id     = m.user_id
        WHERE
            w.status = 'completed'
    ";

    $where  = [];
    $params = [];

    // Date range (by completed date)
    if (!empty($filters['from'])) {
        $where[]   = "DATE(w.completed_at) >= ?";
        $params[]  = $filters['from'];
    }

    if (!empty($filters['to'])) {
        $where[]   = "DATE(w.completed_at) <= ?";
        $params[]  = $filters['to'];
    }

    // Branch filter
    if (!empty($filters['branch_id'])) {
        $where[]   = "b.branch_id = ?";
        $params[]  = (int)$filters['branch_id'];
    }

    // Service type filter
    if (!empty($filters['type_id'])) {
        $where[]   = "s.type_id = ?";
        $params[]  = (int)$filters['type_id'];
    }

    // Text search
    if (!empty($filters['search'])) {
        $where[] = "
            (
                s.name LIKE ?
                OR st.type_name LIKE ?
                OR b.name LIKE ?
                OR CONCAT(u.first_name, ' ', u.last_name) LIKE ?
                OR v.license_plate LIKE ?
                OR v.vehicle_code LIKE ?
            )
        ";

        $like = '%' . $filters['search'] . '%';
        // One ? placeholder per column in the block above (6 of them)
        for ($i = 0; $i < 6; $i++) {
            $params[] = $like;
        }
    }

    if (!empty($where)) {
        $sql .= " AND " . implode(" AND ", $where);
    }

    $sql .= "
        ORDER BY w.completed_at DESC
        LIMIT 200
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    /**
     * Single service history record with full details.
     */
    public function find(int $id): ?array
    {
        $sql = "
            SELECT
                w.work_order_id,
                w.status          AS work_status,
                w.started_at,
                w.completed_at,
                w.total_cost,
                w.service_summary,

                a.appointment_id,
                a.appointment_date,
                a.appointment_time,
                a.status          AS appointment_status,
                a.notes           AS appointment_notes,
                a.created_at      AS appointment_created_at,
                a.updated_at      AS appointment_updated_at,

                s.service_id,
                s.service_code,
                s.name            AS service_name,
                s.base_duration_minutes,
                s.default_price,
                st.type_name      AS service_type,

                b.branch_id,
                b.name            AS branch_name,
                b.branch_code,
                b.city            AS branch_city,
                b.address_line    AS branch_address,
                b.phone           AS branch_phone,

                c.customer_id,
                c.customer_code,
                CONCAT(u.first_name, ' ', u.last_name) AS customer_name,
                u.phone           AS customer_phone,
                u.email           AS customer_email,

                v.vehicle_id,
                v.vehicle_code,
                v.license_plate,
                v.make,
                v.model,
                v.year,
                v.color,

                m.mechanic_id,
                m.mechanic_code,
                CONCAT(mu.first_name, ' ', mu.last_name) AS mechanic_name,
                mu.phone         AS mechanic_phone,
                m.specialization,
                m.experience_years,

                sup.supervisor_id,
                sup.supervisor_code,
                CONCAT(su.first_name, ' ', su.last_name) AS supervisor_name,
                su.phone         AS supervisor_phone
            FROM work_orders w
            JOIN appointments a ON a.appointment_id = w.appointment_id
            JOIN services s     ON s.service_id     = a.service_id
            LEFT JOIN service_types st ON st.type_id = s.type_id
            JOIN branches b     ON b.branch_id      = a.branch_id
            JOIN customers c    ON c.customer_id    = a.customer_id
            JOIN users u        ON u.user_id        = c.user_id
            LEFT JOIN vehicles v ON v.vehicle_id    = a.vehicle_id
            LEFT JOIN mechanics m ON m.mechanic_id  = w.mechanic_id
            LEFT JOIN users mu    ON mu.user_id     = m.user_id
            LEFT JOIN supervisors sup ON sup.branch_id = b.branch_id AND sup.status = 'active'
            LEFT JOIN users su        ON su.user_id    = sup.user_id
            WHERE w.work_order_id = :id
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function getBranches(): array
    {
        $sql = "SELECT branch_id, name FROM branches WHERE status = 'active' ORDER BY name";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getServiceTypes(): array
    {
        $sql = "SELECT type_id, type_name FROM service_types ORDER BY type_name";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
