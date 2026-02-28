<?php
declare(strict_types=1);

namespace app\model\admin;

use PDO;

class Reports
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? db();
    }

    /* -----------------------------
     * Helpers
     * ----------------------------- */
    private function stmt(string $sql, array $params = [])
    {
        $st = $this->pdo->prepare($sql);
        $st->execute($params);
        return $st;
    }

    private function normalizeFilters(array $f): array
    {
        $out = [];
        $out['from'] = $f['from'] ?? '';
        $out['to']   = $f['to']   ?? '';
        $out['branch_id'] = isset($f['branch_id']) && $f['branch_id'] !== '' ? (int)$f['branch_id'] : 0;

        // group for trends: day|month
        $out['group'] = in_array(($f['group'] ?? 'month'), ['day','month'], true) ? $f['group'] : 'month';

        // fallback: last 6 months
        if ($out['from'] === '' || $out['to'] === '') {
            $out['to'] = date('Y-m-d');
            $out['from'] = date('Y-m-d', strtotime('-6 months'));
        }
        return $out;
    }

    private function branchWhere(array $filters, string $col = 'a.branch_id'): array
    {
        if (!empty($filters['branch_id'])) {
            return [" AND {$col} = :branch_id ", [':branch_id' => (int)$filters['branch_id']]];
        }
        return ['', []];
    }

    /* -----------------------------
     * Dropdown data
     * ----------------------------- */
    public function branches(): array
    {
        return $this->stmt("SELECT branch_id, name FROM branches ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function serviceTypes(): array
    {
        return $this->stmt("SELECT type_id, type_name FROM service_types ORDER BY type_name")->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =========================================================
     * 1) SERVICE PERFORMANCE REPORTS
     * ========================================================= */

    // Top requested services (by completed work_orders)
    public function topServices(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);
        [$bw, $bp] = $this->branchWhere($filters, 'a.branch_id');

        $sql = "
            SELECT s.name AS label, COUNT(*) AS value
            FROM work_orders w
            JOIN appointments a ON a.appointment_id = w.appointment_id
            JOIN services s ON s.service_id = a.service_id
            WHERE w.status = 'completed'
              AND a.appointment_date BETWEEN :from AND :to
              {$bw}
            GROUP BY s.service_id
            ORDER BY value DESC
            LIMIT 10
        ";

        $params = array_merge([':from'=>$filters['from'], ':to'=>$filters['to']], $bp);
        return $this->stmt($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Service demand trend (count per month/day)
    public function serviceTrend(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);
        [$bw, $bp] = $this->branchWhere($filters, 'a.branch_id');

        $groupExpr = $filters['group'] === 'day'
            ? "DATE(a.appointment_date)"
            : "DATE_FORMAT(a.appointment_date,'%Y-%m')";

        $sql = "
            SELECT {$groupExpr} AS label, COUNT(*) AS value
            FROM work_orders w
            JOIN appointments a ON a.appointment_id = w.appointment_id
            WHERE w.status = 'completed'
              AND a.appointment_date BETWEEN :from AND :to
              {$bw}
            GROUP BY label
            ORDER BY label
        ";

        $params = array_merge([':from'=>$filters['from'], ':to'=>$filters['to']], $bp);
        return $this->stmt($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Donut: completed services by service type (Maintenance/Brakes/etc)
    public function serviceTypeDistribution(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);
        [$bw, $bp] = $this->branchWhere($filters, 'a.branch_id');

        $sql = "
            SELECT st.type_name AS label, COUNT(*) AS value
            FROM work_orders w
            JOIN appointments a ON a.appointment_id = w.appointment_id
            JOIN services s ON s.service_id = a.service_id
            JOIN service_types st ON st.type_id = s.type_id
            WHERE w.status = 'completed'
              AND a.appointment_date BETWEEN :from AND :to
              {$bw}
            GROUP BY st.type_id
            ORDER BY value DESC
        ";

        $params = array_merge([':from'=>$filters['from'], ':to'=>$filters['to']], $bp);
        return $this->stmt($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Avg completion time (minutes) = completed_at - started_at
    public function avgCompletionMinutes(array $filtersIn): float
    {
        $filters = $this->normalizeFilters($filtersIn);
        [$bw, $bp] = $this->branchWhere($filters, 'a.branch_id');

        $sql = "
            SELECT COALESCE(AVG(TIMESTAMPDIFF(MINUTE, w.started_at, w.completed_at)),0) AS avg_mins
            FROM work_orders w
            JOIN appointments a ON a.appointment_id = w.appointment_id
            WHERE w.status='completed'
              AND w.started_at IS NOT NULL
              AND w.completed_at IS NOT NULL
              AND a.appointment_date BETWEEN :from AND :to
              {$bw}
        ";

        $params = array_merge([':from'=>$filters['from'], ':to'=>$filters['to']], $bp);
        return (float)($this->stmt($sql,$params)->fetchColumn() ?? 0);
    }

    /* =========================================================
     * 2) REVENUE & FINANCIAL REPORTS
     * ========================================================= */

    // revenue trend from invoices (paid only)
    public function revenueTrend(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);
        [$bw, $bp] = $this->branchWhere($filters, 'a.branch_id');

        $groupExpr = $filters['group'] === 'day'
            ? "DATE(i.issued_at)"
            : "DATE_FORMAT(i.issued_at,'%Y-%m')";

        $sql = "
            SELECT {$groupExpr} AS label, COALESCE(SUM(i.grand_total),0) AS value
            FROM invoices i
            JOIN work_orders w ON w.work_order_id = i.work_order_id
            JOIN appointments a ON a.appointment_id = w.appointment_id
            WHERE i.status='paid'
              AND DATE(i.issued_at) BETWEEN :from AND :to
              {$bw}
            GROUP BY label
            ORDER BY label
        ";

        $params = array_merge([':from'=>$filters['from'], ':to'=>$filters['to']], $bp);
        return $this->stmt($sql,$params)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function revenueByBranch(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);

        $sql = "
            SELECT b.name AS label, COALESCE(SUM(i.grand_total),0) AS value
            FROM invoices i
            JOIN work_orders w ON w.work_order_id = i.work_order_id
            JOIN appointments a ON a.appointment_id = w.appointment_id
            JOIN branches b ON b.branch_id = a.branch_id
            WHERE i.status='paid'
              AND DATE(i.issued_at) BETWEEN :from AND :to
            GROUP BY b.branch_id
            ORDER BY value DESC
        ";

        return $this->stmt($sql, [':from'=>$filters['from'], ':to'=>$filters['to']])->fetchAll(PDO::FETCH_ASSOC);
    }

    public function avgInvoiceValue(array $filtersIn): float
    {
        $filters = $this->normalizeFilters($filtersIn);
        [$bw, $bp] = $this->branchWhere($filters, 'a.branch_id');

        $sql = "
            SELECT COALESCE(AVG(i.grand_total),0)
            FROM invoices i
            JOIN work_orders w ON w.work_order_id = i.work_order_id
            JOIN appointments a ON a.appointment_id = w.appointment_id
            WHERE i.status='paid'
              AND DATE(i.issued_at) BETWEEN :from AND :to
              {$bw}
        ";

        $params = array_merge([':from'=>$filters['from'], ':to'=>$filters['to']], $bp);
        return (float)($this->stmt($sql,$params)->fetchColumn() ?? 0);
    }

    public function revenueByServiceType(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);
        [$bw, $bp] = $this->branchWhere($filters, 'a.branch_id');

        $sql = "
            SELECT st.type_name AS label, COALESCE(SUM(i.grand_total),0) AS value
            FROM invoices i
            JOIN work_orders w ON w.work_order_id = i.work_order_id
            JOIN appointments a ON a.appointment_id = w.appointment_id
            JOIN services s ON s.service_id = a.service_id
            JOIN service_types st ON st.type_id = s.type_id
            WHERE i.status='paid'
              AND DATE(i.issued_at) BETWEEN :from AND :to
              {$bw}
            GROUP BY st.type_id
            ORDER BY value DESC
        ";

        $params = array_merge([':from'=>$filters['from'], ':to'=>$filters['to']], $bp);
        return $this->stmt($sql,$params)->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =========================================================
     * 3) APPOINTMENT & WORKLOAD REPORTS
     * ========================================================= */

    public function appointmentStatusCounts(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);
        [$bw, $bp] = $this->branchWhere($filters, 'branch_id');

        $sql = "
            SELECT status AS label, COUNT(*) AS value
            FROM appointments
            WHERE appointment_date BETWEEN :from AND :to
              {$bw}
            GROUP BY status
            ORDER BY value DESC
        ";
        $params = array_merge([':from'=>$filters['from'], ':to'=>$filters['to']], $bp);
        return $this->stmt($sql,$params)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function appointmentsByHour(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);
        [$bw, $bp] = $this->branchWhere($filters, 'branch_id');

        $sql = "
            SELECT LPAD(HOUR(appointment_time),2,'0') AS label, COUNT(*) AS value
            FROM appointments
            WHERE appointment_date BETWEEN :from AND :to
              {$bw}
            GROUP BY HOUR(appointment_time)
            ORDER BY HOUR(appointment_time)
        ";
        $params = array_merge([':from'=>$filters['from'], ':to'=>$filters['to']], $bp);
        return $this->stmt($sql,$params)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function appointmentsTrend(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);
        [$bw, $bp] = $this->branchWhere($filters, 'branch_id');

        $groupExpr = $filters['group'] === 'day'
            ? "DATE(appointment_date)"
            : "DATE_FORMAT(appointment_date,'%Y-%m')";

        $sql = "
            SELECT {$groupExpr} AS label, COUNT(*) AS value
            FROM appointments
            WHERE appointment_date BETWEEN :from AND :to
              {$bw}
            GROUP BY label
            ORDER BY label
        ";
        $params = array_merge([':from'=>$filters['from'], ':to'=>$filters['to']], $bp);
        return $this->stmt($sql,$params)->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =========================================================
     * 4) BRANCH PERFORMANCE REPORTS
     * ========================================================= */

    public function branchCompletedServices(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);

        $sql = "
            SELECT b.name AS label, COUNT(*) AS value
            FROM work_orders w
            JOIN appointments a ON a.appointment_id = w.appointment_id
            JOIN branches b ON b.branch_id = a.branch_id
            WHERE w.status='completed'
              AND a.appointment_date BETWEEN :from AND :to
            GROUP BY b.branch_id
            ORDER BY value DESC
        ";

        return $this->stmt($sql, [':from'=>$filters['from'], ':to'=>$filters['to']])->fetchAll(PDO::FETCH_ASSOC);
    }

    public function branchAvgRating(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);

        $sql = "
            SELECT b.name AS label, COALESCE(AVG(f.rating),0) AS value
            FROM feedback f
            JOIN appointments a ON a.appointment_id = f.appointment_id
            JOIN branches b ON b.branch_id = a.branch_id
            WHERE DATE(f.created_at) BETWEEN :from AND :to
            GROUP BY b.branch_id
            ORDER BY value DESC
        ";

        return $this->stmt($sql, [':from'=>$filters['from'], ':to'=>$filters['to']])->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =========================================================
     * 5) STAFF / MANAGER PERFORMANCE REPORTS
     * ========================================================= */

    // Mechanic productivity (jobs completed)
    public function jobsPerMechanic(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);
        [$bw, $bp] = $this->branchWhere($filters, 'a.branch_id');

        $sql = "
            SELECT CONCAT(u.first_name,' ',u.last_name) AS label, COUNT(*) AS value
            FROM work_orders w
            JOIN mechanics m ON m.mechanic_id = w.mechanic_id
            JOIN users u ON u.user_id = m.user_id
            JOIN appointments a ON a.appointment_id = w.appointment_id
            WHERE w.status='completed'
              AND a.appointment_date BETWEEN :from AND :to
              {$bw}
            GROUP BY m.mechanic_id
            ORDER BY value DESC
            LIMIT 10
        ";

        $params = array_merge([':from'=>$filters['from'], ':to'=>$filters['to']], $bp);
        return $this->stmt($sql,$params)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function servicesSubmittedByManagers(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);

        // submitted_by is users.user_id (manager user)
        $sql = "
            SELECT
                COALESCE(CONCAT(u.first_name,' ',u.last_name), 'Unknown') AS label,
                COUNT(*) AS value
            FROM services s
            LEFT JOIN users u ON u.user_id = s.submitted_by
            WHERE DATE(s.created_at) BETWEEN :from AND :to
            GROUP BY s.submitted_by
            ORDER BY value DESC
            LIMIT 10
        ";

        return $this->stmt($sql, [':from'=>$filters['from'], ':to'=>$filters['to']])->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =========================================================
     * 6) CUSTOMER & FEEDBACK REPORTS
     * ========================================================= */

    public function ratingDistribution(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);
        [$bw, $bp] = $this->branchWhere($filters, 'a.branch_id');

        $sql = "
            SELECT f.rating AS label, COUNT(*) AS value
            FROM feedback f
            JOIN appointments a ON a.appointment_id = f.appointment_id
            WHERE DATE(f.created_at) BETWEEN :from AND :to
              {$bw}
            GROUP BY f.rating
            ORDER BY f.rating DESC
        ";

        $params = array_merge([':from'=>$filters['from'], ':to'=>$filters['to']], $bp);
        return $this->stmt($sql,$params)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function feedbackTrend(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);
        [$bw, $bp] = $this->branchWhere($filters, 'a.branch_id');

        $groupExpr = $filters['group'] === 'day'
            ? "DATE(f.created_at)"
            : "DATE_FORMAT(f.created_at,'%Y-%m')";

        $sql = "
            SELECT {$groupExpr} AS label, COUNT(*) AS value
            FROM feedback f
            JOIN appointments a ON a.appointment_id = f.appointment_id
            WHERE DATE(f.created_at) BETWEEN :from AND :to
              {$bw}
            GROUP BY label
            ORDER BY label
        ";

        $params = array_merge([':from'=>$filters['from'], ':to'=>$filters['to']], $bp);
        return $this->stmt($sql,$params)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function lowestRatedServices(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);
        [$bw, $bp] = $this->branchWhere($filters, 'a.branch_id');

        $sql = "
            SELECT s.name AS label, COALESCE(AVG(f.rating),0) AS value
            FROM feedback f
            JOIN appointments a ON a.appointment_id = f.appointment_id
            JOIN services s ON s.service_id = a.service_id
            WHERE DATE(f.created_at) BETWEEN :from AND :to
              {$bw}
            GROUP BY s.service_id
            HAVING COUNT(*) >= 2
            ORDER BY value ASC
            LIMIT 10
        ";

        $params = array_merge([':from'=>$filters['from'], ':to'=>$filters['to']], $bp);
        return $this->stmt($sql,$params)->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =========================================================
     * 7) SERVICE APPROVAL REPORTS
     * ========================================================= */

    public function approvalStatusCounts(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);

        $sql = "
            SELECT status AS label, COUNT(*) AS value
            FROM services
            WHERE DATE(created_at) BETWEEN :from AND :to
            GROUP BY status
            ORDER BY value DESC
        ";
        return $this->stmt($sql, [':from'=>$filters['from'], ':to'=>$filters['to']])->fetchAll(PDO::FETCH_ASSOC);
    }

    public function avgApprovalHours(array $filtersIn): float
    {
        $filters = $this->normalizeFilters($filtersIn);

        $sql = "
            SELECT COALESCE(AVG(TIMESTAMPDIFF(HOUR, created_at, approved_at)),0)
            FROM services
            WHERE approved_at IS NOT NULL
              AND DATE(created_at) BETWEEN :from AND :to
        ";
        return (float)($this->stmt($sql, [':from'=>$filters['from'], ':to'=>$filters['to']])->fetchColumn() ?? 0);
    }

    /* =========================================================
     * Export: simple dataset by key
     * ========================================================= */
    public function exportDataset(string $key, array $filters): array
    {
        return match ($key) {
            'topServices' => $this->topServices($filters),
            'serviceTrend' => $this->serviceTrend($filters),
            'serviceTypeDistribution' => $this->serviceTypeDistribution($filters),
            'revenueTrend' => $this->revenueTrend($filters),
            'revenueByBranch' => $this->revenueByBranch($filters),
            'revenueByServiceType' => $this->revenueByServiceType($filters),
            'appointmentStatusCounts' => $this->appointmentStatusCounts($filters),
            'appointmentsByHour' => $this->appointmentsByHour($filters),
            'appointmentsTrend' => $this->appointmentsTrend($filters),
            'branchCompletedServices' => $this->branchCompletedServices($filters),
            'branchAvgRating' => $this->branchAvgRating($filters),
            'jobsPerMechanic' => $this->jobsPerMechanic($filters),
            'servicesSubmittedByManagers' => $this->servicesSubmittedByManagers($filters),
            'ratingDistribution' => $this->ratingDistribution($filters),
            'feedbackTrend' => $this->feedbackTrend($filters),
            'lowestRatedServices' => $this->lowestRatedServices($filters),
            'approvalStatusCounts' => $this->approvalStatusCounts($filters),
            default => [],
        };
    }
}
