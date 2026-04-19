<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice Preview</title>

    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/sidebar.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/invoicePreview.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>

<body>

<?php include APP_ROOT . '/views/layouts/receptionist-sidebar.php'; ?>

<div class="main">
<div class="details-section">

<div class="invoice-box">

<h2>Invoice Preview</h2>

<!-- CUSTOMER + VEHICLE -->
<div class="invoice-header">

    <div>
        <h4>Customer</h4>
        <p>
            <?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?><br>
            Phone: <?= htmlspecialchars($order['phone'] ?? 'N/A') ?>
        </p>
    </div>

    <div>
        <h4>Vehicle</h4>
        <p>
            <?= htmlspecialchars($order['vehicle_no'] ?? 'N/A') ?><br>
            <?= htmlspecialchars($order['make'] . ' ' . $order['model']) ?><br>
            <?= htmlspecialchars($order['year'] . ' • ' . $order['color']) ?>
        </p>
    </div>

</div>

<hr>

<!-- WORK ORDER INFO -->
<div class="invoice-meta">
    <p><strong>Work Order:</strong> #<?= $order['work_order_id'] ?></p>
    <p><strong>Completed:</strong> <?= $order['completed_at'] ?? 'N/A' ?></p>
</div>

<!-- SERVICE TABLE -->
<table class="invoice-table">
    <thead>
        <tr>
            <th>Description</th>
            <th class="right">Amount (Rs.)</th>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td><?= nl2br(htmlspecialchars($order['service_summary'] ?? 'Service Charges')) ?></td>
            <td class="right"><?= number_format($order['total_cost'], 2) ?></td>
        </tr>
    </tbody>
</table>

<!-- TOTALS -->
<div class="invoice-totals">

    <div>
        <p>Total</p>
        <p>Discount</p>
        <h3>Grand Total</h3>
    </div>

    <div class="right">
        <p>Rs. <?= number_format($order['total_cost'], 2) ?></p>
        <p>Rs. <?= number_format($order['discount'] ?? 0, 2) ?></p>
        <h3>Rs. <?= number_format($order['grand_total'] ?? $order['total_cost'], 2) ?></h3>
    </div>

</div>

<div class="invoice-actions">

    <form method="POST"
          action="<?= BASE_URL ?>/receptionist/billing/invoice/<?= $order['work_order_id'] ?>">

        <button type="submit" class="btn btn-primary">
    Generate Invoice
</button>

    </form>

    <a href="<?= BASE_URL ?>/receptionist/billing/create"
       class="btn btn-secondary">
       ← Back
    </a>

</div>

</div>
</div>
</div>

</body>
</html>