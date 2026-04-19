<?php /* Admin view: renders admin-viewmanagers/show page. */ ?>
<?php
/** @var array $row */
/** @var string $base */
$base = rtrim($base ?? BASE_URL, '/');
$current = 'service-managers';

function e($value): string
{
  return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$managerCode = (string) ($row['manager_code'] ?? '-');
$firstName = (string) ($row['first_name'] ?? '-');
$lastName = (string) ($row['last_name'] ?? '-');
$fullName = trim($firstName . ' ' . $lastName);
$username = (string) ($row['username'] ?? '-');
$email = trim((string) ($row['email'] ?? '')) ?: '-';
$phone = trim((string) ($row['phone'] ?? '')) ?: '-';
$status = strtolower((string) ($row['status'] ?? 'active'));
$statusClass = $status === 'inactive' ? 'inactive' : 'active';
$statusLabel = ucfirst($status);
$branchCode = trim((string) ($row['branch_code'] ?? ''));
$branchName = trim((string) ($row['branch_name'] ?? ''));
$branchText = ($branchCode !== '' || $branchName !== '')
  ? trim($branchCode . ' ' . ($branchName !== '' ? '(' . $branchName . ')' : ''))
  : 'Not assigned';
$userCreatedAt = trim((string) ($row['user_created_at'] ?? '')) ?: '-';
$managerCreatedAt = trim((string) ($row['manager_created_at'] ?? '')) ?: '-';

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
  <title>Manager <?= e($managerCode) ?> Details</title>

  <link rel="stylesheet" href="<?= $base ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="<?= $base ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/admin/branches/create.css">
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/admin/branches/show.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body>
  <?php include(__DIR__ . '/../../layouts/admin-sidebar/sidebar.php'); ?>

  <main class="main-content branch-show-main branch-create-page">
    <div class="branch-create-shell">
      <header class="create-header">
        <div class="create-title">
          <h1>Manager Details</h1>
          <p><?= e($managerCode) ?> - <?= e($fullName) ?></p>
        </div>

        <div class="form-actions">
          <a href="<?= e($base . '/admin/service-managers') ?>" class="btn-secondary">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Back to Managers</span>
          </a>

          <a href="<?= e($base . '/admin/service-managers/' . urlencode((string) $row['manager_id']) . '/edit') ?>"
            class="btn-primary">
            <i class="fa-solid fa-pen-to-square"></i>
            <span>Edit Manager</span>
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
          <div class="kpi-icon"><i class="fa-solid fa-building"></i></div>
          <div class="kpi-label">Assigned Branch</div>
          <div class="kpi-value"><?= e($branchCode !== '' ? $branchCode : 'N/A') ?></div>
        </div>

        <div class="kpi-card">
          <div class="kpi-icon"><i class="fa-solid fa-id-badge"></i></div>
          <div class="kpi-label">Manager Code</div>
          <div class="kpi-value"><?= e($managerCode) ?></div>
        </div>
      </div>

      <div class="grid-two">
        <div class="detail-card">
          <div class="card-header">
            <i class="fa-solid fa-user-tie"></i>
            <h3>Personal Information</h3>
          </div>
          <div class="card-body">
            <?= fieldRow('fa-user', 'First Name', e($firstName)) ?>
            <?= fieldRow('fa-user', 'Last Name', e($lastName)) ?>
            <?= fieldRow('fa-at', 'Username', e($username)) ?>
            <?= fieldRow('fa-envelope', 'Email', e($email)) ?>
            <?= fieldRow('fa-phone', 'Phone', e($phone)) ?>
          </div>
        </div>

        <div class="detail-card">
          <div class="card-header">
            <i class="fa-solid fa-building"></i>
            <h3>Assignment & Status</h3>
          </div>
          <div class="card-body">
            <?= fieldRow('fa-building', 'Assigned Branch', e($branchText)) ?>
            <?= fieldRow('fa-circle-check', 'Account Status', e($statusLabel)) ?>
            <?= fieldRow('fa-calendar-days', 'User Created At', e($userCreatedAt)) ?>
            <?= fieldRow('fa-calendar-days', 'Manager Created At', e($managerCreatedAt)) ?>
          </div>
        </div>
      </div>
    </div>
  </main>
</body>

</html>