<?php
/** @var array $invoice */
$B = rtrim(BASE_URL,'/');
$current = 'invoices';

$invNo   = $invoice['invoice_no'] ?? '—';
$full    = trim(($invoice['first_name'] ?? '') . ' ' . ($invoice['last_name'] ?? ''));
$email   = $invoice['email'] ?? '—';
$vehicle = trim(($invoice['make'] ?? '') . ' ' . ($invoice['model'] ?? ''));
$plate   = $invoice['license_plate'] ?? '—';
$service = $invoice['service_name'] ?? '—';
$total   = (float)($invoice['grand_total'] ?? 0);
$status  = strtolower((string)($invoice['status'] ?? 'pending'));

$statusClass = match ($status) {
  'paid'      => 'status--active',
  'pending'   => 'status--pending',
  'cancelled' => 'status--inactive',
  default     => 'status--pending',
};
$statusLabel = ucfirst($status);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>Invoice <?= htmlspecialchars((string)$invNo) ?></title>

  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="<?= $B ?>/public/assets/css/admin/invoices/show.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

<main class="main-content invoice-show-main">

  <header class="page-header">
    <div class="page-breadcrumb">
      <a href="<?= $B ?>/admin/admin-viewinvoices">Invoices</a>
      <span>›</span>
      <span>Invoice <?= htmlspecialchars((string)$invNo) ?></span>
    </div>

    <div class="page-header-main">
      <div class="page-title-wrap">
        <div class="page-icon"><i class="fa-solid fa-file-invoice"></i></div>
        <div>
          <h2>Invoice <?= htmlspecialchars((string)$invNo) ?></h2>
          <p class="invoice-subtitle">Summary of billing and job details.</p>
        </div>
      </div>

      <span class="status-pill <?= $statusClass ?>">
        <span class="dot"></span><?= htmlspecialchars($statusLabel) ?>
      </span>
    </div>
  </header>

  <section class="invoice-card">
    <div class="invoice-topbar"></div>

    <div class="invoice-inner">
      <div class="invoice-grid">
        <div class="info-block">
          <div class="info-title">Customer</div>
          <div class="info-value"><?= htmlspecialchars($full ?: '—') ?></div>
          <div class="info-sub">
            <i class="fa-regular fa-envelope"></i>
            <?= htmlspecialchars($email) ?>
          </div>
        </div>

        <div class="info-block">
          <div class="info-title">Vehicle</div>
          <div class="info-value"><?= htmlspecialchars($vehicle ?: '—') ?></div>
          <div class="info-sub">
            <i class="fa-solid fa-id-card"></i>
            <?= htmlspecialchars($plate) ?>
          </div>
        </div>

        <div class="info-block">
          <div class="info-title">Service</div>
          <div class="info-value"><?= htmlspecialchars($service) ?></div>
          <div class="info-sub">
            <i class="fa-regular fa-circle-check"></i>
            Work order linked
          </div>
        </div>

        <div class="info-block total-block">
          <div class="info-title">Grand Total</div>
          <div class="total-amount">Rs.<?= number_format($total, 2) ?></div>
          <div class="info-sub">Includes discounts if applied</div>
        </div>
      </div>
    </div>

    <div class="invoice-actions">
      <a class="btn-primary"
         href="<?= $B ?>/admin/admin-viewinvoices/download?id=<?= (int)$invoice['invoice_id'] ?>">
        <i class="fa-solid fa-download"></i>
        <span>Download PDF</span>
      </a>

      <a class="btn-secondary" href="<?= $B ?>/admin/admin-viewinvoices">
        Back to Invoices
      </a>
    </div>
  </section>

</main>
</body>
</html>
