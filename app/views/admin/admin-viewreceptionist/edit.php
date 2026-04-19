<?php /* Admin view: renders admin-viewreceptionist/edit page. */ ?>
<?php
/** @var array $rec */
/** @var array $branches */
/** @var array $errors */
$current = 'receptionists';
$B = rtrim(BASE_URL, '/');

function oldOrRec(string $key, array $rec)
{
  return $_POST[$key] ?? ($rec[$key] ?? '');
}

function e($value): string
{
  return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$code = $rec['receptionist_code'] ?? ('R' . $rec['receptionist_id']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Receptionist #<?= e($code) ?></title>

  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="<?= $B ?>/public/assets/css/admin/branches/create.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body>
  <?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

  <main class="main-content branch-create-page">
    <div class="branch-create-shell">
      <header class="create-header">
        <div class="create-title">
          <h1>Edit Receptionist</h1>
          <p>Update receptionist account details, branch assignment, and status.</p>
        </div>
        <a href="<?= e($B . '/admin/viewreceptionist') ?>" class="btn-secondary">
          <i class="fa-solid fa-arrow-left"></i>
          <span>Back to Receptionists</span>
        </a>
      </header>

      <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
          <?php foreach ($errors as $err): ?>
            <div><?= e($err) ?></div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form class="create-form-shell" method="post"
        action="<?= e($B . '/admin/receptionists/edit?id=' . urlencode((string) ($rec['receptionist_id'] ?? ''))) ?>">
        <section class="create-card">
          <div class="create-card-header">
            <i class="fa-solid fa-user-headset"></i>
            <h2>Receptionist Information</h2>
          </div>

          <div class="create-card-body">
            <div class="form-grid">
              <div class="field">
                <label class="label" for="first_name">First Name</label>
                <input class="input" id="first_name" name="first_name" required
                  value="<?= e(oldOrRec('first_name', $rec)) ?>">
              </div>

              <div class="field">
                <label class="label" for="last_name">Last Name</label>
                <input class="input" id="last_name" name="last_name" required
                  value="<?= e(oldOrRec('last_name', $rec)) ?>">
              </div>

              <div class="field">
                <label class="label" for="username">Username</label>
                <input class="input" id="username" name="username" required
                  value="<?= e(oldOrRec('username', $rec)) ?>">
              </div>

              <div class="field">
                <label class="label" for="email">Email</label>
                <input class="input" id="email" type="email" name="email" value="<?= e(oldOrRec('email', $rec)) ?>">
              </div>

              <div class="field">
                <label class="label" for="phone">Phone</label>
                <input class="input" id="phone" name="phone" type="tel" inputmode="numeric" pattern="^0[0-9]{9}$"
                  maxlength="10" placeholder="0712345678" value="<?= e(oldOrRec('phone', $rec)) ?>">
              </div>

              <div class="field">
                <label class="label" for="password">New Password (optional)</label>
                <input class="input" id="password" name="password" type="password"
                  placeholder="Leave blank to keep current password">
              </div>

              <div class="field">
                <label class="label" for="receptionist_code">Receptionist Code</label>
                <input class="input" id="receptionist_code" name="receptionist_code"
                  value="<?= e(oldOrRec('receptionist_code', $rec)) ?>">
              </div>

              <div class="field">
                <label class="label" for="branch_id">Assigned Branch</label>
                <?php $currentBranchId = $_POST['branch_id'] ?? $rec['branch_id'] ?? ''; ?>
                <select class="input" id="branch_id" name="branch_id">
                  <option value="">-- Select a branch --</option>
                  <?php foreach (($branches ?? []) as $b): ?>
                    <option value="<?= (int) $b['branch_id'] ?>" <?= (int) $currentBranchId === (int) $b['branch_id'] ? 'selected' : '' ?>>
                      <?= e(($b['branch_code'] ?? '') !== '' ? ($b['branch_code'] . ' • ' . ($b['name'] ?? 'Branch')) : ($b['name'] ?? 'Branch')) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="field">
                <label class="label" for="status">Status</label>
                <?php $st = $_POST['status'] ?? ($rec['status'] ?? 'active'); ?>
                <select class="input" id="status" name="status">
                  <option value="active" <?= $st === 'active' ? 'selected' : '' ?>>Active</option>
                  <option value="inactive" <?= $st === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
              </div>
            </div>
          </div>
        </section>

        <div class="form-actions">
          <a class="btn-secondary"
            href="<?= e($B . '/admin/receptionists/show?id=' . urlencode((string) ($rec['receptionist_id'] ?? ''))) ?>">
            <i class="fa-solid fa-xmark"></i>
            <span>Cancel</span>
          </a>

          <button class="btn-primary" type="submit">
            <i class="fa-solid fa-floppy-disk"></i>
            <span>Save Changes</span>
          </button>
        </div>
      </form>
    </div>
  </main>
</body>

</html>