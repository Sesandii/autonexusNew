<?php $current = 'customers'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Edit Customer #<?= (int)$c['customer_id'] ?></title>

  <link rel="stylesheet" href="<?= rtrim(BASE_URL,'/') ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="<?= rtrim(BASE_URL,'/') ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php include(__DIR__ . '/../../layouts/admin-sidebar/sidebar.php'); ?>

<main class="main-content">
  <header class="page-header">
    <div class="page-breadcrumb">
      <a href="<?= rtrim(BASE_URL,'/') ?>/admin/customers">Customers</a>
      <span>›</span>
      <span>Edit</span>
    </div>

    <div class="page-header-main">
      <div class="page-title-wrap">
        <div class="page-icon"><i class="fa-solid fa-pen-to-square"></i></div>
        <div>
          <h2>Edit Customer</h2>
          <p>Make changes to this customer’s details and status.</p>
        </div>
      </div>
      <span class="page-chip">Customer #<?= (int)$c['customer_id'] ?></span>
    </div>
  </header>

  <form class="form-card" method="post"
        action="<?= rtrim(BASE_URL, '/') ?>/admin/customers/<?= (int)$c['customer_id'] ?>">

    <div class="section-title">Basic Info</div>
    <div class="section-divider"></div>

    <div class="form-grid">
      <div>
        <label for="first_name">First Name</label>
        <input id="first_name" name="first_name" required
               value="<?= htmlspecialchars($c['first_name'] ?? '') ?>">
      </div>
      <div>
        <label for="last_name">Last Name</label>
        <input id="last_name" name="last_name" required
               value="<?= htmlspecialchars($c['last_name'] ?? '') ?>">
      </div>
    </div>

    <div class="form-grid" style="margin-top:14px;">
      <div>
        <label for="email">Email</label>
        <input id="email" type="email" name="email"
               value="<?= htmlspecialchars($c['email'] ?? '') ?>">
        <small>Used for booking confirmations and notifications.</small>
      </div>
      <div>
        <label for="phone">Phone</label>
        <input id="phone" name="phone"
               value="<?= htmlspecialchars($c['phone'] ?? '') ?>">
        <small>Primary contact number for this customer.</small>
      </div>
    </div>

    <div class="section-title" style="margin-top:22px;">Account & Status</div>
    <div class="section-divider"></div>

    <div class="form-grid">
      <div>
        <label for="status">Status</label>
        <?php $s = $c['status'] ?? 'active'; ?>
        <select id="status" name="status">
          <option value="active"   <?= $s==='active'?'selected':'' ?>>Active</option>
          <option value="inactive" <?= $s==='inactive'?'selected':'' ?>>Inactive</option>
          <option value="pending"  <?= $s==='pending'?'selected':'' ?>>Pending</option>
        </select>
        <small>Active customers can log in and create bookings.</small>
      </div>
      <div>
        <label for="customer_code">Customer Code</label>
        <input id="customer_code" name="customer_code"
               value="<?= htmlspecialchars($c['customer_code'] ?? '') ?>">
        <small>Internal reference code visible in admin lists.</small>
      </div>
    </div>

    <div style="margin-top:16px;">
      <label for="password">Reset Password (optional)</label>
      <input id="password" name="password" type="password"
             placeholder="Leave blank to keep the current password">
      <small>If filled, this will replace the existing password.</small>
    </div>

    <div class="form-actions">
      <button class="btn-primary" type="submit">
        <i class="fas fa-save"></i>&nbsp;Save Changes
      </button>
      <a class="btn-secondary"
         href="<?= rtrim(BASE_URL, '/') ?>/admin/customers">
        Cancel
      </a>
    </div>
  </form>
</main>
</body>
</html>
