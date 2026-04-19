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
        $out['to'] = $f['to'] ?? '';
        $out['branch_id'] = isset($f['branch_id']) && $f['branch_id'] !== '' ? (int) $f['branch_id'] : 0;

        if ($out['from'] === '' || $out['to'] === '') {
            $out['to'] = date('Y-m-d');
            $out['from'] = date('Y-m-d', strtotime('-6 months'));
        }

        return $out;
    }

    private function branchWhere(array $filters, string $col = 'a.branch_id'): array
    {
        if (!empty($filters['branch_id'])) {
            return [" AND {$col} = :branch_id ", [':branch_id' => (int) $filters['branch_id']]];
        }
        return ['', []];
    }

    public function branches(): array
    {
        return $this->stmt("SELECT branch_id, name FROM branches ORDER BY name")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

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

        return $this->stmt($sql, array_merge([
            ':from' => $filters['from'],
            ':to' => $filters['to'],
        ], $bp))->fetchAll(PDO::FETCH_ASSOC);
    }

    public function serviceDemandByWeekday(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);
        [$bw, $bp] = $this->branchWhere($filters, 'branch_id');

        $sql = "
            SELECT
                DAYNAME(appointment_date) AS label,
                COUNT(*) AS value,
                WEEKDAY(appointment_date) AS sort_order
            FROM appointments
            WHERE appointment_date BETWEEN :from AND :to
              {$bw}
            GROUP BY DAYNAME(appointment_date), WEEKDAY(appointment_date)
            ORDER BY sort_order
        ";

        return $this->stmt($sql, array_merge([
            ':from' => $filters['from'],
            ':to' => $filters['to'],
        ], $bp))->fetchAll(PDO::FETCH_ASSOC);
    }

    public function mostRebookedServices(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);
        [$bw, $bp] = $this->branchWhere($filters, 'a.branch_id');

        $sql = "
            SELECT
                s.name AS label,
                COUNT(*) AS value
            FROM appointments a
            JOIN services s ON s.service_id = a.service_id
            WHERE a.appointment_date BETWEEN :from AND :to
              {$bw}
            GROUP BY a.customer_id, a.service_id, s.name
            HAVING COUNT(*) >= 2
            ORDER BY value DESC
            LIMIT 10
        ";

        return $this->stmt($sql, array_merge([
            ':from' => $filters['from'],
            ':to' => $filters['to'],
        ], $bp))->fetchAll(PDO::FETCH_ASSOC);
    }

    public function revenueByBranch(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);

        $sql = "
            SELECT b.name AS label, COALESCE(SUM(i.grand_total), 0) AS value
            FROM invoices i
            JOIN work_orders w ON w.work_order_id = i.work_order_id
            JOIN appointments a ON a.appointment_id = w.appointment_id
            JOIN branches b ON b.branch_id = a.branch_id
            WHERE i.status = 'paid'
              AND DATE(i.issued_at) BETWEEN :from AND :to
            GROUP BY b.branch_id
            ORDER BY value DESC
        ";

        return $this->stmt($sql, [
            ':from' => $filters['from'],
            ':to' => $filters['to'],
        ])->fetchAll(PDO::FETCH_ASSOC);
    }

    public function paymentMethodBreakdown(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);

        $sql = "
            SELECT
                p.method AS label,
                COALESCE(SUM(p.amount), 0) AS value
            FROM payments p
            JOIN invoices i ON i.invoice_id = p.invoice_id
            JOIN work_orders w ON w.work_order_id = i.work_order_id
            JOIN appointments a ON a.appointment_id = w.appointment_id
            WHERE DATE(p.payment_date) BETWEEN :from AND :to
              AND (:branch_filter = 0 OR a.branch_id = :branch_id)
            GROUP BY p.method
            ORDER BY value DESC
        ";

        return $this->stmt($sql, [
            ':from' => $filters['from'],
            ':to' => $filters['to'],
            ':branch_filter' => (int) $filters['branch_id'],
            ':branch_id' => (int) $filters['branch_id'],
        ])->fetchAll(PDO::FETCH_ASSOC);
    }

    public function paymentStatusBreakdown(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);

        $sql = "
            SELECT
                p.status AS label,
                COUNT(*) AS value
            FROM payments p
            JOIN invoices i ON i.invoice_id = p.invoice_id
            JOIN work_orders w ON w.work_order_id = i.work_order_id
            JOIN appointments a ON a.appointment_id = w.appointment_id
            WHERE DATE(p.payment_date) BETWEEN :from AND :to
              AND (:branch_filter = 0 OR a.branch_id = :branch_id)
            GROUP BY p.status
            ORDER BY value DESC
        ";

        return $this->stmt($sql, [
            ':from' => $filters['from'],
            ':to' => $filters['to'],
            ':branch_filter' => (int) $filters['branch_id'],
            ':branch_id' => (int) $filters['branch_id'],
        ])->fetchAll(PDO::FETCH_ASSOC);
    }

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

        return $this->stmt($sql, array_merge([
            ':from' => $filters['from'],
            ':to' => $filters['to'],
        ], $bp))->fetchAll(PDO::FETCH_ASSOC);
    }

    public function appointmentsByHour(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);
        [$bw, $bp] = $this->branchWhere($filters, 'branch_id');

        $sql = "
            SELECT LPAD(HOUR(appointment_time), 2, '0') AS label, COUNT(*) AS value
            FROM appointments
            WHERE appointment_date BETWEEN :from AND :to
              {$bw}
            GROUP BY HOUR(appointment_time)
            ORDER BY HOUR(appointment_time)
        ";

        return $this->stmt($sql, array_merge([
            ':from' => $filters['from'],
            ':to' => $filters['to'],
        ], $bp))->fetchAll(PDO::FETCH_ASSOC);
    }

    public function branchCompletedServices(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);

        $sql = "
            SELECT b.name AS label, COUNT(*) AS value
            FROM work_orders w
            JOIN appointments a ON a.appointment_id = w.appointment_id
            JOIN branches b ON b.branch_id = a.branch_id
            WHERE w.status = 'completed'
              AND a.appointment_date BETWEEN :from AND :to
            GROUP BY b.branch_id
            ORDER BY value DESC
        ";

        return $this->stmt($sql, [
            ':from' => $filters['from'],
            ':to' => $filters['to'],
        ])->fetchAll(PDO::FETCH_ASSOC);
    }

    public function branchServiceCoverageMatrix(array $filtersIn): array
    {
        $sql = "
            SELECT
                b.name AS label,
                COUNT(bs.service_id) AS value
            FROM branches b
            LEFT JOIN branch_services bs ON bs.branch_id = b.branch_id
            GROUP BY b.branch_id
            ORDER BY value DESC, b.name ASC
        ";

        return $this->stmt($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function jobsPerMechanic(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);
        [$bw, $bp] = $this->branchWhere($filters, 'a.branch_id');

        $sql = "
            SELECT CONCAT(u.first_name, ' ', u.last_name) AS label, COUNT(*) AS value
            FROM work_orders w
            JOIN mechanics m ON m.mechanic_id = w.mechanic_id
            JOIN users u ON u.user_id = m.user_id
            JOIN appointments a ON a.appointment_id = w.appointment_id
            WHERE w.status = 'completed'
              AND a.appointment_date BETWEEN :from AND :to
              {$bw}
            GROUP BY m.mechanic_id
            ORDER BY value DESC
            LIMIT 10
        ";

        return $this->stmt($sql, array_merge([
            ':from' => $filters['from'],
            ':to' => $filters['to'],
        ], $bp))->fetchAll(PDO::FETCH_ASSOC);
    }

    public function servicesSubmittedByManagers(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);

        $sql = "
            SELECT
                COALESCE(CONCAT(u.first_name, ' ', u.last_name), 'Unknown') AS label,
                COUNT(*) AS value
            FROM services s
            LEFT JOIN users u ON u.user_id = s.submitted_by
            WHERE DATE(s.created_at) BETWEEN :from AND :to
            GROUP BY s.submitted_by
            ORDER BY value DESC
            LIMIT 10
        ";

        return $this->stmt($sql, [
            ':from' => $filters['from'],
            ':to' => $filters['to'],
        ])->fetchAll(PDO::FETCH_ASSOC);
    }

    public function avgJobsPerDayPerMechanic(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);

        $sql = "
            SELECT
                CONCAT(u.first_name, ' ', u.last_name) AS label,
                ROUND(COUNT(*) / NULLIF(COUNT(DISTINCT DATE(w.completed_at)), 0), 2) AS value
            FROM work_orders w
            JOIN mechanics m ON m.mechanic_id = w.mechanic_id
            JOIN users u ON u.user_id = m.user_id
            JOIN appointments a ON a.appointment_id = w.appointment_id
            WHERE w.status = 'completed'
              AND DATE(w.completed_at) BETWEEN :from AND :to
              AND (:branch_filter = 0 OR a.branch_id = :branch_id)
            GROUP BY m.mechanic_id
            ORDER BY value DESC
            LIMIT 10
        ";

        return $this->stmt($sql, [
            ':from' => $filters['from'],
            ':to' => $filters['to'],
            ':branch_filter' => (int) $filters['branch_id'],
            ':branch_id' => (int) $filters['branch_id'],
        ])->fetchAll(PDO::FETCH_ASSOC);
    }

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

        return $this->stmt($sql, array_merge([
            ':from' => $filters['from'],
            ':to' => $filters['to'],
        ], $bp))->fetchAll(PDO::FETCH_ASSOC);
    }

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

        return $this->stmt($sql, [
            ':from' => $filters['from'],
            ':to' => $filters['to'],
        ])->fetchAll(PDO::FETCH_ASSOC);
    }

    public function complaintPriorityAnalysis(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);

        $sql = "
            SELECT cp.priority AS label, COUNT(*) AS value
            FROM complaints cp
            WHERE DATE(cp.created_at) BETWEEN :from AND :to
              AND (:branch_filter = 0 OR cp.branch_id = :branch_id)
            GROUP BY cp.priority
            ORDER BY value DESC
        ";

        return $this->stmt($sql, [
            ':from' => $filters['from'],
            ':to' => $filters['to'],
            ':branch_filter' => (int) $filters['branch_id'],
            ':branch_id' => (int) $filters['branch_id'],
        ])->fetchAll(PDO::FETCH_ASSOC);
    }

    public function exportDataset(string $key, array $filters): array
    {
        return match ($key) {
            'topServices' => $this->topServices($filters),
            'weekdayDemand', 'serviceDemandByWeekday' => $this->serviceDemandByWeekday($filters),
            'mostRebookedServices' => $this->mostRebookedServices($filters),

            'revenueByBranch' => $this->revenueByBranch($filters),
            'paymentMethodBreakdown' => $this->paymentMethodBreakdown($filters),
            'paymentStatusBreakdown' => $this->paymentStatusBreakdown($filters),

            'appointmentStatusCounts' => $this->appointmentStatusCounts($filters),
            'appointmentsByHour' => $this->appointmentsByHour($filters),

            'branchCompletedServices' => $this->branchCompletedServices($filters),
            'branchServiceCoverageMatrix' => $this->branchServiceCoverageMatrix($filters),

            'jobsPerMechanic' => $this->jobsPerMechanic($filters),
            'servicesSubmittedByManagers' => $this->servicesSubmittedByManagers($filters),
            'avgJobsPerDayPerMechanic' => $this->avgJobsPerDayPerMechanic($filters),

            'ratingDistribution' => $this->ratingDistribution($filters),

            'approvalStatusCounts' => $this->approvalStatusCounts($filters),

            'complaintPriorityAnalysis' => $this->complaintPriorityAnalysis($filters),

            default => [],
        };
    }
}
