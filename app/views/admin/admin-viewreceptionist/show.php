<?php /* Admin view: renders admin-viewreceptionist/show page. */ ?>
<?php
/** @var array $rec */
$current = 'receptionists';
$B = rtrim(BASE_URL, '/');

function e($value): string
{
  return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$code = (string) ($rec['receptionist_code'] ?? ('R' . ($rec['receptionist_id'] ?? '')));
$firstName = (string) ($rec['first_name'] ?? '-');
$lastName = (string) ($rec['last_name'] ?? '-');
$name = trim($firstName . ' ' . $lastName);
$status = strtolower((string) ($rec['status'] ?? 'active'));
$userStatus = strtolower((string) ($rec['user_status'] ?? 'active'));
$email = trim((string) ($rec['email'] ?? '')) ?: '-';
$phone = trim((string) ($rec['phone'] ?? '')) ?: '-';
$altPhone = trim((string) ($rec['alt_phone'] ?? '')) ?: '-';
$createdAt = trim((string) ($rec['created_at'] ?? '')) ?: '-';

$branchLabel = 'Not assigned';
if (!empty($rec['branch_name'])) {
  $branchLabel = (string) $rec['branch_name'];
  if (!empty($rec['branch_code'])) {
    $branchLabel .= ' (' . $rec['branch_code'] . ')';
  }
}

$pillClass = $status === 'inactive' ? 'inactive' : 'active';

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
  <title>Receptionist <?= e($code) ?> Details</title>

  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="<?= $B ?>/public/assets/css/admin/branches/create.css">
  <link rel="stylesheet" href="<?= $B ?>/public/assets/css/admin/branches/show.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body>
  <?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

  <main class="main-content branch-show-main branch-create-page">
    <div class="branch-create-shell">
      <header class="create-header">
        <div class="create-title">
          <h1>Receptionist Details</h1>
          <p><?= e($code) ?> - <?= e($name !== '' ? $name : '-') ?></p>
        </div>

        <div class="form-actions">
          <a href="<?= e($B . '/admin/viewreceptionist') ?>" class="btn-secondary">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Back to Receptionists</span>
          </a>

          <a href="<?= e($B . '/admin/receptionists/edit?id=' . urlencode((string) ($rec['receptionist_id'] ?? ''))) ?>"
            class="btn-primary">
            <i class="fa-solid fa-pen-to-square"></i>
            <span>Edit Receptionist</span>
          </a>
        </div>
      </header>

      <div class="grid-three">
        <div class="kpi-card">
          <div class="kpi-icon"><i class="fa-solid fa-circle-check"></i></div>
          <div class="kpi-label">Status</div>
          <div class="status-wrap"><span class="status-badge <?= e($pillClass) ?>"><?= e(ucfirst($status)) ?></span>
          </div>
        </div>

        <div class="kpi-card">
          <div class="kpi-icon"><i class="fa-solid fa-building"></i></div>
          <div class="kpi-label">Assigned Branch</div>
          <div class="kpi-value"><?= e($branchLabel) ?></div>
        </div>

        <div class="kpi-card">
          <div class="kpi-icon"><i class="fa-solid fa-id-badge"></i></div>
          <div class="kpi-label">Receptionist Code</div>
          <div class="kpi-value"><?= e($code) ?></div>
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
            <h3>Account & Branch Info</h3>
          </div>
          <div class="card-body">
            <?= fieldRow('fa-building', 'Assigned Branch', e($branchLabel)) ?>
            <?= fieldRow('fa-circle-check', 'User Status', e(ucfirst($userStatus))) ?>
            <?= fieldRow('fa-calendar-days', 'Created At', e($createdAt)) ?>
          </div>
        </div>
      </div>
    </div>
  </main>
</body>

</html>