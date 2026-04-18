<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/invoicePrint.css">
</head>

<body>

<div class="invoice-box">

    <h2>Vehicle Service Invoice</h2>

    <div class="row">
        <div>
            <strong>Customer:</strong><br>
            <?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?><br>
            Phone: <?= htmlspecialchars($order['phone'] ?? 'N/A') ?>
        </div>

        <div>
            <strong>Vehicle:</strong><br>
            <?= htmlspecialchars($order['vehicle_no'] ?? $order['license_plate'] ?? 'N/A') ?><br>
            <?= htmlspecialchars(($order['make'] ?? '') . ' ' . ($order['model'] ?? '')) ?><br>
            <?= htmlspecialchars(($order['year'] ?? '') . ' • ' . ($order['color'] ?? '')) ?>
        </div>
    </div>

    <p><strong>Work Order:</strong> #<?= htmlspecialchars($order['work_order_id']) ?></p>

    <p><strong>Date:</strong> <?= htmlspecialchars($order['completed_at'] ?? 'N/A') ?></p>

    <table>
        <tr>
            <th>Description</th>
            <th class="right">Amount (Rs.)</th>
        </tr>
        <tr>
            <td><?= nl2br(htmlspecialchars($order['service_summary'] ?? 'Service Charges')) ?></td>
            <td class="right"><?= number_format($order['total_cost'], 2) ?></td>
        </tr>
    </table>

    <div class="total">
        Grand Total: Rs. <?= number_format($order['total_cost'], 2) ?>
    </div>

    <!-- ACTION BUTTONS -->
    <div class="no-print">

        <button onclick="window.print()" class="btn btn-dark">
            Print Invoice
        </button>

    </div>

</div>

</body>
</html>