<?php /* Admin view: renders admin-viewmechanics/show page. */ ?>
<?php
$current = $current ?? 'mechanics';
$B = rtrim(BASE_URL, '/');
$m = $mechanic ?? [];

function e($value): string
{
  return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$mechanicCode = (string) ($m['mechanic_code'] ?? '-');
$firstName = (string) ($m['first_name'] ?? '-');
$lastName = (string) ($m['last_name'] ?? '-');
$fullName = trim($firstName . ' ' . $lastName);
$email = trim((string) ($m['email'] ?? '')) ?: '-';
$phone = trim((string) ($m['phone'] ?? '')) ?: '-';
$mechStatus = strtolower((string) ($m['mech_status'] ?? 'active'));
$mechStatusClass = $mechStatus === 'inactive' ? 'inactive' : 'active';
$mechStatusLabel = ucfirst($mechStatus);
$branchCode = trim((string) ($m['branch_code'] ?? ''));
$branchName = trim((string) ($m['branch_name'] ?? ''));
$branchText = ($branchCode !== '' || $branchName !== '')
  ? trim($branchCode . ' ' . ($branchName !== '' ? '(' . $branchName . ')' : ''))
  : 'Not assigned';
$specialization = trim((string) ($m['specialization'] ?? '')) ?: '-';
$experience = (int) ($m['experience_years'] ?? 0);
$createdAt = trim((string) ($m['created_at'] ?? '')) ?: '-';

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
  <title>Mechanic <?= e($mechanicCode) ?> Details</title>

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
          <h1>Mechanic Details</h1>
          <p><?= e($mechanicCode) ?> - <?= e($fullName) ?></p>
        </div>

        <div class="form-actions">
          <a href="<?= e($B . '/admin/mechanics') ?>" class="btn-secondary">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Back to Mechanics</span>
          </a>

          <a href="<?= e($B . '/admin/mechanics/' . urlencode((string) ($m['mechanic_id'] ?? ''))) ?>/edit"
            class="btn-primary">
            <i class="fa-solid fa-pen-to-square"></i>
            <span>Edit Mechanic</span>
          </a>
        </div>
      </header>

      <div class="grid-three">
        <div class="kpi-card">
          <div class="kpi-icon"><i class="fa-solid fa-circle-check"></i></div>
          <div class="kpi-label">Status</div>
          <div class="status-wrap"><span
              class="status-badge <?= e($mechStatusClass) ?>"><?= e($mechStatusLabel) ?></span></div>
        </div>

        <div class="kpi-card">
          <div class="kpi-icon"><i class="fa-solid fa-tools"></i></div>
          <div class="kpi-label">Specialization</div>
          <div class="kpi-value"><?= e($specialization !== '-' ? $specialization : 'Not set') ?></div>
        </div>

        <div class="kpi-card">
          <div class="kpi-icon"><i class="fa-solid fa-id-badge"></i></div>
          <div class="kpi-label">Mechanic Code</div>
          <div class="kpi-value"><?= e($mechanicCode) ?></div>
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
            <i class="fa-solid fa-briefcase"></i>
            <h3>Professional & Branch Info</h3>
          </div>
          <div class="card-body">
            <?= fieldRow('fa-building', 'Assigned Branch', e($branchText)) ?>
            <?= fieldRow('fa-tools', 'Specialization', e($specialization)) ?>
            <?= fieldRow('fa-graduation-cap', 'Experience', e($experience . ' years')) ?>
            <?= fieldRow('fa-calendar-days', 'Created At', e($createdAt)) ?>
          </div>
        </div>
      </div>
    </div>
  </main>
</body>

</html>