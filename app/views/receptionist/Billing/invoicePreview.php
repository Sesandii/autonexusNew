<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice Preview</title>

    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/sidebar.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/invoicePreview.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>

<?php include APP_ROOT . '/views/layouts/receptionist-sidebar.php'; ?>

<div class="main">
    <div class="details-section">

        <h3>Invoice Preview</h3>

        <div class="invoice-box">

            <h2>Invoice</h2>

            <!-- Header -->
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
                        <?= htmlspecialchars($order['license_plate'] ?? $order['vehicle_no']) ?><br>
                        <?= htmlspecialchars($order['make'] . ' ' . $order['model']) ?><br>
                        <?= htmlspecialchars($order['year'] . ' • ' . $order['color']) ?>
                    </p>
                </div>
            </div>

            <hr>

            <!-- Work order info -->
            <div class="invoice-meta">
                <p><strong>Work Order ID:</strong> #<?= $order['work_order_id'] ?></p>
                <p><strong>Completed At:</strong> <?= $order['completed_at'] ?? 'N/A' ?></p>
            </div>

            <!-- Service summary -->
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

            <!-- Totals -->
            <div class="invoice-totals">
                <div>
                    <p>Total</p>
                    <p>Discount</p>
                    <h3>Grand Total</h3>
                </div>
                <div class="right">
                    <p>Rs. <?= number_format($order['total_cost'], 2) ?></p>
                    <p>Rs. 0.00</p>
                    <h3>Rs. <?= number_format($order['total_cost'], 2) ?></h3>
                </div>
            </div>

     <form method="POST"
      action="<?= BASE_URL ?>/receptionist/billing/invoice/<?= $order['work_order_id'] ?>"
      target="_blank">

    <button type="submit" class="btn btn-primary">
        Generate Invoice
    </button>

</form>
                <!-- Back button -->
                <a href="<?= BASE_URL ?>/receptionist/billing/create" class="btn btn-secondary">
                    ← Back
                </a>
            </div>

        </div>
    </div>
</div>

<?php if (!empty($autoPrint)): ?>
<script>
    window.onload = function () {
        window.print();
    };
</script>
<?php endif; ?>

</body>
</html>
