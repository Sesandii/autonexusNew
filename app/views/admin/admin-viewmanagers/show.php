<?php
/** @var array $row */
/** @var string $base */
$base = rtrim($base ?? BASE_URL, '/');
$current = 'service-managers';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manager #<?= htmlspecialchars($row['manager_id']) ?> • Details</title>
  <link rel="stylesheet" href="../app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="../app/views/layouts/admin-sidebar/styles.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    .sidebar { position:fixed; top:0; left:0; width:260px; height:100vh; overflow-y:auto; }
    .main-content { margin-left:260px; padding:30px; background:#fff; min-height:100vh; }
    .card { border:1px solid #eee; border-radius:10px; padding:20px; max-width:820px; background:#fff; }
    .grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:16px; }
    .label { font-size:12px; color:#666; }
    .value { font-weight:600; }
    .actions { margin-top:16px; display:flex; gap:10px; }
  </style>
</head>
<body>
  <?php include(__DIR__ . '/../../layouts/admin-sidebar/sidebar.php'); ?>

  <main class="main-content">
    <h2>Manager Details</h2>

    <div class="card">
      <div class="grid">
        <div>
          <div class="label">Manager ID</div>
          <div class="value"><?= htmlspecialchars($row['manager_id']) ?></div>
        </div>
        <div>
          <div class="label">Manager Code</div>
          <div class="value"><?= htmlspecialchars($row['manager_code'] ?? '') ?></div>
        </div>

        <div>
          <div class="label">First Name</div>
          <div class="value"><?= htmlspecialchars($row['first_name'] ?? '') ?></div>
        </div>
        <div>
          <div class="label">Last Name</div>
          <div class="value"><?= htmlspecialchars($row['last_name'] ?? '') ?></div>
        </div>

        <div>
          <div class="label">Username</div>
          <div class="value"><?= htmlspecialchars($row['username'] ?? '') ?></div>
        </div>
        <div>
          <div class="label">Email</div>
          <div class="value"><?= htmlspecialchars($row['email'] ?? '') ?></div>
        </div>

        <div>
          <div class="label">Phone</div>
          <div class="value"><?= htmlspecialchars($row['phone'] ?? '') ?></div>
        </div>
        <div>
          <div class="label">Status</div>
          <div class="value"><?= htmlspecialchars($row['status'] ?? '') ?></div>
        </div>

        <div>
          <div class="label">User Created</div>
          <div class="value"><?= htmlspecialchars($row['user_created_at'] ?? '') ?></div>
        </div>
        <div>
          <div class="label">Manager Created</div>
          <div class="value"><?= htmlspecialchars($row['manager_created_at'] ?? '') ?></div>
        </div>
      </div>

      <div class="actions">
        <a class="btn-secondary" href="<?= htmlspecialchars($base . '/service-managers', ENT_QUOTES, 'UTF-8') ?>">
          ← Back to list
        </a>
        <a class="btn"
   href="<?= htmlspecialchars($base . '/service-managers/' . urlencode((string)$row['manager_id']) . '/edit', ENT_QUOTES, 'UTF-8') ?>">
  Edit
</a>

      </div>
    </div>
  </main>
</body>
</html>
