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
  <link rel="stylesheet" href="../../../app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="../../../app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="../../../app/views/admin/admin-viewmanagers/service-managers.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body>
  <?php include(__DIR__ . '/../../layouts/admin-sidebar/sidebar.php'); ?>

  <main class="main-content">
    <h2>Edit Manager</h2>

    <form class="form" method="post"
      action="<?= htmlspecialchars($base . '/admin/service-managers/' . urlencode((string) $row['manager_id']), ENT_QUOTES, 'UTF-8') ?>"
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
          $options = ['active' => 'Active', 'inactive' => 'Inactive'];
          foreach ($options as $val => $label):
            ?>
            <option value="<?= $val ?>" <?= $st === $val ? 'selected' : '' ?>><?= $label ?></option>
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
        <a href="<?= htmlspecialchars($base . '/admin/service-managers/' . urlencode((string) $row['manager_id']), ENT_QUOTES, 'UTF-8') ?>"
          class="btn-secondary">Cancel</a>
      </div>
    </form>
  </main>
</body>

</html>