<?php /* Admin view: renders admin-viewinvoices/show page. */ ?>
<?php
/** @var array $invoice */
$B = rtrim(BASE_URL, '/');
$current = 'invoices';

function e($value): string
{
  return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$invoiceNo = (string) ($invoice['invoice_no'] ?? '—');
$invoiceId = (int) ($invoice['invoice_id'] ?? 0);
$customerName = trim((string) ($invoice['first_name'] ?? '') . ' ' . (string) ($invoice['last_name'] ?? ''));
$email = trim((string) ($invoice['email'] ?? '')) ?: '—';
$vehicle = trim((string) ($invoice['make'] ?? '') . ' ' . (string) ($invoice['model'] ?? '')) ?: '—';
$plate = trim((string) ($invoice['license_plate'] ?? '')) ?: '—';
$service = trim((string) ($invoice['service_name'] ?? '')) ?: '—';
$status = strtolower((string) ($invoice['status'] ?? 'pending'));
$discount = (float) ($invoice['discount'] ?? 0);
$total = (float) ($invoice['grand_total'] ?? 0);
$createdAt = trim((string) ($invoice['created_at'] ?? $invoice['invoice_date'] ?? '')) ?: '—';

$statusClass = match ($status) {
  'paid' => 'paid',
  'cancelled' => 'cancelled',
  default => 'pending',
};
$statusLabel = ucfirst($status);

function fieldRow(string $icon, string $label, string $value): string
{
  return "<div class=\"field-row\">\n"
    . "  <div class=\"field-icon\"><i class=\"fa-solid {$icon}\"></i></div>\n"
    . "  <div class=\"field-content\">\n"
    . "    <div class=\"field-label\">{$label}</div>\n"
    . "    <div class=\"field-value\">{$value}</div>\n"
    . "  </div>\n"
    . "</div>";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>Invoice <?= e($invoiceNo) ?> Details</title>

  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="<?= $B ?>/public/assets/css/admin/branches/create.css">
  <link rel="stylesheet" href="<?= $B ?>/public/assets/css/admin/branches/show.css">
  <link rel="stylesheet" href="<?= $B ?>/public/assets/css/admin/invoices/show.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

  <?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

  <main class="main-content invoice-show-main invoice-show-page">
    <div class="invoice-show-shell">
      <header class="create-header">
        <div class="create-title">
          <h1>Invoice Details</h1>
          <p><?= e($invoiceNo) ?> - <?= e($customerName !== '' ? $customerName : '-') ?></p>
        </div>

        <div class="form-actions">
          <a href="<?= e($B . '/admin/admin-viewinvoices') ?>" class="btn-secondary">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Back to Invoices</span>
          </a>

          <a href="<?= e($B . '/admin/admin-viewinvoices/download?id=' . $invoiceId) ?>" class="btn-primary">
            <i class="fa-solid fa-download"></i>
            <span>Download PDF</span>
          </a>
        </div>
      </header>

      <div class="grid-three">
        <div class="kpi-card">
          <div class="kpi-icon"><i class="fa-solid fa-circle-check"></i></div>
          <div class="kpi-label">Status</div>
          <div class="status-wrap"><span class="status-badge <?= e($statusClass) ?>"><?= e($statusLabel) ?></span></div>
        </div>

        <div class="kpi-card">
          <div class="kpi-icon"><i class="fa-solid fa-receipt"></i></div>
          <div class="kpi-label">Invoice No</div>
          <div class="kpi-value"><?= e($invoiceNo) ?></div>
        </div>

        <div class="kpi-card">
          <div class="kpi-icon"><i class="fa-solid fa-coins"></i></div>
          <div class="kpi-label">Grand Total</div>
          <div class="kpi-value">Rs.<?= number_format($total, 2) ?></div>
        </div>
      </div>

      <div class="grid-two">
        <div class="detail-card">
          <div class="card-header">
            <i class="fa-solid fa-user-tag"></i>
            <h3>Customer Information</h3>
          </div>
          <div class="card-body">
            <?= fieldRow('fa-user', 'Customer Name', e($customerName !== '' ? $customerName : '-')) ?>
            <?= fieldRow('fa-envelope', 'Email', e($email)) ?>
            <?= fieldRow('fa-car', 'Vehicle', e($vehicle)) ?>
            <?= fieldRow('fa-id-card', 'License Plate', e($plate)) ?>
          </div>
        </div>

        <div class="detail-card">
          <div class="card-header">
            <i class="fa-solid fa-file-invoice-dollar"></i>
            <h3>Billing Information</h3>
          </div>
          <div class="card-body">
            <?= fieldRow('fa-wrench', 'Service', e($service)) ?>
            <?= fieldRow('fa-percent', 'Discount', 'Rs.' . number_format($discount, 2)) ?>
            <?= fieldRow('fa-calendar-days', 'Created At', e($createdAt)) ?>
            <?= fieldRow('fa-money-bill-wave', 'Grand Total', 'Rs.' . number_format($total, 2)) ?>
          </div>
        </div>
      </div>

      <div class="detail-card">
        <div class="card-header">
          <i class="fa-solid fa-circle-info"></i>
          <h3>Invoice Summary</h3>
        </div>
        <div class="card-body">
          <div class="summary-box">
            Invoice <?= e($invoiceNo) ?> is currently marked as <?= e($statusLabel) ?>.
            <?= $discount > 0 ? 'A discount of Rs.' . number_format($discount, 2) . ' has been applied.' : 'No discount has been applied.' ?>
          </div>
        </div>
      </div>
    </div>

  </main>
</body>

</html>