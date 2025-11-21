<?php
/** @var array $rec */
/** @var array $branches */
/** @var array $errors */
$current = 'receptionists';
$B = rtrim(BASE_URL, '/');

function oldOrRec(string $key, array $rec) {
    return $_POST[$key] ?? ($rec[$key] ?? '');
}

$code = $rec['receptionist_code'] ?? ('R' . $rec['receptionist_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Edit Receptionist #<?= htmlspecialchars($code) ?></title>

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
      <span>Edit</span>
    </div>

    <div class="page-header-main">
      <div class="page-title-wrap">
        <div class="page-icon"><i class="fa-solid fa-pen-to-square"></i></div>
        <div>
          <h2>Edit Receptionist</h2>
          <p>Update account details, branch assignment, and status.</p>
        </div>
      </div>
      <span class="page-chip">Code: <?= htmlspecialchars($code) ?></span>
    </div>
  </header>

  <?php if (!empty($errors)): ?>
    <div class="error-box">
      <?php foreach ($errors as $err): ?>
        <?= htmlspecialchars($err) ?><br>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <!-- No explicit action: posts back to /admin/receptionists/edit?id=... -->
  <form class="form-card" method="post">

    <div class="section-title">User Account</div>
    <div class="section-divider"></div>

    <div class="form-grid">
      <div>
        <label for="first_name">First Name</label>
        <input id="first_name" name="first_name" required
               value="<?= htmlspecialchars(oldOrRec('first_name', $rec)) ?>">
      </div>
      <div>
        <label for="last_name">Last Name</label>
        <input id="last_name" name="last_name" required
               value="<?= htmlspecialchars(oldOrRec('last_name', $rec)) ?>">
      </div>
    </div>

    <div class="form-grid" style="margin-top:14px;">
      <div>
        <label for="username">Username</label>
        <input id="username" name="username" required
               value="<?= htmlspecialchars(oldOrRec('username', $rec)) ?>">
        <small>Used to sign in to the receptionist portal.</small>
      </div>
      <div>
        <label for="email">Email</label>
        <input id="email" type="email" name="email"
               value="<?= htmlspecialchars(oldOrRec('email', $rec)) ?>">
        <small>We’ll send important notifications to this address.</small>
      </div>
    </div>

    <div class="form-grid" style="margin-top:14px;">
      <div>
        <label for="phone">Phone</label>
        <input id="phone" name="phone"
               value="<?= htmlspecialchars(oldOrRec('phone', $rec)) ?>">
        <small>Primary contact number at the front desk.</small>
      </div>
      <div>
        <label for="password">New Password (optional)</label>
        <input id="password" name="password" type="password"
               placeholder="Leave blank to keep current password">
        <small>If filled, this will replace the existing password.</small>
      </div>
    </div>

    <div class="section-title" style="margin-top:22px;">Receptionist Profile</div>
    <div class="section-divider"></div>

    <div class="form-grid">
      <div>
        <label for="receptionist_code">Receptionist Code</label>
        <input id="receptionist_code" name="receptionist_code"
               value="<?= htmlspecialchars(oldOrRec('receptionist_code', $rec)) ?>">
        <small>Internal reference code shown in lists.</small>
      </div>

      <div>
        <label for="branch_id">Branch</label>
        <?php $currentBranchId = $_POST['branch_id'] ?? $rec['branch_id'] ?? ''; ?>
        <select id="branch_id" name="branch_id">
          <option value="">-- Select Branch --</option>
          <?php foreach ($branches as $b): ?>
            <option value="<?= (int)$b['branch_id'] ?>"
              <?= (int)$currentBranchId === (int)$b['branch_id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($b['name']) ?> (<?= htmlspecialchars($b['branch_code']) ?>)
            </option>
          <?php endforeach; ?>
        </select>
        <small>Choose which branch this receptionist manages.</small>
      </div>
    </div>

    <div class="form-grid" style="margin-top:14px;">
      <div>
        <label for="status">Status</label>
        <?php $st = $_POST['status'] ?? ($rec['status'] ?? 'active'); ?>
        <select id="status" name="status">
          <option value="active"   <?= $st==='active'?'selected':'' ?>>Active</option>
          <option value="inactive" <?= $st==='inactive'?'selected':'' ?>>Inactive</option>
        </select>
        <small>Inactive staff cannot log in.</small>
      </div>
    </div>

    <div class="form-actions">
      <button class="btn-primary" type="submit">
        <i class="fas fa-save"></i>&nbsp;Save Changes
      </button>
      <a class="btn-secondary"
         href="<?= $B ?>/admin/viewreceptionist">
        Cancel
      </a>
    </div>
  </form>
</main>
</body>
</html>
