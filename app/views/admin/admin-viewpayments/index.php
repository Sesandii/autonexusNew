<?php /* Admin view: renders admin-viewpayments/index page. */ ?>
<?php $B = rtrim(BASE_URL, '/'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AutoNexus - Payments Management</title>

  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= $B ?>/public/assets/css/admin-admin-viewpaymentsindex.css?v=1">
</head>

<body>
  <?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

  <main class="main-content">
    <section class="management">
      <div class="management-header">
        <div>
          <h2>Payments Management</h2>
          <p class="management-subtitle">Invoice and payment visibility with status and method filtering.</p>
        </div>
      </div>

      <div class="summary-grid">
        <div class="summary-card">
          <div class="summary-head"><span class="summary-icon"><i class="fa-solid fa-sack-dollar"></i></span></div>
          <p>Total Collected</p>
          <h3>Rs. <?= number_format((float) $summary['collected_total'], 2) ?></h3>
        </div>
        <div class="summary-card">
          <div class="summary-head"><span class="summary-icon"><i class="fa-solid fa-circle-check"></i></span></div>
          <p>Successful Payments</p>
          <h3><?= (int) $summary['success_count'] ?></h3>
        </div>
        <div class="summary-card">
          <div class="summary-head"><span class="summary-icon"><i class="fa-solid fa-hourglass-half"></i></span></div>
          <p>Pending Payments</p>
          <h3><?= (int) $summary['pending_count'] ?></h3>
        </div>
        <div class="summary-card">
          <div class="summary-head"><span class="summary-icon"><i class="fa-solid fa-circle-xmark"></i></span></div>
          <p>Failed Payments</p>
          <h3><?= (int) $summary['failed_count'] ?></h3>
        </div>
        <div class="summary-card">
          <div class="summary-head"><span class="summary-icon"><i class="fa-solid fa-money-bill-wave"></i></span></div>
          <p>Cash Payments</p>
          <h3>Rs. <?= number_format((float) $summary['cash_total'], 2) ?></h3>
        </div>
        <div class="summary-card">
          <div class="summary-head"><span class="summary-icon"><i class="fa-solid fa-credit-card"></i></span></div>
          <p>Card Payments</p>
          <h3>Rs. <?= number_format((float) $summary['card_total'], 2) ?></h3>
        </div>
        <div class="summary-card">
          <div class="summary-head"><span class="summary-icon"><i class="fa-solid fa-globe"></i></span></div>
          <p>Online Payments</p>
          <h3>Rs. <?= number_format((float) $summary['online_total'], 2) ?></h3>
        </div>
      </div>

      <div class="filters-row">
        <form method="GET" action="<?= $B ?>/admin/admin-viewpayments">
          <input class="search-input" type="text" name="q" value="<?= htmlspecialchars($filters['q'] ?? '') ?>"
            placeholder="Search invoice no, payment reference, customer, service...">

          <select name="status">
            <option value="">All Payment Status</option>
            <option value="success" <?= (($filters['status'] ?? '') === 'success') ? 'selected' : '' ?>>Successful</option>
            <option value="pending" <?= (($filters['status'] ?? '') === 'pending') ? 'selected' : '' ?>>Pending</option>
            <option value="failed" <?= (($filters['status'] ?? '') === 'failed') ? 'selected' : '' ?>>Failed</option>
          </select>

          <select name="method">
            <option value="">All Methods</option>
            <option value="cash" <?= (($filters['method'] ?? '') === 'cash') ? 'selected' : '' ?>>Cash</option>
            <option value="card" <?= (($filters['method'] ?? '') === 'card') ? 'selected' : '' ?>>Card</option>
            <option value="online" <?= (($filters['method'] ?? '') === 'online') ? 'selected' : '' ?>>Online</option>
          </select>

          <button class="add-btn" type="submit"><i class="fa-solid fa-filter"></i> Apply Filters</button>
        </form>
      </div>

      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Payment</th>
              <th>Invoice</th>
              <th>Customer / Service</th>
              <th>Method</th>
              <th>Amount</th>
              <th>Status</th>
              <th>Invoice Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($records)): ?>
              <tr>
                <td colspan="8" class="empty-row"><i class="fa-regular fa-face-frown"></i> No payment records found.</td>
              </tr>
            <?php endif; ?>

            <?php foreach ($records as $row): ?>
              <tr>
                <td>
                  <strong>#<?= (int) $row['payment_id'] ?></strong><br>
                  <small><?= date('M d, Y h:i A', strtotime((string) $row['payment_date'])) ?></small><br>
                  <small><?= htmlspecialchars($row['reference_no'] ?: '-') ?></small>
                </td>
                <td>
                  <strong><?= htmlspecialchars($row['invoice_no']) ?></strong><br>
                  <small>Invoice Total: Rs.<?= number_format((float) $row['grand_total'], 2) ?></small>
                </td>
                <td>
                  <strong><?= htmlspecialchars($row['customer_name']) ?></strong><br>
                  <small><?= htmlspecialchars($row['service_name']) ?></small><br>
                  <small><?= htmlspecialchars($row['branch_name'] ?: '-') ?></small>
                </td>
                <td><?= ucfirst(htmlspecialchars((string) $row['method'])) ?></td>
                <td>Rs.<?= number_format((float) $row['amount'], 2) ?></td>
                <td>
                  <span class="status-pill status--<?= htmlspecialchars((string) $row['payment_status']) ?>">
                    <span class="dot"></span>
                    <?= ucfirst(htmlspecialchars((string) $row['payment_status'])) ?>
                  </span>
                </td>
                <td>
                  <span class="invoice-status"><?= ucfirst(htmlspecialchars((string) $row['invoice_status'])) ?></span>
                </td>
                <td>
                  <div class="action-inline">
                    <?php if ((($row['invoice_status'] ?? '') !== 'cancelled') && (($row['payment_status'] ?? '') !== 'success')): ?>
                      <form method="POST" action="<?= $B ?>/admin/admin-viewpayments/cancel-invoice"
                        onsubmit="return confirm('Cancel this invoice? This only works when there are no successful payments.');">
                        <input type="hidden" name="invoice_id" value="<?= (int) $row['invoice_id'] ?>">
                        <button class="btn-cancel-invoice" type="submit"><i class="fa-solid fa-ban"></i> Cancel
                          Invoice</button>
                      </form>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    </section>
  </main>
</body>

</html>