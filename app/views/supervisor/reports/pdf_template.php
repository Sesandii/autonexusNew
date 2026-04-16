<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Helvetica', 'DejaVu Sans', sans-serif; color: #333; font-size: 13px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .section-title { background: #eee; padding: 5px; font-weight: bold; margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        .summary-box { border: 1px solid #ccc; padding: 10px; margin-top: 5px; background: #fafafa; }
        .footer { text-align: center; font-size: 10px; color: #777; margin-top: 30px; }
    .star { color: #ccc; font-size: 18px; }
    .star.active { color: #f1c40f; }
    .checklist-item { margin-bottom: 5px; font-size: 12px; }
    .check-box { 
        display: inline-block; 
        width: 12px; 
        height: 12px; 
        border: 1px solid #333; 
        text-align: center; 
        line-height: 12px; 
        margin-right: 5px;
    }
    .continuity-table { 
        width: 100%; 
        background-color: #f9f9f9; 
        border: 1px solid #eee; 
        margin-top: 10px;
    }
    </style>
</head>
<body>
    <div class="header">
        <h1>AutoNexus Service Report</h1>
        <p>Report ID: <?= $report['report_id'] ?> | Status: <?= strtoupper($report['status']) ?></p>
    </div>

    <div class="section-title">Job Information</div>
    <table>
        <tr>
            <th>Vehicle</th><td><?= htmlspecialchars($workOrder['license_plate']) ?></td>
            <th>Service</th><td><?= htmlspecialchars($workOrder['service_name']) ?></td>
        </tr>
        <tr>
            <th>Customer</th><td><?= htmlspecialchars($workOrder['customer_first_name'] . ' ' . $workOrder['customer_last_name']) ?></td>
            <th>Mechanic</th><td><?= htmlspecialchars($workOrder['mechanic_code']) ?></td>
        </tr>
    </table>

    <div class="section-title">Service Summary</div>
    <table>
        <thead>
            <tr>
                <th>Service Task</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($services as $s): ?>
                <tr>
                    <td><?= htmlspecialchars($s['item_name']) ?></td>
                    <td class="status-<?= strtolower($s['status']) ?>"><?= ucfirst($s['status']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="section-title">Final Inspection Details</div>
<table style="width: 100%; border: none;">
    <tr>
        <td style="width: 50%; border: none; vertical-align: top;">
            <strong>Quality Rating:</strong><br>
            <div class="rating-stars">
                <?php 
                $rating = (int)$report['quality_rating'];
                for ($i = 1; $i <= 5; $i++) {
                    echo ($i <= $rating) ? '<span class="star active">★</span>' : '<span class="star">★</span>';
                }
                ?>
            </div>
        </td>
        <td style="width: 50%; border: none; vertical-align: top;">
            <strong>Checklist Confirmation:</strong><br>
            <div class="checklist-item">
                <span class="check-box"><?= $report['checklist_verified'] ? '✓' : '' ?></span> Tasks verified
            </div>
            <div class="checklist-item">
                <span class="check-box"><?= $report['test_driven'] ? '✓' : '' ?></span> Vehicle test driven
            </div>
            <div class="checklist-item">
                <span class="check-box"><?= $report['concerns_addressed'] ? '✓' : '' ?></span> Customer concerns addressed
            </div>
        </td>
    </tr>
</table>

<div class="section-title">Final Inspection & Summary</div>
    <div class="summary-box">
        <strong>Notes:</strong><br>
        <?= nl2br(htmlspecialchars($report['inspection_notes'])) ?>
    </div>
    <div class="summary-box">
        <strong>Report Summary:</strong><br>
        <?= nl2br(htmlspecialchars($report['report_summary'])) ?>
    </div>

    <div class="section-title">Service Continuity</div>
<table class="continuity-table">
    <tr>
        <td style="padding: 15px;">
            <strong>Next Service Due:</strong> <?= htmlspecialchars($workOrder['last_service_mileage'] ?? 'Not set') ?> km
        </td>
        <td style="padding: 15px;">
            <strong>Service Interval:</strong> <?= htmlspecialchars($workOrder['service_interval_km'] ?? '5000') ?> km
        </td>
    </tr>
</table>

    <div class="section-title">Work Photos</div>
    <?php if (!empty($photos)): ?>
        <div class="photo-grid">
    <?php foreach ($photos as $photo): 
        $imagePath = realpath($_SERVER['DOCUMENT_ROOT'] . '/autonexus/public/' . $photo['file_path']);
    ?>
        <div style="margin-bottom: 15px; text-align: center;">
            <?php if ($imagePath && file_exists($imagePath)): ?>
                <img style = "width: 200px; height: auto; border: 2px solid #333;" src="<?= $imagePath ?>">
            <?php else: ?>
                <p style="color: red; font-size: 10px;">Image Not Found: <?= htmlspecialchars($photo['file_path']) ?></p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>
    <?php else: ?>
        <p>No photos available for this report.</p>
    <?php endif; ?>
    <div class="footer">
        Generated by AutoNexus System on <?= date('Y-m-d H:i') ?>
    </div>

</body>
</html>