<?php
declare(strict_types=1);

namespace app\model\admin;

use PDO;

class Appointment
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = db();
    }

    /**
     * List appointments with main related data for index page.
     *
     * @return array
     */
    public function getAllWithRelations(): array
    {
        $sql = "
            SELECT
                a.appointment_id,
                a.appointment_date,
                a.appointment_time,
                a.status            AS db_status,
                a.notes,
                a.created_at,
                a.updated_at,

                c.customer_id,
                CONCAT(u.first_name, ' ', u.last_name) AS customer_name,

                s.service_id,
                s.name               AS service_name,

                b.branch_id,
                b.name               AS branch_name
            FROM appointments a
            JOIN customers c   ON c.customer_id = a.customer_id
            JOIN users u       ON u.user_id = c.user_id
            JOIN services s    ON s.service_id = a.service_id
            JOIN branches b    ON b.branch_id = a.branch_id
            ORDER BY a.appointment_date DESC, a.appointment_time DESC
        ";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get appointments for a specific date with related data.
     */
    public function getAppointmentsByDate(string $date): array
    {
        $sql = "
            SELECT
                a.appointment_id,
                a.appointment_date,
                a.appointment_time,
                a.status            AS db_status,
                a.assigned_to,
                a.notes,
                a.created_at,
                a.updated_at,

                c.customer_id,
                CONCAT(u.first_name, ' ', u.last_name) AS customer_name,

                s.service_id,
                s.name               AS service_name,

                b.branch_id,
                b.name               AS branch_name,

                CONCAT(su.first_name, ' ', su.last_name) AS supervisor_name
            FROM appointments a
            JOIN customers c   ON c.customer_id = a.customer_id
            JOIN users u       ON u.user_id = c.user_id
            JOIN services s    ON s.service_id = a.service_id
            JOIN branches b    ON b.branch_id = a.branch_id
            LEFT JOIN supervisors sup ON sup.supervisor_id = a.assigned_to
            LEFT JOIN users su ON su.user_id = sup.user_id
            WHERE a.appointment_date = :date
            ORDER BY a.appointment_date ASC, a.appointment_time ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':date' => $date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get appointments for a date range with related data.
     */
    public function getAppointmentsByDateRange(string $dateFrom, string $dateTo): array
    {
        $sql = "
            SELECT
                a.appointment_id,
                a.appointment_date,
                a.appointment_time,
                a.status            AS db_status,
                a.assigned_to,
                a.notes,
                a.created_at,
                a.updated_at,

                c.customer_id,
                CONCAT(u.first_name, ' ', u.last_name) AS customer_name,

                s.service_id,
                s.name               AS service_name,

                b.branch_id,
                b.name               AS branch_name,

                CONCAT(su.first_name, ' ', su.last_name) AS supervisor_name
            FROM appointments a
            JOIN customers c   ON c.customer_id = a.customer_id
            JOIN users u       ON u.user_id = c.user_id
            JOIN services s    ON s.service_id = a.service_id
            JOIN branches b    ON b.branch_id = a.branch_id
            LEFT JOIN supervisors sup ON sup.supervisor_id = a.assigned_to
            LEFT JOIN users su ON su.user_id = sup.user_id
            WHERE a.appointment_date BETWEEN :dateFrom AND :dateTo
            ORDER BY a.appointment_date ASC, a.appointment_time ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':dateFrom' => $dateFrom, ':dateTo' => $dateTo]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Single appointment with full details for show page.
     */
    public function findWithDetails(int $id): ?array
{
    $sql = "
        SELECT
            a.*,

            c.customer_id,
            CONCAT(cu.first_name, ' ', cu.last_name) AS customer_name,
            cu.phone        AS customer_phone,
            cu.email        AS customer_email,

            v.vehicle_id,
            v.vehicle_code,
            v.license_plate,
            v.make,
            v.model,
            v.year,
            v.color,

            s.service_id,
            s.name          AS service_name,
            s.service_code,
            st.type_name    AS service_type,
            s.default_price,

            b.branch_id,
            b.name          AS branch_name,
            b.branch_code,
            b.city          AS branch_city,
            b.address_line  AS branch_address,
            b.phone         AS branch_phone,
            b.manager_id,

            w.work_order_id,
            w.status        AS work_status,
            w.started_at,
            w.completed_at,
            w.total_cost,
            w.service_summary,

            mech.mechanic_id,
            CONCAT(mu.first_name, ' ', mu.last_name) AS mechanic_name,

            CONCAT(su.first_name, ' ', su.last_name) AS supervisor_name
        FROM appointments a
        JOIN customers c           ON c.customer_id = a.customer_id
        JOIN users cu              ON cu.user_id = c.user_id
        LEFT JOIN vehicles v       ON v.vehicle_id = a.vehicle_id
        JOIN services s            ON s.service_id = a.service_id
        LEFT JOIN service_types st ON st.type_id = s.type_id
        JOIN branches b            ON b.branch_id = a.branch_id
        LEFT JOIN work_orders w    ON w.appointment_id = a.appointment_id
        LEFT JOIN mechanics mech   ON mech.mechanic_id = w.mechanic_id
        LEFT JOIN users mu         ON mu.user_id = mech.user_id
        LEFT JOIN supervisors sup  ON sup.supervisor_id = a.assigned_to
        LEFT JOIN users su         ON su.user_id = sup.user_id
        WHERE a.appointment_id = :id
        LIMIT 1
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row ?: null;
}

    /**
     * Delete appointment row.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM appointments WHERE appointment_id = :id LIMIT 1");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * For filter dropdowns.
     */
    public function getBranches(): array
    {
        $sql = "SELECT branch_id, name FROM branches WHERE status = 'active' ORDER BY name";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getServices(): array
    {
        $sql = "SELECT service_id, name FROM services WHERE status = 'active' ORDER BY name";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Convert DB status to human label used on cards.
     */
    public static function statusLabel(string $dbStatus): string
    {
        switch ($dbStatus) {
            case 'requested':
                return 'Scheduled';
            case 'confirmed':
                return 'Confirmed';
            case 'in_progress':
                return 'In Progress';
            case 'completed':
                return 'Completed';
            case 'cancelled':
                return 'Cancelled';
            default:
                return ucfirst(str_replace('_', ' ', $dbStatus));
        }
    }
}