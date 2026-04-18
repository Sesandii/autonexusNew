<?php

namespace app\model\Manager;

use PDO;

class ScheduleModel
{
    protected PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getTeamMembers(int $branchId): array
    {
        $sql = "
            SELECT
                u.user_id,
                u.first_name,
                u.last_name,
                u.email,
                u.phone,
                u.role,
                u.status as user_status,

                COALESCE(m.branch_id, s.branch_id, r.branch_id) AS branch_id,
                
                m.specialization,
                m.experience_years,
                m.mechanic_code,
                m.status as mechanic_status,
                
                s.supervisor_code,
                
                r.receptionist_code,
                
                COUNT(wo.work_order_id) AS tasks_today

            FROM users u

            LEFT JOIN mechanics m
                ON u.user_id = m.user_id AND u.role = 'mechanic'

            LEFT JOIN supervisors s
                ON u.user_id = s.user_id AND u.role = 'supervisor'
                
            LEFT JOIN receptionists r
                ON u.user_id = r.user_id AND u.role = 'receptionist'

            LEFT JOIN work_orders wo
                ON wo.mechanic_id = m.mechanic_id
                AND DATE(wo.started_at) = CURDATE()

            WHERE u.role IN ('mechanic', 'supervisor', 'receptionist')
              AND (
                    m.branch_id = :branch_mech
                    OR s.branch_id = :branch_sup
                    OR r.branch_id = :branch_rec
                  )
              AND u.status = 'active'

            GROUP BY u.user_id
            ORDER BY 
                CASE u.role
                    WHEN 'supervisor' THEN 1
                    WHEN 'mechanic' THEN 2
                    WHEN 'receptionist' THEN 3
                END,
                u.first_name
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'branch_mech' => $branchId,
            'branch_sup'  => $branchId,
            'branch_rec'  => $branchId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all staff for add member page
    public function getAllStaff(string $search = '', int $managerBranchId = null): array
    {
        $sql = "
            SELECT 
                u.user_id,
                u.first_name,
                u.last_name,
                u.email,
                u.phone,
                u.role,
                COALESCE(m.branch_id, s.branch_id, r.branch_id) AS current_branch,
                CASE 
                    WHEN COALESCE(m.branch_id, s.branch_id, r.branch_id) = :branch_id THEN 1
                    ELSE 0
                END AS in_my_team
            FROM users u
            LEFT JOIN mechanics m ON u.user_id = m.user_id AND u.role = 'mechanic'
            LEFT JOIN supervisors s ON u.user_id = s.user_id AND u.role = 'supervisor'
            LEFT JOIN receptionists r ON u.user_id = r.user_id AND u.role = 'receptionist'
            WHERE u.role IN ('mechanic', 'supervisor', 'receptionist')
            AND u.status = 'active'
        ";
        
        if (!empty($search)) {
            $sql .= " AND (u.first_name LIKE :search1 OR u.last_name LIKE :search2)";
        }
        
        $sql .= " ORDER BY in_my_team DESC, u.first_name";
        
        $stmt = $this->db->prepare($sql);
        
        $params = ['branch_id' => $managerBranchId];
        if (!empty($search)) {
            $params['search1'] = "%$search%";
            $params['search2'] = "%$search%";
        }
        
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ONLY ONE assignToBranch method - keep this one!
    public function assignToBranch(int $userId, string $role, int $branchId): bool
    {
        if ($role === 'mechanic') {
            $sql = "UPDATE mechanics SET branch_id = ? WHERE user_id = ?";
        } else if ($role === 'supervisor') {
            $sql = "UPDATE supervisors SET branch_id = ? WHERE user_id = ?";
        } else if ($role === 'receptionist') {
            $sql = "UPDATE receptionists SET branch_id = ? WHERE user_id = ?";
        } else {
            return false;
        }
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$branchId, $userId]);
    }

    public function getEmployeeById(int $userId): ?array
    {
        $sql = "
            SELECT 
                u.user_id,
                u.first_name,
                u.last_name,
                u.email,
                u.phone,
                u.role,
                COALESCE(m.branch_id, s.branch_id, r.branch_id) AS branch_id,
                m.specialization,
                m.experience_years,
                m.mechanic_code,
                s.supervisor_code,
                r.receptionist_code
            FROM users u
            LEFT JOIN mechanics m ON u.user_id = m.user_id
            LEFT JOIN supervisors s ON u.user_id = s.user_id
            LEFT JOIN receptionists r ON u.user_id = r.user_id
            WHERE u.user_id = :user_id
            LIMIT 1
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function getMechanicWorkOrders(int $userId): array
    {
        $sql = "
            SELECT 
                wo.work_order_id,
                wo.status,
                wo.job_start_time,
                wo.started_at,
                wo.completed_at,
                wo.total_cost,
                wo.service_summary,
                a.appointment_date,
                a.appointment_time,
                v.license_plate,
                v.make,
                v.model,
                v.year,
                s.name AS service_name,
                u.first_name AS supervisor_first_name,
                u.last_name AS supervisor_last_name
            FROM work_orders wo
            INNER JOIN mechanics m ON wo.mechanic_id = m.mechanic_id
            INNER JOIN users mu ON m.user_id = mu.user_id
            LEFT JOIN appointments a ON wo.appointment_id = a.appointment_id
            LEFT JOIN vehicles v ON a.vehicle_id = v.vehicle_id
            LEFT JOIN services s ON a.service_id = s.service_id
            LEFT JOIN supervisors sup ON wo.supervisor_id = sup.supervisor_id
            LEFT JOIN users u ON sup.user_id = u.user_id
            WHERE mu.user_id = :user_id
            ORDER BY wo.job_start_time DESC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSupervisorAppointments(int $userId): array
    {
        $sql = "
            SELECT 
                a.appointment_id,
                a.appointment_date,
                a.appointment_time,
                a.status,
                a.notes,
                v.license_plate,
                v.make,
                v.model,
                v.year,
                s.name AS service_name,
                cu.first_name AS customer_first_name,
                cu.last_name AS customer_last_name
            FROM appointments a
            INNER JOIN supervisors sup ON a.assigned_to = sup.supervisor_id
            INNER JOIN users u ON sup.user_id = u.user_id
            LEFT JOIN vehicles v ON a.vehicle_id = v.vehicle_id
            LEFT JOIN services s ON a.service_id = s.service_id
            LEFT JOIN customers c ON a.customer_id = c.customer_id
            LEFT JOIN users cu ON c.user_id = cu.user_id
            WHERE u.user_id = :user_id
            ORDER BY a.appointment_date DESC, a.appointment_time ASC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}