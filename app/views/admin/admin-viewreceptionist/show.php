<?php
/** @var array $rec */
$current = 'receptionists';
$B = rtrim(BASE_URL, '/');

$code       = $rec['receptionist_code'] ?? ('R' . $rec['receptionist_id']);
$name       = trim(($rec['first_name'] ?? '') . ' ' . ($rec['last_name'] ?? ''));
$status     = strtolower($rec['status'] ?? 'active');
$userStatus = strtolower($rec['user_status'] ?? 'active');

$branchLabel = '—';
if (!empty($rec['branch_name'])) {
    $branchLabel = $rec['branch_name'];
    if (!empty($rec['branch_code'])) {
        $branchLabel .= ' (' . $rec['branch_code'] . ')';
    }
}

$pillClass = $status === 'inactive'
  ? 'status--inactive'
  : 'status--active';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Receptionist #<?= htmlspecialchars($code) ?></title>

  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

<main class="main-content">

  <header class="page-header">
    <div class="page-breadcrumb">
      <a href="<?= $B ?>/admin/viewreceptionist">Receptionists</a>
      <span>›</span>
      <span>Receptionist #<?= htmlspecialchars($code) ?></span>
    </div>

    <div class="page-header-main">
      <div class="page-title-wrap">
        <div class="page-icon">
          <i class="fa-solid fa-user-tie"></i>
        </div>
        <div>
          <h2>Receptionist Profile</h2>
          <p>Overview of account details and branch assignment.</p>
        </div>
      </div>

      <div class="page-chip">
        Code: <?= htmlspecialchars($code) ?>
      </div>
    </div>
  </header>

  <section class="detail-card" aria-labelledby="receptionist-heading">
    <div class="detail-card-topbar"></div>

    <div class="detail-card-inner">
      <div class="detail-heading">
        <div>
          <div class="detail-heading-name" id="receptionist-heading">
            <?= htmlspecialchars($name ?: $code) ?>
          </div>
          <div class="detail-item-value" style="margin-top:3px;font-weight:400;color:#6b7280;">
            <?= htmlspecialchars($rec['email'] ?? 'No email on file') ?>
          </div>
        </div>

        <div class="detail-meta-chips">
          <span class="status-pill <?= $pillClass ?>">
            <span class="dot"></span><?= htmlspecialchars(ucfirst($status)) ?>
          </span>
          <span class="meta-chip">
            <i class="fa-regular fa-calendar"></i>
            Created <?= htmlspecialchars(substr($rec['created_at'] ?? '—', 0, 10)) ?>
          </span>
        </div>
      </div>

      <div class="detail-grid">
        <div>
          <p class="detail-item-label">Receptionist Code</p>
          <p class="detail-item-value"><?= htmlspecialchars($code) ?></p>
        </div>
        <div>
          <p class="detail-item-label">Branch</p>
          <p class="detail-item-value"><?= htmlspecialchars($branchLabel) ?></p>
        </div>

        <div>
          <p class="detail-item-label">Email</p>
          <p class="detail-item-value"><?= htmlspecialchars($rec['email'] ?? '—') ?></p>
        </div>
        <div>
          <p class="detail-item-label">Phone</p>
          <p class="detail-item-value"><?= htmlspecialchars($rec['phone'] ?? '—') ?></p>
        </div>

        <div>
          <p class="detail-item-label">Alt Phone</p>
          <p class="detail-item-value"><?= htmlspecialchars($rec['alt_phone'] ?? '—') ?></p>
        </div>
        <div>
          <p class="detail-item-label">User Status</p>
          <p class="detail-item-value"><?= htmlspecialchars(ucfirst($userStatus)) ?></p>
        </div>

        <div>
          <p class="detail-item-label">Created At</p>
          <p class="detail-item-value"><?= htmlspecialchars($rec['created_at'] ?? '—') ?></p>
        </div>
      </div>
    </div>

    <div class="detail-footer">
      <a class="btn-primary"
         href="<?= $B ?>/admin/receptionists/edit?id=<?= (int)$rec['receptionist_id'] ?>">
        <i class="fas fa-pen"></i>&nbsp;Edit Receptionist
      </a>

      <a class="btn-secondary"
         href="<?= $B ?>/admin/viewreceptionist">
        Back to Receptionists
      </a>
    </div>
  </section>
</main>
</body>
</html>
