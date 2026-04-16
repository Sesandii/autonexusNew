<?php
namespace app\model\Manager;

use PDO;

class WorkOrderModel
{
    public function __construct()
    {
        // Model doesn't need db passed - using the db() function from Database.php
    }

    /**
     * Get all work orders with related details
     */
    public function getAllWorkOrders(): array
    {
        $sql = "
            SELECT 
                wo.work_order_id,
                wo.job_start_time,
                wo.started_at,
                wo.completed_at,
                wo.service_summary,
                wo.total_cost,
                wo.status as work_order_status,
                
                -- Vehicle info
                v.vehicle_id,
                v.license_plate,
                v.make,
                v.model,
                v.year,
                v.color,
                
                -- Service info
                s.service_id,
                s.name as service_name,
                s.service_code,
                
                -- Mechanic info
                m.mechanic_id,
                m.mechanic_code,
                u_mech.first_name as mechanic_first_name,
                u_mech.last_name as mechanic_last_name,
                u_mech.username as mechanic_username,
                
                -- Supervisor info
                sup.supervisor_id,
                sup.supervisor_code,
                u_sup.first_name as supervisor_first_name,
                u_sup.last_name as supervisor_last_name,
                
                -- Appointment info
                a.appointment_id,
                a.appointment_date,
                a.appointment_time,
                a.status as appointment_status,
                
                -- Customer info (via appointment -> customer -> user)
                u_cust.first_name as customer_first_name,
                u_cust.last_name as customer_last_name
                
            FROM work_orders wo
            LEFT JOIN appointments a ON wo.appointment_id = a.appointment_id
            LEFT JOIN vehicles v ON a.vehicle_id = v.vehicle_id
            LEFT JOIN services s ON a.service_id = s.service_id
            LEFT JOIN mechanics m ON wo.mechanic_id = m.mechanic_id
            LEFT JOIN users u_mech ON m.user_id = u_mech.user_id
            LEFT JOIN supervisors sup ON wo.supervisor_id = sup.supervisor_id
            LEFT JOIN users u_sup ON sup.user_id = u_sup.user_id
            LEFT JOIN users u_cust ON a.customer_id = u_cust.user_id
            ORDER BY wo.work_order_id DESC
        ";
        
        $stmt = db()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get single work order by ID
     */
    public function getWorkOrderById(int $id): ?array
    {
        $sql = "
            SELECT 
                wo.work_order_id,
                wo.job_start_time,
                wo.started_at,
                wo.completed_at,
                wo.service_summary,
                wo.total_cost,
                wo.status as work_order_status,
                
                v.vehicle_id,
                v.license_plate,
                v.make,
                v.model,
                v.year,
                v.color,
                
                s.service_id,
                s.name as service_name,
                s.service_code,
                
                m.mechanic_id,
                m.mechanic_code,
                u_mech.first_name as mechanic_first_name,
                u_mech.last_name as mechanic_last_name,
                
                sup.supervisor_id,
                sup.supervisor_code,
                u_sup.first_name as supervisor_first_name,
                u_sup.last_name as supervisor_last_name,
                
                a.appointment_id,
                a.appointment_date,
                a.appointment_time,
                
                u_cust.first_name as customer_first_name,
                u_cust.last_name as customer_last_name
                
            FROM work_orders wo
            LEFT JOIN appointments a ON wo.appointment_id = a.appointment_id
            LEFT JOIN vehicles v ON a.vehicle_id = v.vehicle_id
            LEFT JOIN services s ON a.service_id = s.service_id
            LEFT JOIN mechanics m ON wo.mechanic_id = m.mechanic_id
            LEFT JOIN users u_mech ON m.user_id = u_mech.user_id
            LEFT JOIN supervisors sup ON wo.supervisor_id = sup.supervisor_id
            LEFT JOIN users u_sup ON sup.user_id = u_sup.user_id
            LEFT JOIN users u_cust ON a.customer_id = u_cust.user_id
            WHERE wo.work_order_id = :id
        ";
        
        $stmt = db()->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Get work orders by status
     */
    public function getWorkOrdersByStatus(string $status): array
    {
        $sql = "
            SELECT 
                wo.work_order_id,
                wo.total_cost,
                wo.status as work_order_status,
                v.license_plate,
                v.make,
                v.model,
                s.name as service_name,
                u_mech.first_name as mechanic_first_name,
                u_mech.last_name as mechanic_last_name,
                u_sup.first_name as supervisor_first_name,
                u_sup.last_name as supervisor_last_name
            FROM work_orders wo
            LEFT JOIN appointments a ON wo.appointment_id = a.appointment_id
            LEFT JOIN vehicles v ON a.vehicle_id = v.vehicle_id
            LEFT JOIN services s ON a.service_id = s.service_id
            LEFT JOIN mechanics m ON wo.mechanic_id = m.mechanic_id
            LEFT JOIN users u_mech ON m.user_id = u_mech.user_id
            LEFT JOIN supervisors sup ON wo.supervisor_id = sup.supervisor_id
            LEFT JOIN users u_sup ON sup.user_id = u_sup.user_id
            WHERE wo.status = :status
            ORDER BY wo.work_order_id DESC
        ";
        
        $stmt = db()->prepare($sql);
        $stmt->execute([':status' => $status]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get status badge class for styling
     */
     public function getStatusBadgeClass(string $status): string
    {
        return match(strtolower($status)) {
            'open' => 'status-open',
            'in_progress' => 'status-in-progress',
            'on_hold' => 'status-on-hold',
            'completed' => 'status-completed',
            default => 'badge-light'
        };
    }
    /**
     * Get status display text
     */
    public function getStatusDisplay(string $status): string
    {
        return match(strtolower($status)) {
            'open' => 'Open',
            'in_progress' => 'In Progress',
            'on_hold' => 'On Hold',
            'completed' => 'Completed',
            default => ucfirst($status)
        };
    }

    public function getWorkOrderDetail(int $workOrderId): ?array
    {
        // ── 1. Work Order + Appointment + Vehicle + Service + Mechanic + Supervisor ──
        $sql = "
            SELECT
                -- work order
                wo.work_order_id,
                wo.status           AS wo_status,
                wo.job_start_time,
                wo.started_at,
                wo.completed_at,
                wo.service_summary,
                wo.total_cost,
                wo.paused_remaining_seconds,
 
                -- appointment
                a.appointment_id,
                a.appointment_date,
                a.appointment_time,
                a.status            AS appt_status,
                a.notes             AS appt_notes,
                a.created_at        AS appt_created_at,
                a.branch_id,
 
                -- vehicle
                v.vehicle_id,
                v.vehicle_code,
                v.vin,
                v.license_plate,
                v.make,
                v.model,
                v.year,
                v.color,
                v.current_mileage,
                v.last_service_mileage,
                v.service_interval_km,
 
                -- service
                s.service_id,
                s.service_code,
                s.name              AS service_name,
                s.description       AS service_description,
                s.base_duration_minutes,
                s.default_price,
 
                -- mechanic user
                mu.user_id          AS mechanic_user_id,
                mu.first_name       AS mechanic_first_name,
                mu.last_name        AS mechanic_last_name,
                mu.email            AS mechanic_email,
                mu.phone            AS mechanic_phone,
                me.mechanic_code,
                me.specialization,
                me.experience_years,
 
                -- supervisor user
                su.user_id          AS supervisor_user_id,
                su.first_name       AS supervisor_first_name,
                su.last_name        AS supervisor_last_name,
                su.email            AS supervisor_email,
                sv.supervisor_code,
 
                -- customer user
                cu.user_id          AS customer_user_id,
                cu.first_name       AS customer_first_name,
                cu.last_name        AS customer_last_name,
                cu.email            AS customer_email,
                cu.phone            AS customer_phone,
                cu.street_address,
                cu.city,
                cu.state,
                c.customer_code
 
            FROM work_orders wo
 
            -- appointment
            LEFT JOIN appointments a  ON wo.appointment_id  = a.appointment_id
 
            -- vehicle
            LEFT JOIN vehicles v      ON a.vehicle_id       = v.vehicle_id
 
            -- service
            LEFT JOIN services s      ON a.service_id       = s.service_id
 
            -- mechanic
            LEFT JOIN mechanics me    ON wo.mechanic_id     = me.mechanic_id
            LEFT JOIN users mu        ON me.user_id         = mu.user_id
 
            -- supervisor
            LEFT JOIN supervisors sv  ON wo.supervisor_id   = sv.supervisor_id
            LEFT JOIN users su        ON sv.user_id         = su.user_id
 
            -- customer
            LEFT JOIN customers c     ON a.customer_id      = c.customer_id
            LEFT JOIN users cu        ON c.user_id          = cu.user_id
 
            WHERE wo.work_order_id = :id
            LIMIT 1
        ";
 
        $stmt = db()->prepare($sql);
        $stmt->execute(['id' => $workOrderId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
 
        if (!$row) return null;
 
        // ── 2. Complaints linked to this appointment ──
        $complaints = [];
        if ($row['appointment_id']) {
            $cSql = "
                SELECT
                    co.complaint_id,
                    co.subject,
                    co.description,
                    co.priority,
                    co.status       AS complaint_status,
                    co.created_at   AS complaint_created_at,
                    co.resolved_at,
                    au.first_name   AS assigned_first_name,
                    au.last_name    AS assigned_last_name
                FROM complaints co
                LEFT JOIN users au ON co.assigned_to_user_id = au.user_id
                WHERE co.appointment_id = :appt_id
                ORDER BY co.created_at DESC
            ";
            $cStmt = db()->prepare($cSql);
            $cStmt->execute(['appt_id' => $row['appointment_id']]);
            $complaints = $cStmt->fetchAll(PDO::FETCH_ASSOC);
        }
 
        // ── 3. Final report (if completed) ──
        $report = null;
        $rStmt = db()->prepare("
            SELECT report_id, report_details, recommendations, created_at
            FROM final_reports
            WHERE work_order_id = :id
            LIMIT 1
        ");
        $rStmt->execute(['id' => $workOrderId]);
        $report = $rStmt->fetch(PDO::FETCH_ASSOC) ?: null;
 
        // ── Build structured result ──
        return [
            'work_order' => [
                'work_order_id'            => $row['work_order_id'],
                'status'                   => $row['wo_status'],
                'job_start_time'           => $row['job_start_time'],
                'started_at'               => $row['started_at'],
                'completed_at'             => $row['completed_at'],
                'service_summary'          => $row['service_summary'],
                'total_cost'               => $row['total_cost'],
                'paused_remaining_seconds' => $row['paused_remaining_seconds'],
            ],
            'appointment' => [
                'appointment_id'   => $row['appointment_id'],
                'appointment_date' => $row['appointment_date'],
                'appointment_time' => $row['appointment_time'],
                'status'           => $row['appt_status'],
                'notes'            => $row['appt_notes'],
                'created_at'       => $row['appt_created_at'],
                'branch_id'        => $row['branch_id'],
            ],
            'vehicle' => [
                'vehicle_id'            => $row['vehicle_id'],
                'vehicle_code'          => $row['vehicle_code'],
                'vin'                   => $row['vin'],
                'license_plate'         => $row['license_plate'],
                'make'                  => $row['make'],
                'model'                 => $row['model'],
                'year'                  => $row['year'],
                'color'                 => $row['color'],
                'current_mileage'       => $row['current_mileage'],
                'last_service_mileage'  => $row['last_service_mileage'],
                'service_interval_km'   => $row['service_interval_km'],
            ],
            'service' => [
                'service_id'           => $row['service_id'],
                'service_code'         => $row['service_code'],
                'name'                 => $row['service_name'],
                'description'          => $row['service_description'],
                'base_duration_minutes'=> $row['base_duration_minutes'],
                'default_price'        => $row['default_price'],
            ],
            'mechanic' => [
                'user_id'          => $row['mechanic_user_id'],
                'first_name'       => $row['mechanic_first_name'],
                'last_name'        => $row['mechanic_last_name'],
                'email'            => $row['mechanic_email'],
                'phone'            => $row['mechanic_phone'],
                'mechanic_code'    => $row['mechanic_code'],
                'specialization'   => $row['specialization'],
                'experience_years' => $row['experience_years'],
            ],
            'supervisor' => [
                'user_id'         => $row['supervisor_user_id'],
                'first_name'      => $row['supervisor_first_name'],
                'last_name'       => $row['supervisor_last_name'],
                'email'           => $row['supervisor_email'],
                'supervisor_code' => $row['supervisor_code'],
            ],
            'customer' => [
                'user_id'        => $row['customer_user_id'],
                'first_name'     => $row['customer_first_name'],
                'last_name'      => $row['customer_last_name'],
                'email'          => $row['customer_email'],
                'phone'          => $row['customer_phone'],
                'street_address' => $row['street_address'],
                'city'           => $row['city'],
                'state'          => $row['state'],
                'customer_code'  => $row['customer_code'],
            ],
            'complaints' => $complaints,
            'report'     => $report,
        ];
    }
}