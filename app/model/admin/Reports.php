<?php
declare(strict_types=1);

namespace app\model\admin;

use PDO;

class Reports
{
    private PDO $pdo;

    // Initialize model dependencies and database access.
    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? db();
    }

    // Handle stmt operation.
    private function stmt(string $sql, array $params = [])
    {
        $st = $this->pdo->prepare($sql);
        $st->execute($params);
        return $st;
    }

    // Handle normalizeFilters operation.
    private function normalizeFilters(array $f): array
    {
        $out = [];
        $out['from'] = $f['from'] ?? '';
        $out['to']   = $f['to'] ?? '';
        $out['branch_id'] = isset($f['branch_id']) && $f['branch_id'] !== '' ? (int)$f['branch_id'] : 0;
        $out['group'] = in_array(($f['group'] ?? 'month'), ['day', 'month'], true) ? $f['group'] : 'month';

        if ($out['from'] === '' || $out['to'] === '') {
            $out['to'] = date('Y-m-d');
            $out['from'] = date('Y-m-d', strtotime('-6 months'));
        }

        return $out;
    }

    // Handle branchWhere operation.
    private function branchWhere(array $filters, string $col = 'a.branch_id'): array
    {
        if (!empty($filters['branch_id'])) {
            return [" AND {$col} = :branch_id ", [':branch_id' => (int)$filters['branch_id']]];
        }
        return ['', []];
    }

    // Handle groupExpr operation.
    private function groupExpr(array $filters, string $col): string
    {
        return $filters['group'] === 'day'
            ? "DATE({$col})"
            : "DATE_FORMAT({$col},'%Y-%m')";
    }

    // Handle branches operation.
    public function branches(): array
    {
        return $this->stmt("SELECT branch_id, name FROM branches ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle serviceTypes operation.
    public function serviceTypes(): array
    {
        return $this->stmt("SELECT type_id, type_name FROM service_types ORDER BY type_name")->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =========================================================
     * 1) SERVICE PERFORMANCE
     * ========================================================= */

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
            ':to'   => $filters['to'],
        ], $bp))->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle serviceTrend operation.
    public function serviceTrend(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);
        [$bw, $bp] = $this->branchWhere($filters, 'a.branch_id');
        $groupExpr = $this->groupExpr($filters, 'a.appointment_date');

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

        return $this->stmt($sql, array_merge([
            ':from' => $filters['from'],
            ':to'   => $filters['to'],
        ], $bp))->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle serviceTypeDistribution operation.
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

        return $this->stmt($sql, array_merge([
            ':from' => $filters['from'],
            ':to'   => $filters['to'],
        ], $bp))->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle avgCompletionMinutes operation.
    public function avgCompletionMinutes(array $filtersIn): float
    {
        $filters = $this->normalizeFilters($filtersIn);
        [$bw, $bp] = $this->branchWhere($filters, 'a.branch_id');

        $sql = "
            SELECT COALESCE(AVG(TIMESTAMPDIFF(MINUTE, w.started_at, w.completed_at)), 0) AS avg_mins
            FROM work_orders w
            JOIN appointments a ON a.appointment_id = w.appointment_id
            WHERE w.status = 'completed'
              AND w.started_at IS NOT NULL
              AND w.completed_at IS NOT NULL
              AND a.appointment_date BETWEEN :from AND :to
              {$bw}
        ";

        return (float)($this->stmt($sql, array_merge([
            ':from' => $filters['from'],
            ':to'   => $filters['to'],
        ], $bp))->fetchColumn() ?? 0);
    }

    // Handle serviceDemandByWeekday operation.
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
            ':to'   => $filters['to'],
        ], $bp))->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle seasonalDemand operation.
    public function seasonalDemand(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);
        [$bw, $bp] = $this->branchWhere($filters, 'branch_id');

        $sql = "
            SELECT
                DATE_FORMAT(appointment_date, '%Y-%m') AS label,
                COUNT(*) AS value
            FROM appointments
            WHERE appointment_date BETWEEN :from AND :to
              {$bw}
            GROUP BY DATE_FORMAT(appointment_date, '%Y-%m')
            ORDER BY label
        ";

        return $this->stmt($sql, array_merge([
            ':from' => $filters['from'],
            ':to'   => $filters['to'],
        ], $bp))->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle averageWaitingTimeBeforeStart operation.
    public function averageWaitingTimeBeforeStart(array $filtersIn): float
    {
        $filters = $this->normalizeFilters($filtersIn);
        [$bw, $bp] = $this->branchWhere($filters, 'a.branch_id');

        $sql = "
            SELECT COALESCE(AVG(
                TIMESTAMPDIFF(
                    MINUTE,
                    TIMESTAMP(a.appointment_date, a.appointment_time),
                    w.started_at
                )
            ), 0)
            FROM work_orders w
            JOIN appointments a ON a.appointment_id = w.appointment_id
            WHERE w.started_at IS NOT NULL
              AND a.appointment_date BETWEEN :from AND :to
              {$bw}
        ";

        return (float)($this->stmt($sql, array_merge([
            ':from' => $filters['from'],
            ':to'   => $filters['to'],
        ], $bp))->fetchColumn() ?? 0);
    }

    // Handle turnaroundTimeByBranch operation.
    public function turnaroundTimeByBranch(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);

        $sql = "
            SELECT
                b.name AS label,
                COALESCE(AVG(TIMESTAMPDIFF(MINUTE, w.started_at, w.completed_at)), 0) AS value
            FROM work_orders w
            JOIN appointments a ON a.appointment_id = w.appointment_id
            JOIN branches b ON b.branch_id = a.branch_id
            WHERE w.status = 'completed'
              AND w.started_at IS NOT NULL
              AND w.completed_at IS NOT NULL
              AND a.appointment_date BETWEEN :from AND :to
            GROUP BY b.branch_id
            ORDER BY value DESC
        ";

        return $this->stmt($sql, [
            ':from' => $filters['from'],
            ':to'   => $filters['to'],
        ])->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle repeatCustomerFrequency operation.
    public function repeatCustomerFrequency(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);
        [$bw, $bp] = $this->branchWhere($filters, 'a.branch_id');

        $sql = "
            SELECT
                CASE
                    WHEN t.booking_count = 1 THEN '1 booking'
                    WHEN t.booking_count BETWEEN 2 AND 3 THEN '2-3 bookings'
                    WHEN t.booking_count BETWEEN 4 AND 5 THEN '4-5 bookings'
                    ELSE '6+ bookings'
                END AS label,
                COUNT(*) AS value
            FROM (
                SELECT a.customer_id, COUNT(*) AS booking_count
                FROM appointments a
                WHERE a.appointment_date BETWEEN :from AND :to
                  {$bw}
                GROUP BY a.customer_id
            ) t
            GROUP BY label
            ORDER BY value DESC
        ";

        return $this->stmt($sql, array_merge([
            ':from' => $filters['from'],
            ':to'   => $filters['to'],
        ], $bp))->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle mostRebookedServices operation.
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
            ':to'   => $filters['to'],
        ], $bp))->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =========================================================
     * 2) REVENUE & FINANCE
     * ========================================================= */

    public function revenueTrend(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);
        [$bw, $bp] = $this->branchWhere($filters, 'a.branch_id');
        $groupExpr = $this->groupExpr($filters, 'i.issued_at');

        $sql = "
            SELECT {$groupExpr} AS label, COALESCE(SUM(i.grand_total), 0) AS value
            FROM invoices i
            JOIN work_orders w ON w.work_order_id = i.work_order_id
            JOIN appointments a ON a.appointment_id = w.appointment_id
            WHERE i.status = 'paid'
              AND DATE(i.issued_at) BETWEEN :from AND :to
              {$bw}
            GROUP BY label
            ORDER BY label
        ";

        return $this->stmt($sql, array_merge([
            ':from' => $filters['from'],
            ':to'   => $filters['to'],
        ], $bp))->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle costTrend operation.
    public function costTrend(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);
        [$bw, $bp] = $this->branchWhere($filters, 'a.branch_id');
        $groupExpr = $this->groupExpr($filters, 'w.completed_at');

        $sql = "
            SELECT {$groupExpr} AS label, COALESCE(SUM(w.total_cost), 0) AS value
            FROM work_orders w
            JOIN appointments a ON a.appointment_id = w.appointment_id
            WHERE w.completed_at IS NOT NULL
              AND DATE(w.completed_at) BETWEEN :from AND :to
              {$bw}
            GROUP BY label
            ORDER BY label
        ";

        return $this->stmt($sql, array_merge([
            ':from' => $filters['from'],
            ':to'   => $filters['to'],
        ], $bp))->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle profitTrend operation.
    public function profitTrend(array $filtersIn): array
    {
        $revenue = $this->revenueTrend($filtersIn);
        $cost    = $this->costTrend($filtersIn);

        $costMap = [];
        foreach ($cost as $row) {
            $costMap[(string)$row['label']] = (float)$row['value'];
        }

        $out = [];
        foreach ($revenue as $row) {
            $label = (string)$row['label'];
            $out[] = [
                'label' => $label,
                'value' => round((float)$row['value'] - ($costMap[$label] ?? 0), 2),
            ];
        }

        return $out;
    }

    // Handle revenueByBranch operation.
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
            ':to'   => $filters['to'],
        ])->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle avgInvoiceValue operation.
    public function avgInvoiceValue(array $filtersIn): float
    {
        $filters = $this->normalizeFilters($filtersIn);
        [$bw, $bp] = $this->branchWhere($filters, 'a.branch_id');

        $sql = "
            SELECT COALESCE(AVG(i.grand_total), 0)
            FROM invoices i
            JOIN work_orders w ON w.work_order_id = i.work_order_id
            JOIN appointments a ON a.appointment_id = w.appointment_id
            WHERE i.status = 'paid'
              AND DATE(i.issued_at) BETWEEN :from AND :to
              {$bw}
        ";

        return (float)($this->stmt($sql, array_merge([
            ':from' => $filters['from'],
            ':to'   => $filters['to'],
        ], $bp))->fetchColumn() ?? 0);
    }

    // Handle revenueByServiceType operation.
    public function revenueByServiceType(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);
        [$bw, $bp] = $this->branchWhere($filters, 'a.branch_id');

        $sql = "
            SELECT st.type_name AS label, COALESCE(SUM(i.grand_total), 0) AS value
            FROM invoices i
            JOIN work_orders w ON w.work_order_id = i.work_order_id
            JOIN appointments a ON a.appointment_id = w.appointment_id
            JOIN services s ON s.service_id = a.service_id
            JOIN service_types st ON st.type_id = s.type_id
            WHERE i.status = 'paid'
              AND DATE(i.issued_at) BETWEEN :from AND :to
              {$bw}
            GROUP BY st.type_id
            ORDER BY value DESC
        ";

        return $this->stmt($sql, array_merge([
            ':from' => $filters['from'],
            ':to'   => $filters['to'],
        ], $bp))->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle unpaidInvoiceAging operation.
    public function unpaidInvoiceAging(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);

        $sql = "
            SELECT
                CASE
                    WHEN DATEDIFF(CURDATE(), DATE(i.issued_at)) BETWEEN 0 AND 7 THEN '0-7 days'
                    WHEN DATEDIFF(CURDATE(), DATE(i.issued_at)) BETWEEN 8 AND 15 THEN '8-15 days'
                    WHEN DATEDIFF(CURDATE(), DATE(i.issued_at)) BETWEEN 16 AND 30 THEN '16-30 days'
                    ELSE '31+ days'
                END AS label,
                COUNT(*) AS value
            FROM invoices i
            JOIN work_orders w ON w.work_order_id = i.work_order_id
            JOIN appointments a ON a.appointment_id = w.appointment_id
            WHERE i.status = 'unpaid'
              AND DATE(i.issued_at) BETWEEN :from AND :to
              AND (:branch_filter = 0 OR a.branch_id = :branch_id)
            GROUP BY label
            ORDER BY value DESC
        ";

        return $this->stmt($sql, [
            ':from'          => $filters['from'],
            ':to'            => $filters['to'],
            ':branch_filter' => (int)$filters['branch_id'],
            ':branch_id'     => (int)$filters['branch_id'],
        ])->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle paymentMethodBreakdown operation.
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
            ':from'          => $filters['from'],
            ':to'            => $filters['to'],
            ':branch_filter' => (int)$filters['branch_id'],
            ':branch_id'     => (int)$filters['branch_id'],
        ])->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle paymentStatusBreakdown operation.
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
            ':from'          => $filters['from'],
            ':to'            => $filters['to'],
            ':branch_filter' => (int)$filters['branch_id'],
            ':branch_id'     => (int)$filters['branch_id'],
        ])->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle avgRevenuePerAppointment operation.
    public function avgRevenuePerAppointment(array $filtersIn): float
    {
        $filters = $this->normalizeFilters($filtersIn);

        $sql = "
            SELECT COALESCE(SUM(i.grand_total) / NULLIF(COUNT(DISTINCT a.appointment_id), 0), 0)
            FROM invoices i
            JOIN work_orders w ON w.work_order_id = i.work_order_id
            JOIN appointments a ON a.appointment_id = w.appointment_id
            WHERE i.status = 'paid'
              AND DATE(i.issued_at) BETWEEN :from AND :to
              AND (:branch_filter = 0 OR a.branch_id = :branch_id)
        ";

        return (float)($this->stmt($sql, [
            ':from'          => $filters['from'],
            ':to'            => $filters['to'],
            ':branch_filter' => (int)$filters['branch_id'],
            ':branch_id'     => (int)$filters['branch_id'],
        ])->fetchColumn() ?? 0);
    }

    // Handle avgRevenuePerCustomer operation.
    public function avgRevenuePerCustomer(array $filtersIn): float
    {
        $filters = $this->normalizeFilters($filtersIn);

        $sql = "
            SELECT COALESCE(SUM(i.grand_total) / NULLIF(COUNT(DISTINCT a.customer_id), 0), 0)
            FROM invoices i
            JOIN work_orders w ON w.work_order_id = i.work_order_id
            JOIN appointments a ON a.appointment_id = w.appointment_id
            WHERE i.status = 'paid'
              AND DATE(i.issued_at) BETWEEN :from AND :to
              AND (:branch_filter = 0 OR a.branch_id = :branch_id)
        ";

        return (float)($this->stmt($sql, [
            ':from'          => $filters['from'],
            ':to'            => $filters['to'],
            ':branch_filter' => (int)$filters['branch_id'],
            ':branch_id'     => (int)$filters['branch_id'],
        ])->fetchColumn() ?? 0);
    }

    // Handle branchPaymentCollectionPerformance operation.
    public function branchPaymentCollectionPerformance(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);

        $sql = "
            SELECT
                b.name AS label,
                ROUND(
                    100 * COALESCE(SUM(CASE WHEN i.status = 'paid' THEN i.grand_total ELSE 0 END), 0)
                    / NULLIF(COALESCE(SUM(i.grand_total), 0), 0),
                    2
                ) AS value
            FROM invoices i
            JOIN work_orders w ON w.work_order_id = i.work_order_id
            JOIN appointments a ON a.appointment_id = w.appointment_id
            JOIN branches b ON b.branch_id = a.branch_id
            WHERE DATE(i.issued_at) BETWEEN :from AND :to
            GROUP BY b.branch_id
            ORDER BY value DESC
        ";

        return $this->stmt($sql, [
            ':from' => $filters['from'],
            ':to'   => $filters['to'],
        ])->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =========================================================
     * 3) APPOINTMENT & OPERATIONS
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

        return $this->stmt($sql, array_merge([
            ':from' => $filters['from'],
            ':to'   => $filters['to'],
        ], $bp))->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle appointmentsByHour operation.
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
            ':to'   => $filters['to'],
        ], $bp))->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle appointmentsTrend operation.
    public function appointmentsTrend(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);
        [$bw, $bp] = $this->branchWhere($filters, 'branch_id');
        $groupExpr = $this->groupExpr($filters, 'appointment_date');

        $sql = "
            SELECT {$groupExpr} AS label, COUNT(*) AS value
            FROM appointments
            WHERE appointment_date BETWEEN :from AND :to
              {$bw}
            GROUP BY label
            ORDER BY label
        ";

        return $this->stmt($sql, array_merge([
            ':from' => $filters['from'],
            ':to'   => $filters['to'],
        ], $bp))->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle cancellationTrend operation.
    public function cancellationTrend(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);
        $groupExpr = $this->groupExpr($filters, 'appointment_date');

        $sql = "
            SELECT {$groupExpr} AS label, COUNT(*) AS value
            FROM appointments
            WHERE status = 'cancelled'
              AND appointment_date BETWEEN :from AND :to
              AND (:branch_filter = 0 OR branch_id = :branch_id)
            GROUP BY label
            ORDER BY label
        ";

        return $this->stmt($sql, [
            ':from'          => $filters['from'],
            ':to'            => $filters['to'],
            ':branch_filter' => (int)$filters['branch_id'],
            ':branch_id'     => (int)$filters['branch_id'],
        ])->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =========================================================
     * 4) BRANCH PERFORMANCE
     * ========================================================= */

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
            ':to'   => $filters['to'],
        ])->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle branchAvgRating operation.
    public function branchAvgRating(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);

        $sql = "
            SELECT b.name AS label, COALESCE(AVG(f.rating), 0) AS value
            FROM feedback f
            JOIN appointments a ON a.appointment_id = f.appointment_id
            JOIN branches b ON b.branch_id = a.branch_id
            WHERE DATE(f.created_at) BETWEEN :from AND :to
            GROUP BY b.branch_id
            ORDER BY value DESC
        ";

        return $this->stmt($sql, [
            ':from' => $filters['from'],
            ':to'   => $filters['to'],
        ])->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle branchCapacityUtilization operation.
    public function branchCapacityUtilization(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);

        $sql = "
            SELECT
                b.name AS label,
                ROUND(100 * COUNT(a.appointment_id) / NULLIF(MAX(b.capacity), 0), 2) AS value
            FROM branches b
            LEFT JOIN appointments a
                ON a.branch_id = b.branch_id
               AND a.appointment_date BETWEEN :from AND :to
            GROUP BY b.branch_id
            ORDER BY value DESC
        ";

        return $this->stmt($sql, [
            ':from' => $filters['from'],
            ':to'   => $filters['to'],
        ])->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle branchStaffingVsWorkload operation.
    public function branchStaffingVsWorkload(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);

        $sql = "
            SELECT
                b.name AS label,
                ROUND(COUNT(a.appointment_id) / NULLIF(MAX(b.staff_count), 0), 2) AS value
            FROM branches b
            LEFT JOIN appointments a
                ON a.branch_id = b.branch_id
               AND a.appointment_date BETWEEN :from AND :to
            GROUP BY b.branch_id
            ORDER BY value DESC
        ";

        return $this->stmt($sql, [
            ':from' => $filters['from'],
            ':to'   => $filters['to'],
        ])->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle branchServiceCoverageMatrix operation.
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

    // Handle branchComplaintRate operation.
    public function branchComplaintRate(array $filtersIn): array
{
    $filters = $this->normalizeFilters($filtersIn);

    $sql = "
        SELECT
            b.name AS label,
            ROUND(
                100 * COUNT(cp.complaint_id) / NULLIF(COUNT(DISTINCT a.appointment_id), 0),
                2
            ) AS value
        FROM branches b
        LEFT JOIN appointments a
            ON a.branch_id = b.branch_id
           AND a.appointment_date BETWEEN :appt_from AND :appt_to
        LEFT JOIN complaints cp
            ON cp.branch_id = b.branch_id
           AND DATE(cp.created_at) BETWEEN :cmp_from AND :cmp_to
        GROUP BY b.branch_id
        ORDER BY value DESC
    ";

    return $this->stmt($sql, [
        ':appt_from' => $filters['from'],
        ':appt_to'   => $filters['to'],
        ':cmp_from'  => $filters['from'],
        ':cmp_to'    => $filters['to'],
    ])->fetchAll(PDO::FETCH_ASSOC);
}

    // Handle branchApprovalRejectionRate operation.
    public function branchApprovalRejectionRate(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);

        $sql = "
            SELECT
                b.name AS label,
                ROUND(
                    100 * SUM(CASE WHEN s.status = 'rejected' THEN 1 ELSE 0 END)
                    / NULLIF(COUNT(DISTINCT s.service_id), 0),
                    2
                ) AS value
            FROM branches b
            LEFT JOIN branch_services bs ON bs.branch_id = b.branch_id
            LEFT JOIN services s ON s.service_id = bs.service_id
                AND DATE(s.created_at) BETWEEN :from AND :to
            GROUP BY b.branch_id
            ORDER BY value DESC
        ";

        return $this->stmt($sql, [
            ':from' => $filters['from'],
            ':to'   => $filters['to'],
        ])->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle branchQualityScore operation.
    public function branchQualityScore(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);

        $sql = "
            SELECT
                b.name AS label,
                COALESCE(AVG(r.quality_rating), 0) AS value
            FROM reports r
            JOIN work_orders w ON w.work_order_id = r.work_order_id
            JOIN appointments a ON a.appointment_id = w.appointment_id
            JOIN branches b ON b.branch_id = a.branch_id
            WHERE DATE(r.created_at) BETWEEN :from AND :to
            GROUP BY b.branch_id
            ORDER BY value DESC
        ";

        return $this->stmt($sql, [
            ':from' => $filters['from'],
            ':to'   => $filters['to'],
        ])->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle underperformingBranches operation.
    public function underperformingBranches(array $filtersIn): array
{
    $filters = $this->normalizeFilters($filtersIn);

    $sql = "
        SELECT
            b.name AS label,
            (
                (CASE WHEN COALESCE(AVG(f.rating), 0) < 3 THEN 1 ELSE 0 END) +
                (CASE WHEN MAX(b.capacity) > 0 AND COUNT(a.appointment_id) < 5 THEN 1 ELSE 0 END) +
                (CASE WHEN COUNT(cp.complaint_id) >= 3 THEN 1 ELSE 0 END)
            ) AS value
        FROM branches b
        LEFT JOIN appointments a
            ON a.branch_id = b.branch_id
           AND a.appointment_date BETWEEN :appt_from AND :appt_to
        LEFT JOIN feedback f
            ON f.appointment_id = a.appointment_id
           AND DATE(f.created_at) BETWEEN :fb_from AND :fb_to
        LEFT JOIN complaints cp
            ON cp.branch_id = b.branch_id
           AND DATE(cp.created_at) BETWEEN :cmp_from AND :cmp_to
        GROUP BY b.branch_id
        HAVING value > 0
        ORDER BY value DESC, b.name ASC
    ";

    return $this->stmt($sql, [
        ':appt_from' => $filters['from'],
        ':appt_to'   => $filters['to'],
        ':fb_from'   => $filters['from'],
        ':fb_to'     => $filters['to'],
        ':cmp_from'  => $filters['from'],
        ':cmp_to'    => $filters['to'],
    ])->fetchAll(PDO::FETCH_ASSOC);
}

    /* =========================================================
     * 5) STAFF
     * ========================================================= */

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
            ':to'   => $filters['to'],
        ], $bp))->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle servicesSubmittedByManagers operation.
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
            ':to'   => $filters['to'],
        ])->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle managerApprovalDecisions operation.
    public function managerApprovalDecisions(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);

        $sql = "
            SELECT
                COALESCE(CONCAT(u.first_name, ' ', u.last_name), 'Unknown') AS label,
                SUM(CASE WHEN s.status = 'active' THEN 1 ELSE 0 END) AS value
            FROM services s
            LEFT JOIN users u ON u.user_id = s.approved_by
            WHERE DATE(s.created_at) BETWEEN :from AND :to
              AND s.approved_by IS NOT NULL
            GROUP BY s.approved_by
            ORDER BY value DESC
            LIMIT 10
        ";

        return $this->stmt($sql, [
            ':from' => $filters['from'],
            ':to'   => $filters['to'],
        ])->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle mechanicQualityOutcomes operation.
    public function mechanicQualityOutcomes(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);

        $sql = "
            SELECT
                CONCAT(u.first_name, ' ', u.last_name) AS label,
                COALESCE(AVG(r.quality_rating), 0) AS value
            FROM reports r
            JOIN work_orders w ON w.work_order_id = r.work_order_id
            JOIN mechanics m ON m.mechanic_id = w.mechanic_id
            JOIN users u ON u.user_id = m.user_id
            JOIN appointments a ON a.appointment_id = w.appointment_id
            WHERE DATE(r.created_at) BETWEEN :from AND :to
              AND (:branch_filter = 0 OR a.branch_id = :branch_id)
            GROUP BY m.mechanic_id
            ORDER BY value DESC
            LIMIT 10
        ";

        return $this->stmt($sql, [
            ':from'          => $filters['from'],
            ':to'            => $filters['to'],
            ':branch_filter' => (int)$filters['branch_id'],
            ':branch_id'     => (int)$filters['branch_id'],
        ])->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle staffComplaintAssociation operation.
    public function staffComplaintAssociation(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);

        $sql = "
            SELECT
                COALESCE(CONCAT(u.first_name, ' ', u.last_name), 'Unassigned') AS label,
                COUNT(*) AS value
            FROM complaints cp
            LEFT JOIN users u ON u.user_id = cp.assigned_to_user_id
            WHERE DATE(cp.created_at) BETWEEN :from AND :to
              AND (:branch_filter = 0 OR cp.branch_id = :branch_id)
            GROUP BY cp.assigned_to_user_id
            ORDER BY value DESC
            LIMIT 10
        ";

        return $this->stmt($sql, [
            ':from'          => $filters['from'],
            ':to'            => $filters['to'],
            ':branch_filter' => (int)$filters['branch_id'],
            ':branch_id'     => (int)$filters['branch_id'],
        ])->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle avgJobsPerDayPerMechanic operation.
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
            ':from'          => $filters['from'],
            ':to'            => $filters['to'],
            ':branch_filter' => (int)$filters['branch_id'],
            ':branch_id'     => (int)$filters['branch_id'],
        ])->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle delayedWorkOrdersByMechanic operation.
    public function delayedWorkOrdersByMechanic(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);

        $sql = "
            SELECT
                CONCAT(u.first_name, ' ', u.last_name) AS label,
                COUNT(*) AS value
            FROM work_orders w
            JOIN mechanics m ON m.mechanic_id = w.mechanic_id
            JOIN users u ON u.user_id = m.user_id
            JOIN appointments a ON a.appointment_id = w.appointment_id
            JOIN services s ON s.service_id = a.service_id
            WHERE w.completed_at IS NOT NULL
              AND w.started_at IS NOT NULL
              AND TIMESTAMPDIFF(MINUTE, w.started_at, w.completed_at) > COALESCE(s.base_duration_minutes, 0)
              AND DATE(a.appointment_date) BETWEEN :from AND :to
              AND (:branch_filter = 0 OR a.branch_id = :branch_id)
            GROUP BY m.mechanic_id
            ORDER BY value DESC
            LIMIT 10
        ";

        return $this->stmt($sql, [
            ':from'          => $filters['from'],
            ':to'            => $filters['to'],
            ':branch_filter' => (int)$filters['branch_id'],
            ':branch_id'     => (int)$filters['branch_id'],
        ])->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =========================================================
     * 6) FEEDBACK
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

        return $this->stmt($sql, array_merge([
            ':from' => $filters['from'],
            ':to'   => $filters['to'],
        ], $bp))->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle feedbackTrend operation.
    public function feedbackTrend(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);
        [$bw, $bp] = $this->branchWhere($filters, 'a.branch_id');
        $groupExpr = $this->groupExpr($filters, 'f.created_at');

        $sql = "
            SELECT {$groupExpr} AS label, COUNT(*) AS value
            FROM feedback f
            JOIN appointments a ON a.appointment_id = f.appointment_id
            WHERE DATE(f.created_at) BETWEEN :from AND :to
              {$bw}
            GROUP BY label
            ORDER BY label
        ";

        return $this->stmt($sql, array_merge([
            ':from' => $filters['from'],
            ':to'   => $filters['to'],
        ], $bp))->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle lowestRatedServices operation.
    public function lowestRatedServices(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);
        [$bw, $bp] = $this->branchWhere($filters, 'a.branch_id');

        $sql = "
            SELECT s.name AS label, COALESCE(AVG(f.rating), 0) AS value
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

        return $this->stmt($sql, array_merge([
            ':from' => $filters['from'],
            ':to'   => $filters['to'],
        ], $bp))->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle branchRatingTrend operation.
    public function branchRatingTrend(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);

        $sql = "
            SELECT
                b.name AS label,
                COALESCE(AVG(f.rating), 0) AS value
            FROM feedback f
            JOIN appointments a ON a.appointment_id = f.appointment_id
            JOIN branches b ON b.branch_id = a.branch_id
            WHERE DATE(f.created_at) BETWEEN :from AND :to
              AND (:branch_filter = 0 OR a.branch_id = :branch_id)
            GROUP BY b.branch_id
            ORDER BY value DESC
        ";

        return $this->stmt($sql, [
            ':from'          => $filters['from'],
            ':to'            => $filters['to'],
            ':branch_filter' => (int)$filters['branch_id'],
            ':branch_id'     => (int)$filters['branch_id'],
        ])->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle ratingByServiceType operation.
    public function ratingByServiceType(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);

        $sql = "
            SELECT
                st.type_name AS label,
                COALESCE(AVG(f.rating), 0) AS value
            FROM feedback f
            JOIN appointments a ON a.appointment_id = f.appointment_id
            JOIN services s ON s.service_id = a.service_id
            JOIN service_types st ON st.type_id = s.type_id
            WHERE DATE(f.created_at) BETWEEN :from AND :to
              AND (:branch_filter = 0 OR a.branch_id = :branch_id)
            GROUP BY st.type_id
            ORDER BY value DESC
        ";

        return $this->stmt($sql, [
            ':from'          => $filters['from'],
            ':to'            => $filters['to'],
            ':branch_filter' => (int)$filters['branch_id'],
            ':branch_id'     => (int)$filters['branch_id'],
        ])->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle feedbackResponseTurnaround operation.
    public function feedbackResponseTurnaround(array $filtersIn): float
    {
        $filters = $this->normalizeFilters($filtersIn);

        $sql = "
            SELECT COALESCE(AVG(TIMESTAMPDIFF(HOUR, f.created_at, f.replied_at)), 0)
            FROM feedback f
            JOIN appointments a ON a.appointment_id = f.appointment_id
            WHERE f.replied_at IS NOT NULL
              AND DATE(f.created_at) BETWEEN :from AND :to
              AND (:branch_filter = 0 OR a.branch_id = :branch_id)
        ";

        return (float)($this->stmt($sql, [
            ':from'          => $filters['from'],
            ':to'            => $filters['to'],
            ':branch_filter' => (int)$filters['branch_id'],
            ':branch_id'     => (int)$filters['branch_id'],
        ])->fetchColumn() ?? 0);
    }

    // Handle mostPraisedServices operation.
    public function mostPraisedServices(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);

        $sql = "
            SELECT
                s.name AS label,
                COALESCE(AVG(f.rating), 0) AS value
            FROM feedback f
            JOIN appointments a ON a.appointment_id = f.appointment_id
            JOIN services s ON s.service_id = a.service_id
            WHERE DATE(f.created_at) BETWEEN :from AND :to
              AND f.rating >= 4
              AND (:branch_filter = 0 OR a.branch_id = :branch_id)
            GROUP BY s.service_id
            HAVING COUNT(*) >= 2
            ORDER BY value DESC, label ASC
            LIMIT 10
        ";

        return $this->stmt($sql, [
            ':from'          => $filters['from'],
            ':to'            => $filters['to'],
            ':branch_filter' => (int)$filters['branch_id'],
            ':branch_id'     => (int)$filters['branch_id'],
        ])->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle repeatNegativeFeedbackCustomers operation.
    public function repeatNegativeFeedbackCustomers(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);

        $sql = "
            SELECT
                CONCAT(u.first_name, ' ', u.last_name) AS label,
                COUNT(*) AS value
            FROM feedback f
            JOIN appointments a ON a.appointment_id = f.appointment_id
            JOIN customers c ON c.customer_id = a.customer_id
            JOIN users u ON u.user_id = c.user_id
            WHERE DATE(f.created_at) BETWEEN :from AND :to
              AND f.rating <= 2
              AND (:branch_filter = 0 OR a.branch_id = :branch_id)
            GROUP BY c.customer_id
            HAVING COUNT(*) >= 2
            ORDER BY value DESC
            LIMIT 10
        ";

        return $this->stmt($sql, [
            ':from'          => $filters['from'],
            ':to'            => $filters['to'],
            ':branch_filter' => (int)$filters['branch_id'],
            ':branch_id'     => (int)$filters['branch_id'],
        ])->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =========================================================
     * 7) APPROVALS
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

        return $this->stmt($sql, [
            ':from' => $filters['from'],
            ':to'   => $filters['to'],
        ])->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle avgApprovalHours operation.
    public function avgApprovalHours(array $filtersIn): float
    {
        $filters = $this->normalizeFilters($filtersIn);

        $sql = "
            SELECT COALESCE(AVG(TIMESTAMPDIFF(HOUR, created_at, approved_at)), 0)
            FROM services
            WHERE approved_at IS NOT NULL
              AND DATE(created_at) BETWEEN :from AND :to
        ";

        return (float)($this->stmt($sql, [
            ':from' => $filters['from'],
            ':to'   => $filters['to'],
        ])->fetchColumn() ?? 0);
    }

    /* =========================================================
     * 8) COMPLAINTS
     * ========================================================= */

    public function complaintTrend(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);
        $groupExpr = $this->groupExpr($filters, 'cp.created_at');

        $sql = "
            SELECT {$groupExpr} AS label, COUNT(*) AS value
            FROM complaints cp
            WHERE DATE(cp.created_at) BETWEEN :from AND :to
              AND (:branch_filter = 0 OR cp.branch_id = :branch_id)
            GROUP BY label
            ORDER BY label
        ";

        return $this->stmt($sql, [
            ':from'          => $filters['from'],
            ':to'            => $filters['to'],
            ':branch_filter' => (int)$filters['branch_id'],
            ':branch_id'     => (int)$filters['branch_id'],
        ])->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle complaintResolutionTrend operation.
    public function complaintResolutionTrend(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);
        $groupExpr = $this->groupExpr($filters, 'cp.resolved_at');

        $sql = "
            SELECT {$groupExpr} AS label,
                   COALESCE(AVG(TIMESTAMPDIFF(HOUR, cp.created_at, cp.resolved_at)), 0) AS value
            FROM complaints cp
            WHERE cp.resolved_at IS NOT NULL
              AND DATE(cp.created_at) BETWEEN :from AND :to
              AND (:branch_filter = 0 OR cp.branch_id = :branch_id)
            GROUP BY label
            ORDER BY label
        ";

        return $this->stmt($sql, [
            ':from'          => $filters['from'],
            ':to'            => $filters['to'],
            ':branch_filter' => (int)$filters['branch_id'],
            ':branch_id'     => (int)$filters['branch_id'],
        ])->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle complaintClosureRateByBranch operation.
    public function complaintClosureRateByBranch(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);

        $sql = "
            SELECT
                b.name AS label,
                ROUND(
                    100 * SUM(CASE WHEN cp.status IN ('resolved', 'closed') THEN 1 ELSE 0 END)
                    / NULLIF(COUNT(cp.complaint_id), 0),
                    2
                ) AS value
            FROM complaints cp
            JOIN branches b ON b.branch_id = cp.branch_id
            WHERE DATE(cp.created_at) BETWEEN :from AND :to
            GROUP BY b.branch_id
            ORDER BY value DESC
        ";

        return $this->stmt($sql, [
            ':from' => $filters['from'],
            ':to'   => $filters['to'],
        ])->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle complaintPriorityAnalysis operation.
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
            ':from'          => $filters['from'],
            ':to'            => $filters['to'],
            ':branch_filter' => (int)$filters['branch_id'],
            ':branch_id'     => (int)$filters['branch_id'],
        ])->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle mostComplainedServices operation.
    public function mostComplainedServices(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);

        $sql = "
            SELECT s.name AS label, COUNT(*) AS value
            FROM complaints cp
            LEFT JOIN appointments a ON a.appointment_id = cp.appointment_id
            LEFT JOIN services s ON s.service_id = a.service_id
            WHERE DATE(cp.created_at) BETWEEN :from AND :to
              AND (:branch_filter = 0 OR cp.branch_id = :branch_id)
            GROUP BY s.service_id, s.name
            ORDER BY value DESC
            LIMIT 10
        ";

        return $this->stmt($sql, [
            ':from'          => $filters['from'],
            ':to'            => $filters['to'],
            ':branch_filter' => (int)$filters['branch_id'],
            ':branch_id'     => (int)$filters['branch_id'],
        ])->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle mostComplainedBranches operation.
    public function mostComplainedBranches(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);

        $sql = "
            SELECT b.name AS label, COUNT(*) AS value
            FROM complaints cp
            JOIN branches b ON b.branch_id = cp.branch_id
            WHERE DATE(cp.created_at) BETWEEN :from AND :to
            GROUP BY b.branch_id
            ORDER BY value DESC
            LIMIT 10
        ";

        return $this->stmt($sql, [
            ':from' => $filters['from'],
            ':to'   => $filters['to'],
        ])->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle mostComplainedStaff operation.
    public function mostComplainedStaff(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);

        $sql = "
            SELECT
                COALESCE(CONCAT(u.first_name, ' ', u.last_name), 'Unassigned') AS label,
                COUNT(*) AS value
            FROM complaints cp
            LEFT JOIN users u ON u.user_id = cp.assigned_to_user_id
            WHERE DATE(cp.created_at) BETWEEN :from AND :to
              AND (:branch_filter = 0 OR cp.branch_id = :branch_id)
            GROUP BY cp.assigned_to_user_id
            ORDER BY value DESC
            LIMIT 10
        ";

        return $this->stmt($sql, [
            ':from'          => $filters['from'],
            ':to'            => $filters['to'],
            ':branch_filter' => (int)$filters['branch_id'],
            ':branch_id'     => (int)$filters['branch_id'],
        ])->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle slaBreachTrend operation.
    public function slaBreachTrend(array $filtersIn): array
    {
        $filters = $this->normalizeFilters($filtersIn);
        $groupExpr = $this->groupExpr($filters, 'cp.created_at');

        $sql = "
            SELECT {$groupExpr} AS label, COUNT(*) AS value
            FROM complaints cp
            WHERE cp.status NOT IN ('resolved', 'closed')
              AND (
                    (cp.priority = 'high' AND TIMESTAMPDIFF(HOUR, cp.created_at, NOW()) > 24)
                 OR (cp.priority = 'medium' AND TIMESTAMPDIFF(HOUR, cp.created_at, NOW()) > 48)
                 OR (cp.priority = 'low' AND TIMESTAMPDIFF(HOUR, cp.created_at, NOW()) > 72)
              )
              AND DATE(cp.created_at) BETWEEN :from AND :to
              AND (:branch_filter = 0 OR cp.branch_id = :branch_id)
            GROUP BY label
            ORDER BY label
        ";

        return $this->stmt($sql, [
            ':from'          => $filters['from'],
            ':to'            => $filters['to'],
            ':branch_filter' => (int)$filters['branch_id'],
            ':branch_id'     => (int)$filters['branch_id'],
        ])->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =========================================================
     * Export map
     * ========================================================= */

    public function exportDataset(string $key, array $filters): array
    {
        return match ($key) {
            'topServices'                  => $this->topServices($filters),
            'serviceTrend'                 => $this->serviceTrend($filters),
            'serviceTypeDistribution'      => $this->serviceTypeDistribution($filters),
            'serviceDemandByWeekday'       => $this->serviceDemandByWeekday($filters),
            'seasonalDemand'               => $this->seasonalDemand($filters),
            'turnaroundTimeByBranch'       => $this->turnaroundTimeByBranch($filters),
            'repeatCustomerFrequency'      => $this->repeatCustomerFrequency($filters),
            'mostRebookedServices'         => $this->mostRebookedServices($filters),

            'revenueTrend'                 => $this->revenueTrend($filters),
            'costTrend'                    => $this->costTrend($filters),
            'profitTrend'                  => $this->profitTrend($filters),
            'revenueByBranch'              => $this->revenueByBranch($filters),
            'revenueByServiceType'         => $this->revenueByServiceType($filters),
            'unpaidInvoiceAging'           => $this->unpaidInvoiceAging($filters),
            'paymentMethodBreakdown'       => $this->paymentMethodBreakdown($filters),
            'paymentStatusBreakdown'       => $this->paymentStatusBreakdown($filters),
            'branchPaymentCollectionPerformance' => $this->branchPaymentCollectionPerformance($filters),

            'appointmentStatusCounts'      => $this->appointmentStatusCounts($filters),
            'appointmentsByHour'           => $this->appointmentsByHour($filters),
            'appointmentsTrend'            => $this->appointmentsTrend($filters),
            'cancellationTrend'            => $this->cancellationTrend($filters),

            'branchCompletedServices'      => $this->branchCompletedServices($filters),
            'branchAvgRating'              => $this->branchAvgRating($filters),
            'branchCapacityUtilization'    => $this->branchCapacityUtilization($filters),
            'branchStaffingVsWorkload'     => $this->branchStaffingVsWorkload($filters),
            'branchServiceCoverageMatrix'  => $this->branchServiceCoverageMatrix($filters),
            'branchComplaintRate'          => $this->branchComplaintRate($filters),
            'branchApprovalRejectionRate'  => $this->branchApprovalRejectionRate($filters),
            'branchQualityScore'           => $this->branchQualityScore($filters),
            'underperformingBranches'      => $this->underperformingBranches($filters),

            'jobsPerMechanic'              => $this->jobsPerMechanic($filters),
            'servicesSubmittedByManagers'  => $this->servicesSubmittedByManagers($filters),
            'managerApprovalDecisions'     => $this->managerApprovalDecisions($filters),
            'mechanicQualityOutcomes'      => $this->mechanicQualityOutcomes($filters),
            'staffComplaintAssociation'    => $this->staffComplaintAssociation($filters),
            'avgJobsPerDayPerMechanic'     => $this->avgJobsPerDayPerMechanic($filters),
            'delayedWorkOrdersByMechanic'  => $this->delayedWorkOrdersByMechanic($filters),

            'ratingDistribution'           => $this->ratingDistribution($filters),
            'feedbackTrend'                => $this->feedbackTrend($filters),
            'lowestRatedServices'          => $this->lowestRatedServices($filters),
            'branchRatingTrend'            => $this->branchRatingTrend($filters),
            'ratingByServiceType'          => $this->ratingByServiceType($filters),
            'mostPraisedServices'          => $this->mostPraisedServices($filters),
            'repeatNegativeFeedbackCustomers' => $this->repeatNegativeFeedbackCustomers($filters),

            'approvalStatusCounts'         => $this->approvalStatusCounts($filters),

            'complaintTrend'               => $this->complaintTrend($filters),
            'complaintResolutionTrend'     => $this->complaintResolutionTrend($filters),
            'complaintClosureRateByBranch' => $this->complaintClosureRateByBranch($filters),
            'complaintPriorityAnalysis'    => $this->complaintPriorityAnalysis($filters),
            'mostComplainedServices'       => $this->mostComplainedServices($filters),
            'mostComplainedBranches'       => $this->mostComplainedBranches($filters),
            'mostComplainedStaff'          => $this->mostComplainedStaff($filters),
            'slaBreachTrend'               => $this->slaBreachTrend($filters),

            default => [],
        };
    }
}