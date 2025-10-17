<?php $current = 'customers'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Edit Customer #<?= (int)$c['customer_id'] ?></title>
  <link rel="stylesheet" href="../../../app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="../../../app/views/layouts/admin-sidebar/styles.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>.sidebar{position:fixed;top:0;left:0;width:260px;height:100vh;overflow-y:auto}.main-content{margin-left:260px;padding:30px;background:#fff;min-height:100vh}.form{max-width:700px}.form .row{display:grid;grid-template-columns:1fr 1fr;gap:16px}.form label{display:block;margin:8px 0 4px}.form input,.form select{width:100%;padding:10px;border:1px solid #ddd;border-radius:8px}</style>
</head>
<body>
<?php include(__DIR__ . '/../../layouts/admin-sidebar/sidebar.php'); ?>
<main class="main-content">
  <h2>Edit Customer</h2>

  <form class="form" method="post" action="<?= rtrim(BASE_URL, '/') ?>/admin/customers/<?= (int)$c['customer_id'] ?>">
    <div class="row">
      <div>
        <label>First Name</label>
        <input name="first_name" required value="<?= htmlspecialchars($c['first_name'] ?? '') ?>">
      </div>
      <div>
        <label>Last Name</label>
        <input name="last_name" required value="<?= htmlspecialchars($c['last_name'] ?? '') ?>">
      </div>
    </div>

    <div class="row">
      <div>
        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($c['email'] ?? '') ?>">
      </div>
      <div>
        <label>Phone</label>
        <input name="phone" value="<?= htmlspecialchars($c['phone'] ?? '') ?>">
      </div>
    </div>

    <div class="row">
      <div>
        <label>Status</label>
        <select name="status">
          <?php $s = $c['status'] ?? 'active'; ?>
          <option value="active"   <?= $s==='active'?'selected':'' ?>>Active</option>
          <option value="inactive" <?= $s==='inactive'?'selected':'' ?>>Inactive</option>
          <option value="pending"  <?= $s==='pending'?'selected':'' ?>>Pending</option>
        </select>
      </div>
      <div>
        <label>Customer Code</label>
        <input name="customer_code" value="<?= htmlspecialchars($c['customer_code'] ?? '') ?>">
      </div>
    </div>

    <div class="row">
      <div>
        <label>Reset Password (optional)</label>
        <input name="password" type="password" placeholder="Leave blank to keep the same">
      </div>
    </div>

    <p style="margin-top:18px">
      <button class="add-btn" type="submit">Save Changes</button>
     <a class="btn-secondary" href="<?= rtrim(BASE_URL, '/') ?>/admin/customers" style="margin-left:8px">Cancel</a>
    </p>
  </form>
</main>
</body>
</html>
