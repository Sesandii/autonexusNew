<?php /* Admin view: renders admin-viewcustomers/create page. */ ?>
<?php
$current = 'customers';
$B = rtrim(BASE_URL, '/');

function e($value): string
{
  return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Customer</title>

  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="<?= $B ?>/public/assets/css/admin/branches/create.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body>
  <?php include(__DIR__ . '/../../layouts/admin-sidebar/sidebar.php'); ?>

  <main class="main-content branch-create-page">
    <div class="branch-create-shell">
      <header class="create-header">
        <div class="create-title">
          <h1>Add Customer</h1>
          <p>Create a new customer account with contact details and initial status.</p>
        </div>
        <a href="<?= e($B . '/admin/customers') ?>" class="btn-secondary">
          <i class="fa-solid fa-arrow-left"></i>
          <span>Back to Customers</span>
        </a>
      </header>

      <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
          <?php foreach ($errors as $err): ?>
            <div><?= e($err) ?></div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form class="create-form-shell" method="post" action="<?= e($B . '/admin/customers') ?>">
        <section class="create-card">
          <div class="create-card-header">
            <i class="fa-solid fa-user-plus"></i>
            <h2>Customer Information</h2>
          </div>

          <div class="create-card-body">
            <div class="form-grid">
              <div class="field">
                <label class="label" for="first_name">First Name</label>
                <input class="input" id="first_name" name="first_name" required
                  value="<?= e($old['first_name'] ?? '') ?>">
              </div>

              <div class="field">
                <label class="label" for="last_name">Last Name</label>
                <input class="input" id="last_name" name="last_name" required value="<?= e($old['last_name'] ?? '') ?>">
              </div>

              <div class="field">
                <label class="label" for="email">Email</label>
                <input class="input" id="email" type="email" name="email" value="<?= e($old['email'] ?? '') ?>">
                <div class="hint">We’ll send booking confirmations to this address.</div>
              </div>

              <div class="field">
                <label class="label" for="phone">Phone</label>
                <input class="input" id="phone" name="phone" value="<?= e($old['phone'] ?? '') ?>">
                <div class="hint">Primary contact number for the customer.</div>
              </div>

              <div class="field">
                <label class="label" for="status">Status</label>
                <?php $s = $old['status'] ?? 'active'; ?>
                <select class="input" id="status" name="status">
                  <option value="active" <?= $s === 'active' ? 'selected' : '' ?>>Active</option>
                  <option value="inactive" <?= $s === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                  <option value="pending" <?= $s === 'pending' ? 'selected' : '' ?>>Pending</option>
                </select>
              </div>

              <div class="field">
                <label class="label" for="customer_code">Customer Code (optional)</label>
                <input class="input" id="customer_code" name="customer_code" placeholder="Auto-generated if left blank"
                  value="<?= e($old['customer_code'] ?? '') ?>">
              </div>

              <div class="field full">
                <label class="label" for="password">Initial Password (optional)</label>
                <input class="input" id="password" name="password" type="password"
                  placeholder="Defaults to 'autonexus' if left blank">
                <div class="hint">Customer can change this after logging in.</div>
              </div>
            </div>
          </div>
        </section>

        <div class="form-actions">
          <a class="btn-secondary" href="<?= e($B . '/admin/customers') ?>">
            <i class="fa-solid fa-xmark"></i>
            <span>Cancel</span>
          </a>

          <button class="btn-primary" type="submit">
            <i class="fa-solid fa-floppy-disk"></i>
            <span>Save Customer</span>
          </button>
        </div>
      </form>
    </div>
  </main>
</body>

</html>