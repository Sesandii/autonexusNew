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
  <title>Edit Manager #<?= htmlspecialchars($row['manager_id']) ?></title>
  <link rel="stylesheet" href="../../app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="../../app/views/layouts/admin-sidebar/styles.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    .sidebar { position:fixed; top:0; left:0; width:260px; height:100vh; overflow-y:auto; }
    .main-content { margin-left:260px; padding:30px; background:#fff; min-height:100vh; }
    .form { max-width:720px; }
    .row { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:12px; }
    .label { color:#666; font-size:12px; margin-bottom:2px; }
    .input, select { width:100%; padding:10px 12px; border:1px solid #e5e7eb; border-radius:6px; }
    .btns { margin-top:16px; display:flex; gap:10px; }
  </style>
</head>
<body>
  <?php include(__DIR__ . '/../../layouts/admin-sidebar/sidebar.php'); ?>

  <main class="main-content">
    <h2>Edit Manager</h2>

    <form class="form" method="post"
          action="<?= htmlspecialchars($base . '/service-managers/' . urlencode((string)$row['manager_id']), ENT_QUOTES, 'UTF-8') ?>">
      <div class="row">
        <div>
          <div class="label">First name</div>
          <input class="input" name="first_name" value="<?= htmlspecialchars($row['first_name'] ?? '') ?>" required>
        </div>
        <div>
          <div class="label">Last name</div>
          <input class="input" name="last_name" value="<?= htmlspecialchars($row['last_name'] ?? '') ?>" required>
        </div>

        <div>
          <div class="label">Username</div>
          <input class="input" name="username" value="<?= htmlspecialchars($row['username'] ?? '') ?>" required>
        </div>
        <div>
          <div class="label">Email</div>
          <input class="input" type="email" name="email" value="<?= htmlspecialchars($row['email'] ?? '') ?>" required>
        </div>

        <div>
          <div class="label">Phone</div>
          <input class="input" name="phone" value="<?= htmlspecialchars($row['phone'] ?? '') ?>">
        </div>
        <div>
          <div class="label">Manager Code</div>
          <input class="input" name="manager_code" value="<?= htmlspecialchars($row['manager_code'] ?? '') ?>">
        </div>

        <div>
          <div class="label">Status</div>
          <select name="status" class="input">
            <?php
              $st = $row['status'] ?? 'active';
              $options = ['active'=>'Active','inactive'=>'Inactive'];
              foreach ($options as $val=>$label):
            ?>
              <option value="<?= $val ?>" <?= $st===$val?'selected':'' ?>><?= $label ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <div class="label">Password (leave blank to keep)</div>
          <input class="input" type="password" name="password" placeholder="••••••">
        </div>
      </div>

      <div class="btns">
        <button type="submit" class="btn-primary">Update</button>
        <a href="<?= htmlspecialchars($base . '/service-managers', ENT_QUOTES, 'UTF-8') ?>" class="btn-secondary">Cancel</a>
      </div>
    </form>
  </main>
</body>
</html>
