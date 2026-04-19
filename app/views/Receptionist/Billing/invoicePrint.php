<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/invoicePrint.css">
</head>
<body>

<?php if (!empty($error) || empty($order)): ?>
    <div style="padding:2rem; font-family:sans-serif; color:red;">
        <p><?= htmlspecialchars($error ?? 'Invoice data could not be loaded.') ?></p>
        <a href="<?= BASE_URL ?>/receptionist/billing">← Back to Billing</a>
    </div>
<?php else: ?>

<div class="invoice-box">

    <h2>Vehicle Service Invoice</h2>

    <p><strong>Invoice No:</strong> <?= htmlspecialchars($order['invoice_no']) ?></p>

    <div class="row">
        <div>
            <strong>Customer:</strong><br>
            <?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?><br>
            Phone: <?= htmlspecialchars($order['phone'] ?? 'N/A') ?>
        </div>

        <div>
            <strong>Vehicle:</strong><br>
            <?= htmlspecialchars($order['vehicle_no'] ?? 'N/A') ?><br>
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
        Grand Total: Rs. <?= number_format($order['grand_total'] ?? $order['total_cost'], 2) ?>
    </div>

    <!-- Buttons hidden when printing -->
    <div class="no-print" style="margin-top:1.5rem; display:flex; gap:1rem;">

        <button onclick="window.print()" class="btn btn-dark">
            🖨 Print / Save as PDF
        </button>

        <a href="<?= BASE_URL ?>/receptionist/billing" class="btn btn-secondary">
            ← Back to Billing
        </a>

    </div>

</div>

<?php endif; ?>

</body>
</html>