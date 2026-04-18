<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Billing & Payments - AutoNexus</title>

  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/sidebar.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/billing.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

<?php include APP_ROOT . '/views/layouts/receptionist-sidebar.php'; ?>

<div class="main">

<header class="header-bar">
    <h1>Billing & Payments</h1>
    <a href="<?= BASE_URL ?>/receptionist/billing/create" class="create-btn">+ Create Invoice</a>
</header>

<nav class="tab-nav">
  <ul class="tab-list">
    <li class="tab-item active" data-tab="invoice">Invoices</li>
    <li class="tab-item" data-tab="T_history">Transaction History</li>
  </ul>
</nav>

<!-- ================= INVOICES ================= -->
<section id="invoice" class="tab-content active">

<!-- FILTER DROPDOWN -->
<form method="GET" action="<?= BASE_URL ?>/receptionist/billing" class="filter-dropdown">
    <select name="status" onchange="this.form.submit()">

        <option value="" <?= empty($status) ? 'selected' : '' ?>>All</option>

        <option value="paid" <?= ($status ?? '') === 'paid' ? 'selected' : '' ?>>Paid</option>

        <option value="unpaid" <?= ($status ?? '') === 'unpaid' ? 'selected' : '' ?>>Unpaid</option>

        <option value="cancelled" <?= ($status ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>

    </select>
</form>

<table>
    <thead>
        <tr>
            <th>Invoice ID</th>
            <th>Customer</th>
            <th>Vehicle</th>
            <th>Date</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>
    <?php if (!empty($invoices)): ?>
        <?php foreach ($invoices as $inv): ?>
            <tr>

                <td><?= htmlspecialchars($inv['invoice_no']) ?></td>

                <td><?= htmlspecialchars($inv['first_name'].' '.$inv['last_name']) ?></td>

                <td>
                    <?= htmlspecialchars($inv['vehicle_no'].' ('.$inv['make'].' '.$inv['model'].')') ?>
                </td>

                <td><?= htmlspecialchars($inv['issued_at']) ?></td>

                <td>Rs. <?= number_format($inv['grand_total'], 2) ?></td>

                <td>
                    <?php if ($inv['status'] === 'paid'): ?>
                        <span class="badge badge-success">Paid</span>
                    <?php elseif ($inv['status'] === 'unpaid'): ?>
                        <span class="badge badge-warning">Unpaid</span>
                    <?php else: ?>
                        <span class="badge badge-danger">Cancelled</span>
                    <?php endif; ?>
                </td>

                <td>
                    <!-- PRINT -->
                    <a href="<?= BASE_URL ?>/receptionist/billing/printInvoice?id=<?= $inv['work_order_id'] ?>"
                       target="_blank"
                       title="Print Invoice">
                        <i class="fas fa-print"></i>
                    </a>

                    <!-- MARK AS PAID -->
                    <?php if ($inv['status'] === 'unpaid'): ?>
                        <a href="<?= BASE_URL ?>/receptionist/billing/markAsPaid?id=<?= $inv['invoice_id'] ?>"
                           title="Mark as Paid"
                           onclick="return confirm('Mark this invoice as PAID?')">
                            <i class="fas fa-file-invoice-dollar" style="color:green;"></i>
                        </a>
                    <?php endif; ?>
                </td>

            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="7" style="text-align:center;">No invoices found</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>

</section>

<!-- ================= TRANSACTIONS ================= -->
<section id="T_history" class="tab-content">

<div class="T_history">

    <div class="search-filter">
        <input type="text" placeholder="Search invoice..." class="search-bar">
        <select>
            <option>All Transactions</option>
            <option>Last 7 Days</option>
            <option>Last 30 Days</option>
        </select>
    </div>

    <table>
        <thead>
            <tr>
                <th>Transaction ID</th>
                <th>Customer</th>
                <th>Vehicle</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
        <?php if (!empty($paidInvoices)): ?>
            <?php foreach ($paidInvoices as $txn): ?>
                <tr>

                    <td><?= htmlspecialchars($txn['invoice_no']) ?></td>

                    <td><?= htmlspecialchars($txn['first_name'].' '.$txn['last_name']) ?></td>

                    <td>
                        <?= htmlspecialchars($txn['vehicle_no'].' ('.$txn['make'].' '.$txn['model'].')') ?>
                    </td>

                    <td><?= date('Y-m-d', strtotime($txn['issued_at'])) ?></td>

                    <td>Rs. <?= number_format($txn['grand_total'], 2) ?></td>

                    <td><span class="badge badge-success">Paid</span></td>

                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" style="text-align:center;">No paid transactions found</td>
            </tr>
        <?php endif; ?>
        </tbody>

    </table>

</div>

</section>

<script src="<?= BASE_URL ?>/public/assets/js/receptionist/billing.js"></script>

</div>

</body>
</html>