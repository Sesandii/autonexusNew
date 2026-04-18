<?php
$current = 'supervisors';
$base = rtrim($base ?? BASE_URL, '/');
$managerName = trim(($s['manager_first_name'] ?? '') . ' ' . ($s['manager_last_name'] ?? ''));
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Supervisor <?= htmlspecialchars($s['supervisor_code']) ?></title>
  <link rel="stylesheet" href="<?= $base ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="<?= $base ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="<?= $base ?>/app/views/admin/admin-viewsupervisor/supervisors.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body>
  <?php include(__DIR__ . '/../../layouts/admin-sidebar/sidebar.php'); ?>
  <main class="main-content">
    <h2>Supervisor Details</h2>
    <div class="card">
      <p><strong>Supervisor ID:</strong> <?= htmlspecialchars((string) ($s['supervisor_id'] ?? '')) ?></p>
      <p><strong>User ID:</strong> <?= htmlspecialchars((string) ($s['user_id'] ?? '')) ?></p>
      <p><strong>Supervisor Code:</strong> <?= htmlspecialchars((string) ($s['supervisor_code'] ?? '')) ?></p>
      <p><strong>Name:</strong> <?= htmlspecialchars(trim(($s['first_name'] ?? '') . ' ' . ($s['last_name'] ?? ''))) ?>
      </p>
      <p><strong>Email:</strong> <?= htmlspecialchars((string) ($s['email'] ?? '')) ?></p>
      <p><strong>Phone:</strong> <?= htmlspecialchars((string) ($s['phone'] ?? '')) ?></p>
      <p><strong>Status:</strong> <?= htmlspecialchars(ucfirst((string) ($s['status'] ?? 'active'))) ?></p>
      <p><strong>Branch Code:</strong> <?= htmlspecialchars((string) ($s['branch_code'] ?? 'Not assigned')) ?></p>
      <p><strong>Branch Name:</strong> <?= htmlspecialchars((string) ($s['branch_name'] ?? 'Not assigned')) ?></p>
      <p><strong>Manager Code:</strong> <?= htmlspecialchars((string) ($s['manager_code'] ?? 'Not assigned')) ?></p>
      <p><strong>Manager Name:</strong> <?= htmlspecialchars($managerName !== '' ? $managerName : 'Not assigned') ?></p>
      <p><strong>Created At:</strong> <?= htmlspecialchars((string) ($s['created_at'] ?? '')) ?></p>

      <p style="margin-top:14px">
        <a class="add-btn" href="<?= $base ?>/admin/supervisors/<?= urlencode((string) $s['supervisor_code']) ?>/edit"><i
            class="fas fa-pen"></i> Edit</a>
        <a class="btn-secondary" href="<?= $base ?>/admin/supervisors" style="margin-left:8px">Back</a>
      </p>
    </div>
  </main>
</body>

</html>