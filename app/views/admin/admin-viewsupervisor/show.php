<?php /* Admin view: renders admin-viewsupervisor/show page. */ ?>
<?php
$current = 'supervisors';
$base = rtrim($base ?? BASE_URL, '/');

function e($value): string
{
  return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$supervisorCode = (string) ($s['supervisor_code'] ?? '-');
$firstName = (string) ($s['first_name'] ?? '-');
$lastName = (string) ($s['last_name'] ?? '-');
$fullName = trim($firstName . ' ' . $lastName);
$email = trim((string) ($s['email'] ?? '')) ?: '-';
$phone = trim((string) ($s['phone'] ?? '')) ?: '-';
$status = strtolower((string) ($s['status'] ?? 'active'));
$statusClass = $status === 'inactive' ? 'inactive' : 'active';
$statusLabel = ucfirst($status);
$branchCode = trim((string) ($s['branch_code'] ?? ''));
$branchName = trim((string) ($s['branch_name'] ?? ''));
$branchText = ($branchCode !== '' || $branchName !== '')
  ? trim($branchCode . ' ' . ($branchName !== '' ? '(' . $branchName . ')' : ''))
  : 'Not assigned';
$managerName = trim(($s['manager_first_name'] ?? '') . ' ' . ($s['manager_last_name'] ?? ''));
$managerCode = (string) ($s['manager_code'] ?? '-');
$managerLabel = $managerCode !== '-' ? "$managerCode — $managerName" : ($managerName !== '' ? $managerName : 'Not assigned');
$createdAt = trim((string) ($s['created_at'] ?? '')) ?: '-';

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
  <title>Supervisor <?= e($supervisorCode) ?> Details</title>

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
          <h1>Supervisor Details</h1>
          <p><?= e($supervisorCode) ?> - <?= e($fullName) ?></p>
        </div>

        <div class="form-actions">
          <a href="<?= e($base . '/admin/supervisors') ?>" class="btn-secondary">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Back to Supervisors</span>
          </a>

          <a href="<?= e($base . '/admin/supervisors/' . urlencode($supervisorCode) . '/edit') ?>" class="btn-primary">
            <i class="fa-solid fa-pen-to-square"></i>
            <span>Edit Supervisor</span>
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
          <div class="kpi-label">Supervisor Code</div>
          <div class="kpi-value"><?= e($supervisorCode) ?></div>
        </div>
      </div>

      <div class="grid-two">
        <div class="detail-card">
          <div class="card-header">
            <i class="fa-solid fa-user-check"></i>
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
            <i class="fa-solid fa-building"></i>
            <h3>Assignment & Status</h3>
          </div>
          <div class="card-body">
            <?= fieldRow('fa-building', 'Assigned Branch', e($branchText)) ?>
            <?= fieldRow('fa-user-tie', 'Branch Manager', e($managerLabel)) ?>
            <?= fieldRow('fa-circle-check', 'Account Status', e($statusLabel)) ?>
            <?= fieldRow('fa-calendar-days', 'Created At', e($createdAt)) ?>
          </div>
        </div>
      </div>
    </div>
  </main>
</body>

</html>