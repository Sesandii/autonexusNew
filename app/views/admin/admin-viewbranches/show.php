<?php
/** @var array $row */
/** @var string $base */
$base = rtrim($base ?? BASE_URL, '/');
$current = 'branches';

function e($value): string
{
  return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$st = $row['status'] ?? 'active';
$statusClass = $st === 'inactive' ? 'status-pill inactive' : 'status-pill active';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Branch <?= e($row['branch_code'] ?? '') ?> • Details</title>

  <link rel="stylesheet" href="<?= $base ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="<?= $base ?>/app/views/admin/admin-viewbranches/branches.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body>
  <?php include(__DIR__ . '/../../layouts/admin-sidebar/sidebar.php'); ?>

  <main class="main-content">
    <header class="page-header">
      <h1>Branch Details</h1>
      <p>View complete information for branch <?= e($row['branch_code'] ?? '') ?></p>
    </header>

    <section class="details-card">
      <div class="details-card-header">
        <h2><?= e($row['name'] ?? '') ?></h2>
        <span class="<?= e($statusClass) ?>"><?= e(ucfirst((string) $st)) ?></span>
      </div>

      <div class="details-body">
        <div class="details-grid">
          <div class="detail-item">
            <div class="detail-label">Branch Code</div>
            <div class="detail-value"><?= e($row['branch_code'] ?? '') ?></div>
          </div>

          <div class="detail-item">
            <div class="detail-label">City</div>
            <div class="detail-value"><?= e($row['city'] ?? '') ?></div>
          </div>

          <div class="detail-item">
            <div class="detail-label">Manager ID</div>
            <div class="detail-value"><?= e($row['manager_id'] ?? '') ?: 'Not assigned' ?></div>
          </div>

          <div class="detail-item">
            <div class="detail-label">Phone</div>
            <div class="detail-value"><?= e($row['phone'] ?? '') ?: '—' ?></div>
          </div>

          <div class="detail-item">
            <div class="detail-label">Email</div>
            <div class="detail-value"><?= e($row['email'] ?? '') ?: '—' ?></div>
          </div>

          <div class="detail-item">
            <div class="detail-label">Created At</div>
            <div class="detail-value"><?= e($row['created_at'] ?? '') ?: '—' ?></div>
          </div>

          <div class="detail-item">
            <div class="detail-label">Capacity</div>
            <div class="detail-value"><?= e($row['capacity'] ?? '') ?: '0' ?></div>
          </div>

          <div class="detail-item">
            <div class="detail-label">Staff Count</div>
            <div class="detail-value"><?= e($row['staff_count'] ?? '') ?: '0' ?></div>
          </div>

          <div class="detail-item full">
            <div class="detail-label">Address / Working Hours</div>
            <div class="detail-value"><?= e($row['address_line'] ?? '') ?: '—' ?></div>
          </div>

          <div class="detail-item full">
            <div class="detail-label">Notes</div>
            <div class="detail-value"><?= nl2br(e($row['notes'] ?? '')) ?: '—' ?></div>
          </div>
        </div>

        <div class="actions">
          <a class="btn-secondary" href="<?= e($base . '/admin/branches') ?>">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Back to list</span>
          </a>

          <a class="btn-primary"
            href="<?= e($base . '/admin/branches/' . rawurlencode((string) $row['branch_code']) . '/edit') ?>">
            <i class="fa-solid fa-pen"></i>
            <span>Edit Branch</span>
          </a>
        </div>
      </div>
    </section>
  </main>
</body>

</html>