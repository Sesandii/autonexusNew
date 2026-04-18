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

        $topServices       = $this->reports->topServices($filters);
        $serviceTrend      = $this->reports->serviceTrend($filters);
        $serviceTypeDist   = $this->reports->serviceTypeDistribution($filters);
        $avgCompletionMins = $this->reports->avgCompletionMinutes($filters);
        $weekdayDemand     = $this->reports->serviceDemandByWeekday($filters);
        $seasonalDemand    = $this->reports->seasonalDemand($filters);
        $avgWaitingMins    = $this->reports->averageWaitingTimeBeforeStart($filters);
        $turnaroundByBranch = $this->reports->turnaroundTimeByBranch($filters);
        $repeatCustomerFrequency = $this->reports->repeatCustomerFrequency($filters);
        $mostRebookedServices    = $this->reports->mostRebookedServices($filters);

        $revenueTrend   = $this->reports->revenueTrend($filters);
        $costTrend      = $this->reports->costTrend($filters);
        $profitTrend    = $this->reports->profitTrend($filters);
        $revenueByBranch = $this->reports->revenueByBranch($filters);
        $revenueByServiceType = $this->reports->revenueByServiceType($filters);
        $avgInvoice = $this->reports->avgInvoiceValue($filters);
        $unpaidInvoiceAging = $this->reports->unpaidInvoiceAging($filters);
        $paymentMethodBreakdown = $this->reports->paymentMethodBreakdown($filters);
        $paymentStatusBreakdown = $this->reports->paymentStatusBreakdown($filters);
        $avgRevenuePerAppointment = $this->reports->avgRevenuePerAppointment($filters);
        $avgRevenuePerCustomer = $this->reports->avgRevenuePerCustomer($filters);
        $branchPaymentCollectionPerformance = $this->reports->branchPaymentCollectionPerformance($filters);

        $apptStatus = $this->reports->appointmentStatusCounts($filters);
        $apptByHour = $this->reports->appointmentsByHour($filters);
        $apptTrend  = $this->reports->appointmentsTrend($filters);
        $cancellationTrend = $this->reports->cancellationTrend($filters);

        $branchCompleted = $this->reports->branchCompletedServices($filters);
        $branchRating    = $this->reports->branchAvgRating($filters);
        $branchCapacityUtilization = $this->reports->branchCapacityUtilization($filters);
        $branchStaffingVsWorkload  = $this->reports->branchStaffingVsWorkload($filters);
        $branchServiceCoverageMatrix = $this->reports->branchServiceCoverageMatrix($filters);
        $branchComplaintRate = $this->reports->branchComplaintRate($filters);
        $branchApprovalRejectionRate = $this->reports->branchApprovalRejectionRate($filters);
        $branchQualityScore = $this->reports->branchQualityScore($filters);
        $underperformingBranches = $this->reports->underperformingBranches($filters);

        $jobsPerMechanic = $this->reports->jobsPerMechanic($filters);
        $submittedByManagers = $this->reports->servicesSubmittedByManagers($filters);
        $managerApprovalDecisions = $this->reports->managerApprovalDecisions($filters);
        $mechanicQualityOutcomes = $this->reports->mechanicQualityOutcomes($filters);
        $staffComplaintAssociation = $this->reports->staffComplaintAssociation($filters);
        $avgJobsPerDayPerMechanic = $this->reports->avgJobsPerDayPerMechanic($filters);
        $delayedWorkOrdersByMechanic = $this->reports->delayedWorkOrdersByMechanic($filters);

        $ratingDist    = $this->reports->ratingDistribution($filters);
        $feedbackTrend = $this->reports->feedbackTrend($filters);
        $lowestRated   = $this->reports->lowestRatedServices($filters);
        $branchRatingTrend = $this->reports->branchRatingTrend($filters);
        $ratingByServiceType = $this->reports->ratingByServiceType($filters);
        $feedbackResponseTurnaround = $this->reports->feedbackResponseTurnaround($filters);
        $mostPraisedServices = $this->reports->mostPraisedServices($filters);
        $repeatNegativeFeedbackCustomers = $this->reports->repeatNegativeFeedbackCustomers($filters);

        $approvalStatus   = $this->reports->approvalStatusCounts($filters);
        $avgApprovalHours = $this->reports->avgApprovalHours($filters);

        $complaintTrend = $this->reports->complaintTrend($filters);
        $complaintResolutionTrend = $this->reports->complaintResolutionTrend($filters);
        $complaintClosureRateByBranch = $this->reports->complaintClosureRateByBranch($filters);
        $complaintPriorityAnalysis = $this->reports->complaintPriorityAnalysis($filters);
        $mostComplainedServices = $this->reports->mostComplainedServices($filters);
        $mostComplainedBranches = $this->reports->mostComplainedBranches($filters);
        $mostComplainedStaff = $this->reports->mostComplainedStaff($filters);
        $slaBreachTrend = $this->reports->slaBreachTrend($filters);

        $payload = [
            'service' => [
                'topServices'            => $topServices,
                'trend'                  => $serviceTrend,
                'typeDist'               => $serviceTypeDist,
                'avgCompletionMins'      => $avgCompletionMins,
                'weekdayDemand'          => $weekdayDemand,
                'seasonalDemand'         => $seasonalDemand,
                'avgWaitingMins'         => $avgWaitingMins,
                'turnaroundByBranch'     => $turnaroundByBranch,
                'repeatCustomerFrequency'=> $repeatCustomerFrequency,
                'mostRebookedServices'   => $mostRebookedServices,
            ],
            'revenue' => [
                'trend'                  => $revenueTrend,
                'costTrend'              => $costTrend,
                'profitTrend'            => $profitTrend,
                'byBranch'               => $revenueByBranch,
                'byServiceType'          => $revenueByServiceType,
                'avgInvoice'             => $avgInvoice,
                'unpaidInvoiceAging'     => $unpaidInvoiceAging,
                'paymentMethodBreakdown' => $paymentMethodBreakdown,
                'paymentStatusBreakdown' => $paymentStatusBreakdown,
                'avgRevenuePerAppointment' => $avgRevenuePerAppointment,
                'avgRevenuePerCustomer'  => $avgRevenuePerCustomer,
                'branchPaymentCollectionPerformance' => $branchPaymentCollectionPerformance,
            ],
            'appointments' => [
                'status'            => $apptStatus,
                'byHour'            => $apptByHour,
                'trend'             => $apptTrend,
                'cancellationTrend' => $cancellationTrend,
            ],
            'branches' => [
                'completed'               => $branchCompleted,
                'avgRating'               => $branchRating,
                'capacityUtilization'     => $branchCapacityUtilization,
                'staffingVsWorkload'      => $branchStaffingVsWorkload,
                'serviceCoverageMatrix'   => $branchServiceCoverageMatrix,
                'complaintRate'           => $branchComplaintRate,
                'approvalRejectionRate'   => $branchApprovalRejectionRate,
                'qualityScore'            => $branchQualityScore,
                'underperformingBranches' => $underperformingBranches,
            ],
            'staff' => [
                'jobsPerMechanic'         => $jobsPerMechanic,
                'submittedByManagers'     => $submittedByManagers,
                'managerApprovalDecisions'=> $managerApprovalDecisions,
                'mechanicQualityOutcomes' => $mechanicQualityOutcomes,
                'staffComplaintAssociation'=> $staffComplaintAssociation,
                'avgJobsPerDayPerMechanic'=> $avgJobsPerDayPerMechanic,
                'delayedWorkOrdersByMechanic' => $delayedWorkOrdersByMechanic,
            ],
            'feedback' => [
                'ratingDist'                   => $ratingDist,
                'trend'                        => $feedbackTrend,
                'lowestRated'                  => $lowestRated,
                'branchRatingTrend'            => $branchRatingTrend,
                'ratingByServiceType'          => $ratingByServiceType,
                'feedbackResponseTurnaround'   => $feedbackResponseTurnaround,
                'mostPraisedServices'          => $mostPraisedServices,
                'repeatNegativeFeedbackCustomers' => $repeatNegativeFeedbackCustomers,
            ],
            'approval' => [
                'statusCounts'     => $approvalStatus,
                'avgApprovalHours' => $avgApprovalHours,
            ],
            'complaints' => [
                'trend'                  => $complaintTrend,
                'resolutionTrend'        => $complaintResolutionTrend,
                'closureRateByBranch'    => $complaintClosureRateByBranch,
                'priorityAnalysis'       => $complaintPriorityAnalysis,
                'mostComplainedServices' => $mostComplainedServices,
                'mostComplainedBranches' => $mostComplainedBranches,
                'mostComplainedStaff'    => $mostComplainedStaff,
                'slaBreachTrend'         => $slaBreachTrend,
            ],
        ];

        $this->view('admin/admin-viewreports/index', [
            'pageTitle'      => 'Reports - AutoNexus',
            'current'        => 'reports',
            'filters'        => $filters,
            'branches'       => $branches,
            'serviceTypes'   => $serviceTypes,
            'reportDataJson' => json_encode($payload, JSON_UNESCAPED_UNICODE),
        ]);
    }

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
        header('Content-Disposition: attachment; filename="report_' . $key . '_' . date('Ymd_His') . '.csv"');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['label', 'value']);
        foreach ($rows as $r) {
            fputcsv($out, [$r['label'] ?? '', $r['value'] ?? '']);
        }
        fclose($out);
        exit;
    }

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
            'from'      => $_GET['from'] ?? '',
            'to'        => $_GET['to'] ?? '',
            'branch_id' => $_GET['branch_id'] ?? '',
            'group'     => $_GET['group'] ?? 'month',
        ];

        $rows = $this->reports->exportDataset($key, $filters);

        $pdfTitle = 'AutoNexus Report: ' . $key;
        $generatedAt = date('Y-m-d H:i:s');

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
            'isRemoteEnabled' => true,
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