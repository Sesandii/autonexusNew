<?php
declare(strict_types=1);

namespace app\controllers\customer;

use app\core\Controller;
use app\model\customer\ServiceHistory;
use Dompdf\Dompdf;
use Dompdf\Options;

class ServiceHistoryController extends Controller
{
    public function index(): void
    {
        // Allow only logged-in customers
        $this->requireCustomer();

        // Use the parent helper method to get user_id
        $userId = $this->userId();

        $model = new ServiceHistory();
        $services = $model->getByCustomer($userId);

        // Debug logging
        error_log("ServiceHistoryController - UserID: $userId");
        error_log("ServiceHistoryController - Services found: " . count($services));
        if (!empty($services)) {
            error_log("First service: " . print_r($services[0], true));
        }

        $this->view('customer/service-history/index', [
            'title' => 'Service History',
            'services' => $services,
        ]);
    }

    public function show(int $id): void
    {
        $this->requireCustomer();
        $userId = $this->userId();

        $model = new ServiceHistory();
        $service = $model->getById($id);

        // Check if service exists and belongs to this customer
        if (!$service) {
            $_SESSION['flash'] = 'Service record not found.';
            header('Location: ' . BASE_URL . '/customer/service-history');
            exit;
        }

        // Verify ownership
        $customerServices = $model->getByCustomer($userId);
        $isOwner = false;
        foreach ($customerServices as $s) {
            if ((int)$s['work_order_id'] === $id) {
                $isOwner = true;
                break;
            }
        }

        if (!$isOwner) {
            $_SESSION['flash'] = 'You do not have access to this service record.';
            header('Location: ' . BASE_URL . '/customer/service-history');
            exit;
        }

        $serviceTasks = $model->getServiceTasks($id);
        $photos = [];
        if (!empty($service['report_id'])) {
            $photos = $model->getReportPhotos((int)$service['report_id']);
        }

        $this->view('customer/service-history/show', [
            'title' => !empty($service['report_id']) ? 'Final Service Report' : 'Service Details',
            'service' => $service,
            'serviceTasks' => $serviceTasks,
            'photos' => $photos,
        ]);
    }

    public function downloadPdf(int $id): void
    {
        $this->requireCustomer();
        $userId = $this->userId();

        $model = new ServiceHistory();
        $service = $model->getById($id);

        // Check if service exists
        if (!$service) {
            $_SESSION['flash'] = 'Service record not found.';
            header('Location: ' . BASE_URL . '/customer/service-history');
            exit;
        }

        // Verify ownership
        $customerServices = $model->getByCustomer($userId);
        $isOwner = false;
        foreach ($customerServices as $s) {
            if ((int)$s['work_order_id'] === $id) {
                $isOwner = true;
                break;
            }
        }

        if (!$isOwner) {
            $_SESSION['flash'] = 'You do not have access to this service record.';
            header('Location: ' . BASE_URL . '/customer/service-history');
            exit;
        }

        if (empty($service['report_id'])) {
            $_SESSION['flash'] = 'Final report is not available for this service yet.';
            header('Location: ' . BASE_URL . '/customer/service-history/' . $id);
            exit;
        }

        $serviceTasks = $model->getServiceTasks($id);

        // Generate PDF
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);

        $html = $this->generatePdfHtml($service, $serviceTasks);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'FinalReport_WO' . $service['work_order_id'] . '_' . date('Ymd') . '.pdf';
        $dompdf->stream($filename, ['Attachment' => true]);
    }

    private function generatePdfHtml(array $service, array $serviceTasks = []): string
    {
        $date = !empty($service['date'])
            ? date('M d, Y', strtotime((string)$service['date']))
            : 'N/A';
        $generatedDate = date('M d, Y');
        $price = number_format((float)($service['price'] ?? 0), 2);
        $make = htmlspecialchars((string)($service['make'] ?? ''));
        $model = htmlspecialchars((string)($service['model'] ?? ''));
        $plate = htmlspecialchars((string)($service['license_plate'] ?? 'N/A'));
        $year = htmlspecialchars((string)($service['vehicle_year'] ?? ''));
        $technician = htmlspecialchars((string)($service['technician'] ?? 'Not assigned'));
        $branch = htmlspecialchars((string)($service['branch_name'] ?? 'Main Branch'));
        $serviceType = htmlspecialchars((string)($service['service_type'] ?? 'N/A'));
        $status = htmlspecialchars(ucfirst((string)($service['status'] ?? 'completed')));
        $reportStatus = htmlspecialchars(ucfirst((string)($service['report_status'] ?? 'submitted')));
        $description = nl2br(htmlspecialchars((string)($service['description'] ?? '')));
        $reportSummaryRaw = trim((string)($service['report_summary'] ?? ''));
        if ($reportSummaryRaw === '') {
            $reportSummaryRaw = (string)($service['description'] ?? '');
        }
        $reportSummary = nl2br(htmlspecialchars($reportSummaryRaw));
        $inspectionNotes = nl2br(htmlspecialchars((string)($service['inspection_notes'] ?? '-')));
        $time = htmlspecialchars((string)($service['time'] ?? 'N/A'));
        $workOrderId = htmlspecialchars((string)($service['work_order_id']));
        $customerName = htmlspecialchars((string)($service['customer_name'] ?? 'Customer'));
        $qualityRating = max(0, min(5, (int)($service['quality_rating'] ?? 0)));
        $qualityText = $qualityRating . ' / 5';
        $checklistVerified = !empty($service['checklist_verified']) ? 'Yes' : 'No';
        $testDriven = !empty($service['test_driven']) ? 'Yes' : 'No';
        $concernsAddressed = !empty($service['concerns_addressed']) ? 'Yes' : 'No';
        $nextServiceDue = !empty($service['last_service_mileage'])
            ? htmlspecialchars((string)$service['last_service_mileage']) . ' km'
            : 'Not set';
        $serviceInterval = !empty($service['service_interval_km'])
            ? htmlspecialchars((string)$service['service_interval_km']) . ' km'
            : '5000 km';
        $recommendation = trim((string)($service['next_service_recommendation'] ?? ''));
        $recommendationHtml = '';
        if ($recommendation !== '') {
            $recommendationSafe = nl2br(htmlspecialchars($recommendation));
            $recommendationHtml = "
                <div class=\"section\">
                    <div class=\"section-title\">Next Service Recommendation</div>
                    <div class=\"description-box\">{$recommendationSafe}</div>
                </div>
            ";
        }

        $serviceTaskRows = '';
        if (!empty($serviceTasks)) {
            foreach ($serviceTasks as $task) {
                $itemName = htmlspecialchars((string)($task['item_name'] ?? 'Service task'));
                $itemStatus = htmlspecialchars(ucfirst((string)($task['status'] ?? 'pending')));
                $serviceTaskRows .= "<tr><td>{$itemName}</td><td>{$itemStatus}</td></tr>";
            }
        } else {
            $serviceTaskRows = '<tr><td colspan="2">No service task details available.</td></tr>';
        }

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Final Service Report - Work Order #{$workOrderId}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            padding: 40px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #dc2626;
        }
        .header h1 {
            color: #dc2626;
            font-size: 24px;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            font-size: 11px;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #111;
            margin-bottom: 12px;
            padding-bottom: 6px;
            border-bottom: 2px solid #fecaca;
        }
        .grid {
            display: table;
            width: 100%;
        }
        .grid-row {
            display: table-row;
        }
        .grid-cell {
            display: table-cell;
            width: 50%;
            padding: 8px 0;
            vertical-align: top;
        }
        .label {
            font-size: 10px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }
        .value {
            font-size: 13px;
            color: #111;
            font-weight: 500;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            background: #d1fae5;
            color: #065f46;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
        }
        .description-box {
            background: #f9fafb;
            padding: 15px;
            border-left: 3px solid #dc2626;
            margin-top: 10px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>AutoNexus</h1>
        <p>Customer Final Service Report</p>
    </div>

    <div class="section">
        <div class="section-title">Job Inspection & Reporting</div>
        <div class="grid">
            <div class="grid-row">
                <div class="grid-cell">
                    <div class="label">Work Order #</div>
                    <div class="value">{$workOrderId}</div>
                </div>
                <div class="grid-cell">
                    <div class="label">Service Status</div>
                    <div class="value"><span class="status-badge">{$status}</span></div>
                </div>
            </div>
            <div class="grid-row">
                <div class="grid-cell">
                    <div class="label">Customer</div>
                    <div class="value">{$customerName}</div>
                </div>
                <div class="grid-cell">
                    <div class="label">Final Report Status</div>
                    <div class="value">{$reportStatus}</div>
                </div>
            </div>
            <div class="grid-row">
                <div class="grid-cell">
                    <div class="label">Service Type</div>
                    <div class="value">{$serviceType}</div>
                </div>
                <div class="grid-cell">
                    <div class="label">Total Cost</div>
                    <div class="value">Rs. {$price}</div>
                </div>
            </div>
            <div class="grid-row">
                <div class="grid-cell">
                    <div class="label">Date</div>
                    <div class="value">{$date}</div>
                </div>
                <div class="grid-cell">
                    <div class="label">Time</div>
                    <div class="value">{$time}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Vehicle Information</div>
        <div class="grid">
            <div class="grid-row">
                <div class="grid-cell">
                    <div class="label">License Plate</div>
                    <div class="value">{$plate}</div>
                </div>
                <div class="grid-cell">
                    <div class="label">Make & Model</div>
                    <div class="value">{$make} {$model}</div>
                </div>
            </div>
            <div class="grid-row">
                <div class="grid-cell">
                    <div class="label">Year</div>
                    <div class="value">{$year}</div>
                </div>
                <div class="grid-cell"></div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Service Provider</div>
        <div class="grid">
            <div class="grid-row">
                <div class="grid-cell">
                    <div class="label">Technician</div>
                    <div class="value">{$technician}</div>
                </div>
                <div class="grid-cell">
                    <div class="label">Branch</div>
                    <div class="value">{$branch}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Service Tasks</div>
        <table style="width:100%; border-collapse: collapse; margin-top: 8px;">
            <thead>
                <tr>
                    <th style="text-align:left; border-bottom:1px solid #e5e7eb; padding:8px 6px;">Task</th>
                    <th style="text-align:left; border-bottom:1px solid #e5e7eb; padding:8px 6px;">Status</th>
                </tr>
            </thead>
            <tbody>
                {$serviceTaskRows}
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Final Inspection</div>
        <div class="description-box">
            <div style="margin-bottom:8px;"><strong>Inspection Notes:</strong><br>{$inspectionNotes}</div>
            <div><strong>Quality Rating:</strong> {$qualityText}</div>
            <div style="margin-top:8px;"><strong>Tasks Verified:</strong> {$checklistVerified}</div>
            <div><strong>Vehicle Test Driven:</strong> {$testDriven}</div>
            <div><strong>Customer Concerns Addressed:</strong> {$concernsAddressed}</div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Final Report Summary</div>
        <div class="description-box">
            {$reportSummary}
        </div>
    </div>

    <div class="section">
        <div class="section-title">Service Continuity</div>
        <div class="grid">
            <div class="grid-row">
                <div class="grid-cell">
                    <div class="label">Next Service Due</div>
                    <div class="value">{$nextServiceDue}</div>
                </div>
                <div class="grid-cell">
                    <div class="label">Service Interval</div>
                    <div class="value">{$serviceInterval}</div>
                </div>
            </div>
        </div>
    </div>

    {$recommendationHtml}

    <div class="section">
        <div class="section-title">Internal Service Notes</div>
        <div class="description-box">
            {$description}
        </div>
    </div>

    <div class="footer">
        <p>Generated on: {$generatedDate} | AutoNexus Vehicle Service Management System</p>
        <p>This is a computer-generated document. No signature required.</p>
    </div>
</body>
</html>
HTML;
    }
}
