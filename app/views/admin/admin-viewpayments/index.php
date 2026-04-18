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

  <style>
    .summary-grid {
      display:grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap:14px;
      margin-bottom:20px;
    }
    .summary-card {
      background:#fff;
      border-radius:12px;
      padding:16px;
      box-shadow:0 2px 8px rgba(0,0,0,.05);
    }
    .summary-card p { color:#6b7280; font-size:13px; margin-bottom:6px; }
    .summary-card h3 { font-size:24px; color:#111827; }
    .panel {
      background:#fff;
      border-radius:12px;
      box-shadow:0 2px 8px rgba(0,0,0,.05);
      padding:18px;
      margin-bottom:20px;
    }
    .panel h3 {
      margin-bottom:12px;
      font-size:18px;
      color:#111827;
    }
    .form-grid {
      display:grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap:12px;
      align-items:end;
    }
    .form-grid label {
      display:flex;
      flex-direction:column;
      gap:6px;
      font-size:13px;
      color:#374151;
      font-weight:600;
    }
    .form-grid input,
    .form-grid select {
      padding:9px 12px;
      border:1px solid #d1d5db;
      border-radius:8px;
      background:#fff;
      font-size:14px;
    }
    .filters-row {
      display:flex;
      gap:10px;
      flex-wrap:wrap;
      margin-bottom:18px;
    }
    .filters-row form {
      display:flex;
      gap:10px;
      flex-wrap:wrap;
      width:100%;
    }
    .filters-row input,
    .filters-row select {
      padding:9px 12px;
      border:1px solid #d1d5db;
      border-radius:8px;
      background:#fff;
      font-size:14px;
      min-width:160px;
    }
    .filters-row .search-input { min-width:260px; }
    .status--success { background:#dcfce7; border-color:#bbf7d0; color:#166534; }
    .status--success .dot { background:#16a34a; }
    .status--failed { background:#fee2e2; border-color:#fecaca; color:#991b1b; }
    .status--failed .dot { background:#dc2626; }
    .status--pending { background:#fef3c7; border-color:#fde68a; color:#92400e; }
    .status--pending .dot { background:#d97706; }
    .invoice-status {
      display:inline-block;
      padding:4px 10px;
      border-radius:999px;
      background:#f3f4f6;
      color:#374151;
      font-size:12px;
      font-weight:600;
    }
    .action-inline {
      display:flex;
      align-items:center;
      gap:8px;
      flex-wrap:wrap;
    }
    .action-inline button {
      border:none;
      border-radius:999px;
      padding:7px 12px;
      font-size:12px;
      font-weight:600;
      cursor:pointer;
    }
    .btn-orange { background:#cf3202; color:#fff; }
    .btn-red { background:#dc2626; color:#fff; }
    .small-note {
      color:#6b7280;
      font-size:13px;
      margin-top:6px;
    }
  </style>
</head>
<body>
<?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

<main class="main-content">
  <section class="management">
    <div class="management-header">
      <div>
        <h2>Payments Management</h2>
        <p class="management-subtitle">Invoice + payment visibility with manual payment marking and reference search.</p>
      </div>
    </div>

    <div class="summary-grid">
      <div class="summary-card">
        <p>Total Collected</p>
        <h3>Rs. <?= number_format((float)$summary['collected_total'], 2) ?></h3>
      </div>
      <div class="summary-card">
        <p>Successful Payments</p>
        <h3><?= (int)$summary['success_count'] ?></h3>
      </div>
      <div class="summary-card">
        <p>Pending Payments</p>
        <h3><?= (int)$summary['pending_count'] ?></h3>
      </div>
      <div class="summary-card">
        <p>Failed Payments</p>
        <h3><?= (int)$summary['failed_count'] ?></h3>
      </div>
      <div class="summary-card">
        <p>Cash / Card / Online</p>
        <h3 style="font-size:16px; line-height:1.6;">
          Rs. <?= number_format((float)$summary['cash_total'], 2) ?><br>
          Rs. <?= number_format((float)$summary['card_total'], 2) ?><br>
          Rs. <?= number_format((float)$summary['online_total'], 2) ?>
        </h3>
      </div>
    </div>

    <div class="panel">
      <h3>Manual Payment Marking</h3>
      <form method="POST" action="<?= $B ?>/admin/admin-viewpayments/store">
        <div class="form-grid">
          <label>
            Invoice
            <select name="invoice_id" required>
              <option value="">Select invoice</option>
              <?php foreach ($invoiceOptions as $invoice): ?>
                <option value="<?= (int)$invoice['invoice_id'] ?>">
                  <?= htmlspecialchars($invoice['invoice_no']) ?> - <?= htmlspecialchars($invoice['customer_name']) ?> - <?= htmlspecialchars($invoice['service_name']) ?>
                  (Paid Rs.<?= number_format((float)$invoice['paid_amount'], 2) ?> / Total Rs.<?= number_format((float)$invoice['grand_total'], 2) ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </label>

          <label>
            Amount
            <input type="number" step="0.01" min="0.01" name="amount" required>
          </label>

          <label>
            Method
            <select name="method" required>
              <option value="cash">Cash</option>
              <option value="card">Card</option>
              <option value="online">Online</option>
            </select>
          </label>

          <label>
            Reference / Receipt No
            <input type="text" name="reference_no" placeholder="REF-12345">
          </label>

          <label>
            Payment Status
            <select name="status" required>
              <option value="success">Successful</option>
              <option value="pending">Pending</option>
              <option value="failed">Failed</option>
            </select>
          </label>

          <div>
            <button class="add-btn" type="submit"><i class="fa-solid fa-plus"></i> Save Payment</button>
          </div>
        </div>
      </form>
      <p class="small-note">This uses the existing `payments` table only. Successful payments auto-update invoice status to paid when the successful total reaches the invoice grand total.</p>
    </div>

    <div class="filters-row">
      <form method="GET" action="<?= $B ?>/admin/admin-viewpayments">
        <input class="search-input" type="text" name="q" value="<?= htmlspecialchars($filters['q'] ?? '') ?>" placeholder="Search invoice no, payment reference, customer, service...">

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
                <strong>#<?= (int)$row['payment_id'] ?></strong><br>
                <small><?= date('M d, Y h:i A', strtotime((string)$row['payment_date'])) ?></small><br>
                <small><?= htmlspecialchars($row['reference_no'] ?: '-') ?></small>
              </td>
              <td>
                <strong><?= htmlspecialchars($row['invoice_no']) ?></strong><br>
                <small>Invoice Total: Rs.<?= number_format((float)$row['grand_total'], 2) ?></small>
              </td>
              <td>
                <strong><?= htmlspecialchars($row['customer_name']) ?></strong><br>
                <small><?= htmlspecialchars($row['service_name']) ?></small><br>
                <small><?= htmlspecialchars($row['branch_name'] ?: '-') ?></small>
              </td>
              <td><?= ucfirst(htmlspecialchars((string)$row['method'])) ?></td>
              <td>Rs.<?= number_format((float)$row['amount'], 2) ?></td>
              <td>
                <span class="status-pill status--<?= htmlspecialchars((string)$row['payment_status']) ?>">
                  <span class="dot"></span>
                  <?= ucfirst(htmlspecialchars((string)$row['payment_status'])) ?>
                </span>
              </td>
              <td>
                <span class="invoice-status"><?= ucfirst(htmlspecialchars((string)$row['invoice_status'])) ?></span>
              </td>
              <td>
                <div class="action-inline">
                  <?php if (($row['invoice_status'] ?? '') !== 'cancelled'): ?>
                    <form method="POST" action="<?= $B ?>/admin/admin-viewpayments/cancel-invoice" onsubmit="return confirm('Cancel this invoice? This only works when there are no successful payments.');">
                      <input type="hidden" name="invoice_id" value="<?= (int)$row['invoice_id'] ?>">
                      <button class="btn-red" type="submit">Cancel Invoice</button>
                    </form>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="panel" style="margin-top:20px;">
      <h3>Refund / Cancel Note</h3>
      <p class="small-note">
        With your current schema, a full refund audit flow is not safe to fake because there is no refund table and no payment reversal fields.
        So this code supports invoice cancel only when there are no successful payments. Proper refunds need a new table such as `payment_refunds`.
      </p>
    </div>
  </section>
</main>
</body>
</html>