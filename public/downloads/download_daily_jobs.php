<?php
require __DIR__ . '/../../config/config.php'; // DB config
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../app/model/supervisor/Report.php';

use app\model\supervisor\Report;
use Dompdf\Dompdf;

// Create PDO
$pdo = new PDO(
    "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
    DB_USER,
    DB_PASS,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

// Pass PDO to Report
$reportModel = new Report($pdo);

// Filters
$reportDate = $_GET['report_date'] ?? null;
$mechanicCode = $_GET['mechanic_code'] ?? null;

// Optional format: pdf (default) or csv
$format = $_GET['format'] ?? 'pdf';

// Fetch actual report data
$dailyReport = $reportModel->getDailyJobCompletion($reportDate, $mechanicCode);

// CSV export
if ($format === 'csv') {
    $filename = 'Daily_Job_Report_' . date('Ymd_His') . '.csv';
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $output = fopen('php://output', 'w');

    // CSV headers
    fputcsv($output, [
        'Date',
        'Mechanic',
        'Total Completed',
        'On-Time',
        'Delayed',
        'Average Completion Time (mins)'
    ]);

    // CSV rows
    foreach ($dailyReport as $row) {
        // Format date properly
        $date = isset($row['report_date']) ? date('Y-m-d', strtotime($row['report_date'])) : '';
    
        fputcsv($output, [
            $date,
            $row['mechanic_code'],
            (int)$row['total_completed'],
            (int)$row['on_time'],
            (int)$row['delayed_count'],
            (float)$row['avg_completion_time']
        ]);
    
    }

    fclose($output);
    exit;
}

// PDF export (your existing code)
$html = '<h1>Daily Job Completion Report</h1>';
$html .= '<table border="1" cellpadding="5" cellspacing="0" width="100%">';
$html .= '<thead>
<tr>
<th>Date</th>
<th>Mechanic</th>
<th>Total Completed</th>
<th>On-Time</th>
<th>Delayed</th>
<th>Average Completion Time (mins)</th>
</tr>
</thead><tbody>';

foreach ($dailyReport as $row) {
    $html .= '<tr>';
    $html .= '<td>' . htmlspecialchars($row['report_date']) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['mechanic_code']) . '</td>';
    $html .= '<td>' . (int)$row['total_completed'] . '</td>';
    $html .= '<td style="color:green;font-weight:bold;">' . (int)$row['on_time'] . '</td>';
    $html .= '<td style="color:red;font-weight:bold;">' . (int)$row['delayed_count'] . '</td>';
    $html .= '<td>' . (float)$row['avg_completion_time'] . '</td>';
    $html .= '</tr>';
}

$html .= '</tbody></table>';

// Generate PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

$filename = 'Daily_Job_Report_' . date('Ymd_His') . '.pdf';
$dompdf->stream($filename, ["Attachment" => true]);
exit;
