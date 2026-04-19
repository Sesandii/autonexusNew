<?php
declare(strict_types=1);

namespace app\controllers\admin;

use app\core\Controller;
use app\model\admin\Reports;

class ReportsController extends Controller
{
    private Reports $reports;

    // Initialize controller dependencies and request context.
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->requireAdmin();
        $this->reports = new Reports();
    }

    // Display the main listing or dashboard page.
    public function index(): void
    {
        $filters = [
            'from' => $_GET['from'] ?? '',
            'to' => $_GET['to'] ?? '',
            'branch_id' => $_GET['branch_id'] ?? '',
            'group' => $_GET['group'] ?? 'month',
        ];

        $branches = $this->reports->branches();
        $topServices = $this->reports->topServices($filters);
        $weekdayDemand = $this->reports->serviceDemandByWeekday($filters);
        $mostRebookedServices = $this->reports->mostRebookedServices($filters);

        $revenueByBranch = $this->reports->revenueByBranch($filters);
        $paymentMethodBreakdown = $this->reports->paymentMethodBreakdown($filters);
        $paymentStatusBreakdown = $this->reports->paymentStatusBreakdown($filters);

        $apptStatus = $this->reports->appointmentStatusCounts($filters);
        $apptByHour = $this->reports->appointmentsByHour($filters);

        $branchCompleted = $this->reports->branchCompletedServices($filters);
        $branchServiceCoverageMatrix = $this->reports->branchServiceCoverageMatrix($filters);

        $jobsPerMechanic = $this->reports->jobsPerMechanic($filters);
        $submittedByManagers = $this->reports->servicesSubmittedByManagers($filters);
        $avgJobsPerDayPerMechanic = $this->reports->avgJobsPerDayPerMechanic($filters);

        $ratingDist = $this->reports->ratingDistribution($filters);

        $approvalStatus = $this->reports->approvalStatusCounts($filters);

        $complaintPriorityAnalysis = $this->reports->complaintPriorityAnalysis($filters);

        $payload = [
            'service' => [
                'topServices' => $topServices,
                'weekdayDemand' => $weekdayDemand,
                'mostRebookedServices' => $mostRebookedServices,
            ],
            'revenue' => [
                'byBranch' => $revenueByBranch,
                'paymentMethodBreakdown' => $paymentMethodBreakdown,
                'paymentStatusBreakdown' => $paymentStatusBreakdown,
            ],
            'appointments' => [
                'status' => $apptStatus,
                'byHour' => $apptByHour,
            ],
            'branches' => [
                'completed' => $branchCompleted,
                'serviceCoverageMatrix' => $branchServiceCoverageMatrix,
            ],
            'staff' => [
                'jobsPerMechanic' => $jobsPerMechanic,
                'submittedByManagers' => $submittedByManagers,
                'avgJobsPerDayPerMechanic' => $avgJobsPerDayPerMechanic,
            ],
            'feedback' => [
                'ratingDist' => $ratingDist,
            ],
            'approval' => [
                'statusCounts' => $approvalStatus,
            ],
            'complaints' => [
                'priorityAnalysis' => $complaintPriorityAnalysis,
            ],
        ];

        $this->view('admin/admin-viewreports/index', [
            'pageTitle' => 'Reports - AutoNexus',
            'current' => 'reports',
            'filters' => $filters,
            'branches' => $branches,
            'reportDataJson' => json_encode($payload, JSON_UNESCAPED_UNICODE),
        ]);
    }

    // Handle export operation.
    public function export(): void
    {
        $this->requireAdmin();

        $key = $_GET['key'] ?? '';
        $filters = [
            'from' => $_GET['from'] ?? '',
            'to' => $_GET['to'] ?? '',
            'branch_id' => $_GET['branch_id'] ?? '',
            'group' => $_GET['group'] ?? 'month',
        ];

        $rows = $this->reports->exportDataset($key, $filters);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="report_' . $key . '_' . date('Ymd_His') . '.csv"');

        $out = fopen('php://output', 'w');

        // Write filter metadata
        fputcsv($out, ['Report Information']);
        fputcsv($out, ['Generated', date('Y-m-d H:i:s')]);

        // Format date range display
        $from = $filters['from'] ?? '';
        $to = $filters['to'] ?? '';
        if (empty($from) && empty($to)) {
            fputcsv($out, ['Date Range', 'All time']);
        } else {
            $dateRange = ($from ?: 'Start') . ' to ' . ($to ?: 'End');
            fputcsv($out, ['Date Range', $dateRange]);
        }

        // Format branch display
        $branchId = $filters['branch_id'] ?? '';
        $branchDisplay = (empty($branchId) || $branchId === '0') ? 'All Branches' : $branchId;
        fputcsv($out, ['Branch', $branchDisplay]);

        fputcsv($out, ['Group By', $filters['group'] ?? 'month']);
        fputcsv($out, []); // Blank line separator

        // Write data headers
        fputcsv($out, ['Label', 'Value']);

        // Write data rows
        foreach ($rows as $r) {
            fputcsv($out, [$r['label'] ?? '', $r['value'] ?? '']);
        }
        fclose($out);
        exit;
    }

    // Handle exportPdf operation.
    public function exportPdf(): void
    {
        $this->requireAdmin();

        $autoload = BASE_PATH . '/vendor/autoload.php';
        if (!is_file($autoload)) {
            http_response_code(500);
            echo "<pre>Missing vendor/autoload.php. Run: composer require dompdf/dompdf</pre>";
            exit;
        }
        require_once $autoload;

        $key = $_GET['key'] ?? '';
        $filters = [
            'from' => $_GET['from'] ?? '',
            'to' => $_GET['to'] ?? '',
            'branch_id' => $_GET['branch_id'] ?? '',
            'group' => $_GET['group'] ?? 'month',
        ];

        $rows = $this->reports->exportDataset($key, $filters);

        $pdfTitle = 'AutoNexus Report: ' . $key;
        $generatedAt = date('Y-m-d H:i:s');

        ob_start();
        $B = rtrim(BASE_URL, '/');
        $data = [
            'pdfTitle' => $pdfTitle,
            'generatedAt' => $generatedAt,
            'key' => $key,
            'filters' => $filters,
            'rows' => $rows,
            'baseUrl' => $B,
        ];
        extract($data);
        require APP_ROOT . '/views/admin/admin-viewreports/pdf.php';
        $html = ob_get_clean();

        $dompdf = new \Dompdf\Dompdf([
            'isRemoteEnabled' => true,
        ]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = "report_{$key}_" . date('Ymd_His') . ".pdf";
        $dompdf->stream($filename, ['Attachment' => true]);
        exit;
    }

    // Handle exportAll operation.
    public function exportAll(): void
    {
        $this->requireAdmin();

        $format = $_GET['format'] ?? 'csv';
        $filters = [
            'from' => $_GET['from'] ?? '',
            'to' => $_GET['to'] ?? '',
            'branch_id' => $_GET['branch_id'] ?? '',
            'group' => $_GET['group'] ?? 'month',
        ];

        if ($format === 'pdf') {
            $this->exportAllPdf($filters);
        } else {
            $this->exportAllCsv($filters);
        }
    }

    // Handle exportAllCsv operation.
    private function exportAllCsv(array $filters): void
    {
        $reportKeys = [
            'topServices',
            'weekdayDemand',
            'mostRebookedServices',
            'revenueByBranch',
            'paymentMethodBreakdown',
            'paymentStatusBreakdown',
            'appointmentStatusCounts',
            'appointmentsByHour',
            'branchCompletedServices',
            'branchServiceCoverageMatrix',
            'jobsPerMechanic',
            'servicesSubmittedByManagers',
            'avgJobsPerDayPerMechanic',
            'ratingDistribution',
            'approvalStatusCounts',
            'complaintPriorityAnalysis'
        ];

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="all_reports_' . date('Ymd_His') . '.csv"');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['Report', 'Label', 'Value']);

        foreach ($reportKeys as $key) {
            $rows = $this->reports->exportDataset($key, $filters);
            foreach ($rows as $row) {
                fputcsv($out, [
                    ucwords(str_replace(['By', 'Per'], ' ', $key)),
                    $row['label'] ?? '',
                    $row['value'] ?? ''
                ]);
            }
        }
        fclose($out);
        exit;
    }

    // Handle exportAllPdf operation.
    private function exportAllPdf(array $filters): void
    {
        $autoload = BASE_PATH . '/vendor/autoload.php';
        if (!is_file($autoload)) {
            http_response_code(500);
            echo "<pre>Missing vendor/autoload.php. Run: composer install</pre>";
            exit;
        }
        require_once $autoload;

        $reportKeys = [
            'topServices',
            'weekdayDemand',
            'mostRebookedServices',
            'revenueByBranch',
            'paymentMethodBreakdown',
            'paymentStatusBreakdown',
            'appointmentStatusCounts',
            'appointmentsByHour',
            'branchCompletedServices',
            'branchServiceCoverageMatrix',
            'jobsPerMechanic',
            'servicesSubmittedByManagers',
            'avgJobsPerDayPerMechanic',
            'ratingDistribution',
            'approvalStatusCounts',
            'complaintPriorityAnalysis'
        ];

        $allReports = [];
        foreach ($reportKeys as $key) {
            $rows = $this->reports->exportDataset($key, $filters);
            $allReports[$key] = $rows;
        }

        $pdfTitle = 'AutoNexus All Reports';
        $generatedAt = date('Y-m-d H:i:s');
        $B = rtrim(BASE_URL, '/');

        ob_start();
        ?>
        <!DOCTYPE html>
        <html>

        <head>
            <meta charset="UTF-8">
            <style>
                body {
                    font-family: Arial, sans-serif;
                    font-size: 11px;
                    margin: 20px;
                }

                h1 {
                    color: #111827;
                    border-bottom: 2px solid #111827;
                    padding-bottom: 10px;
                    page-break-after: avoid;
                }

                h2 {
                    color: #374151;
                    font-size: 14px;
                    margin-top: 20px;
                    margin-bottom: 10px;
                    page-break-after: avoid;
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 20px;
                }

                th {
                    background: #f3f4f6;
                    color: #111827;
                    padding: 8px;
                    text-align: left;
                    border: 1px solid #d1d5db;
                }

                td {
                    padding: 6px;
                    border: 1px solid #e5e7eb;
                }

                .header {
                    text-align: center;
                    margin-bottom: 30px;
                }

                .footer {
                    text-align: center;
                    font-size: 10px;
                    color: #6b7280;
                    margin-top: 20px;
                    page-break-before: avoid;
                }

                .report-section {
                    page-break-inside: avoid;
                    margin-bottom: 30px;
                }
            </style>
        </head>

        <body>
            <div class="header">
                <h1><?= htmlspecialchars($pdfTitle) ?></h1>
                <p>Generated on <?= htmlspecialchars($generatedAt) ?></p>
            </div>

            <?php foreach ($reportKeys as $key): ?>
                <?php if (!empty($allReports[$key])): ?>
                    <div class="report-section">
                        <h2><?= htmlspecialchars(ucwords(str_replace(['By', 'Per'], ' ', $key))) ?></h2>
                        <table>
                            <thead>
                                <tr>
                                    <th>Label</th>
                                    <th>Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($allReports[$key] as $row): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['label'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($row['value'] ?? '') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>

            <div class="footer">
                <p>This report was automatically generated by AutoNexus</p>
            </div>
        </body>

        </html>
        <?php
        $html = ob_get_clean();

        $dompdf = new \Dompdf\Dompdf(['isRemoteEnabled' => true]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = "all_reports_" . date('Ymd_His') . ".pdf";
        $dompdf->stream($filename, ['Attachment' => true]);
        exit;
    }

    // Ensure the current session belongs to an admin user.
    private function requireAdmin(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $u = $_SESSION['user'] ?? null;
        if (!$u || (($u['role'] ?? '') !== 'admin')) {
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }
    }
}