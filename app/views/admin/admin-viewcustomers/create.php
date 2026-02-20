<?php $current = 'customers'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Create Customer</title>

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
      <span>Create</span>
    </div>

    <div class="page-header-main">
      <div class="page-title-wrap">
        <div class="page-icon"><i class="fa-solid fa-user-plus"></i></div>
        <div>
          <h2>Create Customer</h2>
          <p>Add a new customer to AutoNexus.</p>
        </div>
      </div>
      <span class="page-chip">New record</span>
    </div>
  </header>

  <?php if (!empty($errors)): ?>
    <div class="error-box">
      <?php foreach ($errors as $err): ?>
        <?= htmlspecialchars($err) ?><br>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form class="form-card" method="post"
        action="<?= rtrim(BASE_URL,'/') ?>/admin/customers">

    <div class="section-title">Basic Info</div>
    <div class="section-divider"></div>

    <div class="form-grid">
      <div>
        <label for="first_name">First Name</label>
        <input id="first_name" name="first_name" required
               value="<?= htmlspecialchars($old['first_name'] ?? '') ?>">
      </div>
      <div>
        <label for="last_name">Last Name</label>
        <input id="last_name" name="last_name" required
               value="<?= htmlspecialchars($old['last_name'] ?? '') ?>">
      </div>
    </div>

    <div class="form-grid" style="margin-top:14px;">
      <div>
        <label for="email">Email</label>
        <input id="email" type="email" name="email"
               value="<?= htmlspecialchars($old['email'] ?? '') ?>">
        <small>We’ll send booking confirmations to this address.</small>
      </div>
      <div>
        <label for="phone">Phone</label>
        <input id="phone" name="phone"
               value="<?= htmlspecialchars($old['phone'] ?? '') ?>">
        <small>Primary contact number for the customer.</small>
      </div>
    </div>

    <div class="section-title" style="margin-top:22px;">Account & Status</div>
    <div class="section-divider"></div>

    <div class="form-grid">
      <div>
        <label for="status">Status</label>
        <?php $s = $old['status'] ?? 'active'; ?>
        <select id="status" name="status">
          <option value="active"   <?= $s==='active'?'selected':'' ?>>Active</option>
          <option value="inactive" <?= $s==='inactive'?'selected':'' ?>>Inactive</option>
          <option value="pending"  <?= $s==='pending'?'selected':'' ?>>Pending</option>
        </select>
        <small>Choose how this account should behave initially.</small>
      </div>
      <div>
        <label for="customer_code">Customer Code (optional)</label>
        <input id="customer_code" name="customer_code"
               placeholder="Auto-generated if left blank"
               value="<?= htmlspecialchars($old['customer_code'] ?? '') ?>">
        <small>Useful if you follow a custom coding scheme.</small>
      </div>
    </div>

    <div style="margin-top:16px;">
      <label for="password">Initial Password (optional)</label>
      <input id="password" name="password" type="password"
             placeholder="Defaults to 'autonexus' if left blank">
      <small>Customer can change this after logging in.</small>
    </div>

    <div class="form-actions">
      <button class="btn-primary" type="submit">
        <i class="fas fa-plus"></i>&nbsp;Create Customer
      </button>
      <a class="btn-secondary"
         href="<?= rtrim(BASE_URL,'/') ?>/admin/customers">
        Cancel
      </a>
    </div>
  </form>
</main>
</body>
</html>
