<?php /* Admin view: renders admin-viewbranches/show page. */ ?>
<?php
/** @var array $row */
/** @var string $base */
$base = rtrim($base ?? BASE_URL, '/');
$current = 'branches';

function e($value): string
{
  return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$status = strtolower((string) ($row['status'] ?? 'active'));
$statusClass = $status === 'inactive' ? 'inactive' : 'active';
$statusLabel = ucfirst($status);
$branchCode = (string) ($row['branch_code'] ?? '—');
$branchName = (string) ($row['name'] ?? '—');
$city = (string) ($row['city'] ?? '—');
$phone = trim((string) ($row['phone'] ?? '')) ?: '—';
$email = trim((string) ($row['email'] ?? '')) ?: '—';
$createdAt = trim((string) ($row['created_at'] ?? '')) ?: '—';
$capacity = trim((string) ($row['capacity'] ?? '')) ?: '0';
$staffCount = trim((string) ($row['staff_count'] ?? '')) ?: '0';
$managerId = trim((string) ($row['manager_id'] ?? '')) ?: 'Not assigned';
$address = trim((string) ($row['address_line'] ?? '')) ?: '—';
$notes = trim((string) ($row['notes'] ?? ''));

function fieldRow(string $icon, string $label, string $value): string
{
  return "<div class=\"field-row\">
    <div class=\"field-icon\"><i class=\"fa-solid {$icon}\"></i></div>
    <div class=\"field-content\">
      <div class=\"field-label\">{$label}</div>
      <div class=\"field-value\">{$value}</div>
    </div>
  </div>";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Branch <?= e($row['branch_code'] ?? '') ?> • Details</title>

  <link rel="stylesheet" href="<?= $base ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="<?= $base ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/admin/branches/show.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body>
  <?php include(__DIR__ . '/../../layouts/admin-sidebar/sidebar.php'); ?>

  <main class="main-content branch-show-main">
    <header class="topbar">
      <div>
        <h1 class="page-title">Branch Details</h1>
        <p class="subtitle">#<?= e($branchCode) ?> • <?= e($branchName) ?></p>
      </div>

      <div class="page-actions">
        <a href="<?= e($base . '/admin/branches') ?>" class="back-btn">
          <i class="fa-solid fa-arrow-left"></i> Back to list
        </a>

        <a href="<?= e($base . '/admin/branches/' . rawurlencode($branchCode) . '/edit') ?>" class="action-btn">
          <i class="fa-solid fa-pen-to-square"></i> Edit Branch
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
        <div class="kpi-icon"><i class="fa-solid fa-users"></i></div>
        <div class="kpi-label">Staff Count</div>
        <div class="kpi-value"><?= e($staffCount) ?></div>
      </div>

      <div class="kpi-card">
        <div class="kpi-icon"><i class="fa-solid fa-gauge"></i></div>
        <div class="kpi-label">Capacity</div>
        <div class="kpi-value"><?= e($capacity) ?></div>
      </div>
    </div>

    <div class="grid-two">
      <div class="detail-card">
        <div class="card-header">
          <i class="fa-solid fa-building"></i>
          <h3>Branch Information</h3>
        </div>
        <div class="card-body">
          <?= fieldRow('fa-barcode', 'Branch Code', e($branchCode)) ?>
          <?= fieldRow('fa-building', 'Branch Name', e($branchName)) ?>
          <?= fieldRow('fa-location-dot', 'City', e($city)) ?>
          <?= fieldRow('fa-phone', 'Phone', e($phone)) ?>
          <?= fieldRow('fa-envelope', 'Email', e($email)) ?>
          <?= fieldRow('fa-map', 'Address', e($address)) ?>
        </div>
      </div>

      <div class="detail-card">
        <div class="card-header">
          <i class="fa-solid fa-gear"></i>
          <h3>Operations & Assignment</h3>
        </div>
        <div class="card-body">
          <?= fieldRow('fa-user-tie', 'Manager ID', e($managerId)) ?>
          <?= fieldRow('fa-users', 'Staff Count', e($staffCount)) ?>
          <?= fieldRow('fa-gauge', 'Capacity', e($capacity)) ?>
          <?= fieldRow('fa-calendar-days', 'Created At', e($createdAt)) ?>
        </div>
      </div>
    </div>

    <div class="detail-card">
      <div class="card-header">
        <i class="fa-solid fa-note-sticky"></i>
        <h3>Notes</h3>
      </div>
      <div class="card-body">
        <div class="summary-box"><?= $notes !== '' ? nl2br(e($notes)) : 'No notes provided.' ?></div>
      </div>
    </div>
  </main>
</body>

</html>