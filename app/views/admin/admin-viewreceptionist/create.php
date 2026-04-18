<?php
/** @var array $branches */
/** @var array $errors */
$current = 'receptionists';
$B = rtrim(BASE_URL, '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Create Receptionist</title>

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
      <span>Create</span>
    </div>

    <div class="page-header-main">
      <div class="page-title-wrap">
        <div class="page-icon"><i class="fa-solid fa-user-plus"></i></div>
        <div>
          <h2>Create Receptionist</h2>
          <p>Add a new receptionist and assign them to a branch.</p>
        </div>
      </div>
      <span class="page-chip">New staff</span>
    </div>
  </header>

  <?php if (!empty($errors)): ?>
    <div class="error-box">
      <?php foreach ($errors as $err): ?>
        <?= htmlspecialchars($err) ?><br>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <!-- No explicit action: posts back to /admin/receptionists/create -->
  <form class="form-card" method="post">

    <div class="section-title">User Account</div>
    <div class="section-divider"></div>

    <div class="form-grid">
      <div>
        <label for="first_name">First Name</label>
        <input id="first_name" name="first_name" required
               value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>">
      </div>
      <div>
        <label for="last_name">Last Name</label>
        <input id="last_name" name="last_name" required
               value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>">
      </div>
    </div>

    <div class="form-grid" style="margin-top:14px;">
      <div>
        <label for="username">Username</label>
        <input id="username" name="username" required
               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
        <small>Used to sign in to the receptionist portal.</small>
      </div>
      <div>
        <label for="email">Email</label>
        <input id="email" type="email" name="email"
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        <small>We’ll send important notifications to this address.</small>
      </div>
    </div>

    <div class="form-grid" style="margin-top:14px;">
      <div>
        <label for="phone">Phone</label>
        <input id="phone" name="phone"
               value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
        <small>Primary contact number at the front desk.</small>
      </div>
      <div>
        <label for="password">Password</label>
        <input id="password" name="password" type="password" required>
        <small>Initial password for this account.</small>
      </div>
    </div>

    <div class="section-title" style="margin-top:22px;">Receptionist Profile</div>
    <div class="section-divider"></div>

    <div class="form-grid">
      <div>
        <label for="receptionist_code">Receptionist Code</label>
        <input id="receptionist_code" name="receptionist_code"
               placeholder="Optional – auto-generate if left blank"
               value="<?= htmlspecialchars($_POST['receptionist_code'] ?? '') ?>">
        <small>Internal reference code (e.g. REC010).</small>
      </div>

      <div>
        <label for="branch_id">Branch</label>
        <select id="branch_id" name="branch_id">
          <option value="">-- Select Branch --</option>
          <?php foreach ($branches as $b): ?>
            <option value="<?= (int)$b['branch_id'] ?>"
              <?= (isset($_POST['branch_id']) && (int)$_POST['branch_id'] === (int)$b['branch_id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($b['name']) ?> (<?= htmlspecialchars($b['branch_code']) ?>)
            </option>
          <?php endforeach; ?>
        </select>
        <small>Choose the workshop/branch this receptionist belongs to.</small>
      </div>
    </div>

    <div class="form-grid" style="margin-top:14px;">
      <div>
        <label for="status">Status</label>
        <?php $st = $_POST['status'] ?? 'active'; ?>
        <select id="status" name="status">
          <option value="active"   <?= $st==='active'?'selected':'' ?>>Active</option>
          <option value="inactive" <?= $st==='inactive'?'selected':'' ?>>Inactive</option>
        </select>
        <small>Inactive staff cannot log in.</small>
      </div>
    </div>

    <div class="form-actions">
      <button class="btn-primary" type="submit">
        <i class="fas fa-plus"></i>&nbsp;Create Receptionist
      </button>
      <a class="btn-secondary" href="<?= $B ?>/admin/viewreceptionist">
        Cancel
      </a>
    </div>
  </form>
</main>
</body>
</html>
