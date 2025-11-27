<?php
declare(strict_types=1);

namespace app\model\admin;

use app\core\Database;
use PDO;

class Appointment
{
    private PDO $pdo;

     public function __construct()
    {
        $this->pdo = db(); // your global db() function
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
     * Single appointment with full details for show / edit.
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
                s.default_price,

                b.branch_id,
                b.name          AS branch_name,
                b.city          AS branch_city,
                b.address_line  AS branch_address,
                b.phone         AS branch_phone,
                b.manager_id,

                -- Work order / mechanic (may be null)
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
            JOIN customers c       ON c.customer_id = a.customer_id
            JOIN users cu          ON cu.user_id = c.user_id
            LEFT JOIN vehicles v   ON v.vehicle_id = a.vehicle_id
            JOIN services s        ON s.service_id = a.service_id
            JOIN branches b        ON b.branch_id = a.branch_id

            LEFT JOIN work_orders w ON w.appointment_id = a.appointment_id
            LEFT JOIN mechanics mech ON mech.mechanic_id = w.mechanic_id
            LEFT JOIN users mu ON mu.user_id = mech.user_id

            LEFT JOIN users su ON su.user_id = b.manager_id

            WHERE a.appointment_id = :id
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /**
     * Update appointment core fields.
     */
    public function update(int $id, array $data): bool
    {
        $sql = "
            UPDATE appointments
            SET
                branch_id        = :branch_id,
                service_id       = :service_id,
                appointment_date = :appointment_date,
                appointment_time = :appointment_time,
                status           = :status,
                notes            = :notes,
                updated_at       = NOW()
            WHERE appointment_id   = :id
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':branch_id'        => $data['branch_id'],
            ':service_id'       => $data['service_id'],
            ':appointment_date' => $data['appointment_date'],
            ':appointment_time' => $data['appointment_time'],
            ':status'           => $data['status'],
            ':notes'            => $data['notes'],
            ':id'               => $id,
        ]);
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
            case 'confirmed':
                return 'Scheduled';
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
