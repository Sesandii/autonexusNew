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

        $this->view('customer/service-history/show', [
            'title' => 'Service Details',
            'service' => $service,
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

        // Generate PDF
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);

        $html = $this->generatePdfHtml($service);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'ServiceDetails_WO' . $service['work_order_id'] . '_' . date('Ymd') . '.pdf';
        $dompdf->stream($filename, ['Attachment' => true]);
    }

    private function generatePdfHtml(array $service): string
    {
        $date = date('M d, Y', strtotime($service['date']));
        $generatedDate = date('M d, Y');
        $price = number_format((float)($service['price'] ?? 0), 2);
        $make = htmlspecialchars((string)($service['make'] ?? ''));
        $model = htmlspecialchars((string)($service['model'] ?? ''));
        $plate = htmlspecialchars((string)($service['license_plate'] ?? 'N/A'));
        $year = htmlspecialchars((string)($service['vehicle_year'] ?? ''));
        $technician = htmlspecialchars((string)($service['technician'] ?? 'Not assigned'));
        $branch = htmlspecialchars((string)($service['branch_name'] ?? 'Main Branch'));
        $serviceType = htmlspecialchars((string)($service['service_type'] ?? 'N/A'));
        $status = ucfirst((string)($service['status'] ?? 'completed'));
        $description = nl2br(htmlspecialchars((string)($service['description'] ?? '')));
        $time = htmlspecialchars((string)($service['time'] ?? 'N/A'));
        $workOrderId = htmlspecialchars((string)($service['work_order_id']));

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Service Details - Work Order #{$workOrderId}</title>
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
        <p>Service Details Report</p>
    </div>

    <div class="section">
        <div class="section-title">Service Information</div>
        <div class="grid">
            <div class="grid-row">
                <div class="grid-cell">
                    <div class="label">Work Order #</div>
                    <div class="value">{$workOrderId}</div>
                </div>
                <div class="grid-cell">
                    <div class="label">Status</div>
                    <div class="value"><span class="status-badge">{$status}</span></div>
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
        <div class="section-title">Service Summary</div>
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
