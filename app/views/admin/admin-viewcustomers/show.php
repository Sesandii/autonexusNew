<?php /* Admin view: renders admin-viewcustomers/show page. */ ?>
<?php
$current = 'customers';
$B = rtrim(BASE_URL, '/');

function e($value): string
{
  return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$firstName = (string) ($c['first_name'] ?? '-');
$lastName = (string) ($c['last_name'] ?? '-');
$fullName = trim($firstName . ' ' . $lastName);
$customerCode = trim((string) ($c['customer_code'] ?? '')) ?: '-';
$email = trim((string) ($c['email'] ?? '')) ?: '-';
$phone = trim((string) ($c['phone'] ?? '')) ?: '-';
$createdAt = trim((string) ($c['created_at'] ?? '')) ?: '-';
$status = strtolower((string) ($c['status'] ?? 'active'));
$statusClass = in_array($status, ['active', 'inactive', 'pending'], true) ? $status : 'active';

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
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer <?= e($customerCode) ?> Details</title>

  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="<?= $B ?>/public/assets/css/admin/branches/create.css">
  <link rel="stylesheet" href="<?= $B ?>/public/assets/css/admin/branches/show.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body>
  <?php include(__DIR__ . '/../../layouts/admin-sidebar/sidebar.php'); ?>

  <main class="main-content branch-show-main branch-create-page">
    <div class="branch-create-shell">
      <header class="create-header">
        <div class="create-title">
          <h1>Customer Details</h1>
          <p><?= e($customerCode) ?> - <?= e($fullName !== '' ? $fullName : '-') ?></p>
        </div>

        <div class="form-actions">
          <a href="<?= e($B . '/admin/customers') ?>" class="btn-secondary">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Back to Customers</span>
          </a>

          <a href="<?= e($B . '/admin/customers/' . (int) ($c['customer_id'] ?? 0) . '/edit') ?>" class="btn-primary">
            <i class="fa-solid fa-pen-to-square"></i>
            <span>Edit Customer</span>
          </a>
        </div>
      </header>

      <div class="grid-three">
        <div class="kpi-card">
          <div class="kpi-icon"><i class="fa-solid fa-circle-check"></i></div>
          <div class="kpi-label">Status</div>
          <div class="status-wrap"><span class="status-badge <?= e($statusClass) ?>"><?= e(ucfirst($status)) ?></span>
          </div>
        </div>

        <div class="kpi-card">
          <div class="kpi-icon"><i class="fa-solid fa-id-badge"></i></div>
          <div class="kpi-label">Customer Code</div>
          <div class="kpi-value"><?= e($customerCode) ?></div>
        </div>

        <div class="kpi-card">
          <div class="kpi-icon"><i class="fa-solid fa-calendar-days"></i></div>
          <div class="kpi-label">Created At</div>
          <div class="kpi-value"><?= e(substr($createdAt, 0, 10)) ?></div>
        </div>
      </div>

      <div class="grid-two">
        <div class="detail-card">
          <div class="card-header">
            <i class="fa-solid fa-user"></i>
            <h3>Personal Information</h3>
          </div>
          <div class="card-body">
            <?= fieldRow('fa-user', 'First Name', e($firstName)) ?>
            <?= fieldRow('fa-user', 'Last Name', e($lastName)) ?>
            <?= fieldRow('fa-envelope', 'Email', e($email)) ?>
            <?= fieldRow('fa-phone', 'Phone', e($phone)) ?>
          </div>
        </div>

        <div class="detail-card">
          <div class="card-header">
            <i class="fa-solid fa-circle-info"></i>
            <h3>Account Information</h3>
          </div>
          <div class="card-body">
            <?= fieldRow('fa-id-badge', 'Customer Code', e($customerCode)) ?>
            <?= fieldRow('fa-circle-check', 'Status', e(ucfirst($status))) ?>
            <?= fieldRow('fa-calendar-days', 'Created At', e($createdAt)) ?>
          </div>
        </div>
      </div>

    </div>
  </main>
</body>

</html>