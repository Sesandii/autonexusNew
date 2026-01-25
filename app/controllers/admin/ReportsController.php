<?php
declare(strict_types=1);

namespace app\controllers\admin;

use app\core\Controller;
use app\model\admin\Reports;

class ReportsController extends Controller
{
    private Reports $reports;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->requireAdmin();
        $this->reports = new Reports();
    }

    /** GET /admin/admin-viewreports */
    public function index(): void
    {
        $filters = [
            'from'      => $_GET['from'] ?? '',
            'to'        => $_GET['to'] ?? '',
            'branch_id' => $_GET['branch_id'] ?? '',
            'group'     => $_GET['group'] ?? 'month',
        ];

        $branches     = $this->reports->branches();
        $serviceTypes = $this->reports->serviceTypes();

        // SERVICE PERFORMANCE
        $topServices        = $this->reports->topServices($filters);
        $serviceTrend       = $this->reports->serviceTrend($filters);
        $serviceTypeDist    = $this->reports->serviceTypeDistribution($filters);
        $avgCompletionMins  = $this->reports->avgCompletionMinutes($filters);

        // REVENUE
        $revenueTrend         = $this->reports->revenueTrend($filters);
        $revenueByBranch      = $this->reports->revenueByBranch($filters);
        $revenueByServiceType = $this->reports->revenueByServiceType($filters);
        $avgInvoice           = $this->reports->avgInvoiceValue($filters);

        // APPOINTMENTS
        $apptStatus = $this->reports->appointmentStatusCounts($filters);
        $apptByHour = $this->reports->appointmentsByHour($filters);
        $apptTrend  = $this->reports->appointmentsTrend($filters);

        // BRANCH PERFORMANCE
        $branchCompleted = $this->reports->branchCompletedServices($filters);
        $branchRating    = $this->reports->branchAvgRating($filters);

        // STAFF
        $jobsPerMechanic      = $this->reports->jobsPerMechanic($filters);
        $submittedByManagers  = $this->reports->servicesSubmittedByManagers($filters);

        // FEEDBACK
        $ratingDist    = $this->reports->ratingDistribution($filters);
        $feedbackTrend = $this->reports->feedbackTrend($filters);
        $lowestRated   = $this->reports->lowestRatedServices($filters);

        // APPROVAL
        $approvalStatus   = $this->reports->approvalStatusCounts($filters);
        $avgApprovalHours = $this->reports->avgApprovalHours($filters);

        $payload = [
            'service' => [
                'topServices'        => $topServices,
                'trend'              => $serviceTrend,
                'typeDist'           => $serviceTypeDist,
                'avgCompletionMins'  => $avgCompletionMins,
            ],
            'revenue' => [
                'trend'        => $revenueTrend,
                'byBranch'     => $revenueByBranch,
                'byServiceType'=> $revenueByServiceType,
                'avgInvoice'   => $avgInvoice,
            ],
            'appointments' => [
                'status' => $apptStatus,
                'byHour' => $apptByHour,
                'trend'  => $apptTrend,
            ],
            'branches' => [
                'completed' => $branchCompleted,
                'avgRating' => $branchRating,
            ],
            'staff' => [
                'jobsPerMechanic'        => $jobsPerMechanic,
                'submittedByManagers'    => $submittedByManagers,
            ],
            'feedback' => [
                'ratingDist'   => $ratingDist,
                'trend'        => $feedbackTrend,
                'lowestRated'  => $lowestRated,
            ],
            'approval' => [
                'statusCounts'    => $approvalStatus,
                'avgApprovalHours'=> $avgApprovalHours,
            ],
        ];

        $this->view('admin/admin-viewreports/index', [
            'pageTitle'       => 'Reports - AutoNexus',
            'current'         => 'reports',
            'filters'         => $filters,
            'branches'        => $branches,
            'serviceTypes'    => $serviceTypes,
            'reportDataJson'  => json_encode($payload, JSON_UNESCAPED_UNICODE),
        ]);
    }

    /** GET /admin/admin-viewreports/export?key=topServices&from=... */
    public function export(): void
    {
        $this->requireAdmin();

        $key = $_GET['key'] ?? '';
        $filters = [
            'from'      => $_GET['from'] ?? '',
            'to'        => $_GET['to'] ?? '',
            'branch_id' => $_GET['branch_id'] ?? '',
            'group'     => $_GET['group'] ?? 'month',
        ];

        $rows = $this->reports->exportDataset($key, $filters);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="report_'.$key.'_'.date('Ymd_His').'.csv"');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['label', 'value']);
        foreach ($rows as $r) {
            fputcsv($out, [$r['label'] ?? '', $r['value'] ?? '']);
        }
        fclose($out);
        exit;
    }

    /** GET /admin/admin-viewreports/export-pdf?key=topServices&from=... */
    public function exportPdf(): void
    {
        $this->requireAdmin();

        // Dompdf
        $autoload = BASE_PATH . '/vendor/autoload.php';
        if (!is_file($autoload)) {
            http_response_code(500);
            echo "<pre>Missing vendor/autoload.php. Run: composer require dompdf/dompdf</pre>";
            exit;
        }
        require_once $autoload;

        $key = $_GET['key'] ?? '';
        $filters = [
            'from'      => $_GET['from'] ?? '',
            'to'        => $_GET['to'] ?? '',
            'branch_id' => $_GET['branch_id'] ?? '',
            'group'     => $_GET['group'] ?? 'month',
        ];

        $rows = $this->reports->exportDataset($key, $filters);

        // data for PDF template
        $pdfTitle = 'AutoNexus Report: ' . $key;
        $generatedAt = date('Y-m-d H:i:s');

        // Render HTML view into a string
        ob_start();
        $B = rtrim(BASE_URL, '/');
        $data = [
            'pdfTitle'    => $pdfTitle,
            'generatedAt' => $generatedAt,
            'key'         => $key,
            'filters'     => $filters,
            'rows'        => $rows,
            'baseUrl'     => $B,
        ];
        extract($data);
        require APP_ROOT . '/views/admin/admin-viewreports/pdf.php';
        $html = ob_get_clean();

        $dompdf = new \Dompdf\Dompdf([
            'isRemoteEnabled' => true, // allow loading CSS/logo by URL if needed
        ]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = "report_{$key}_" . date('Ymd_His') . ".pdf";
        $dompdf->stream($filename, ['Attachment' => true]);
        exit;
    }

    private function requireAdmin(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $u = $_SESSION['user'] ?? null;
        if (!$u || (($u['role'] ?? '') !== 'admin')) {
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }
    }
}
