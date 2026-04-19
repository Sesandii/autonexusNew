<?php /* Admin view: renders admin-viewreceptionist/create page. */ ?>
<?php
/** @var array $branches */
/** @var array $errors */
$current = 'receptionists';
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
  <title>Add Receptionist</title>

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
          <h1>Add Receptionist</h1>
          <p>Create a receptionist account and assign branch access.</p>
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

      <form class="create-form-shell" method="post" action="<?= e($B . '/admin/receptionists/create') ?>">
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
                  value="<?= e($_POST['first_name'] ?? '') ?>">
              </div>

              <div class="field">
                <label class="label" for="last_name">Last Name</label>
                <input class="input" id="last_name" name="last_name" required
                  value="<?= e($_POST['last_name'] ?? '') ?>">
              </div>

              <div class="field">
                <label class="label" for="username">Username</label>
                <input class="input" id="username" name="username" required value="<?= e($_POST['username'] ?? '') ?>">
              </div>

              <div class="field">
                <label class="label" for="email">Email</label>
                <input class="input" id="email" type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>">
              </div>

              <div class="field">
                <label class="label" for="phone">Phone</label>
                <input class="input" id="phone" name="phone" type="tel" inputmode="numeric" pattern="^0[0-9]{9}$"
                  maxlength="10" placeholder="0712345678" value="<?= e($_POST['phone'] ?? '') ?>">
              </div>

              <div class="field">
                <label class="label" for="password">Password</label>
                <input class="input" id="password" name="password" type="password" required>
                <div class="hint">Initial password for this account.</div>
              </div>

              <div class="field">
                <label class="label" for="receptionist_code">Receptionist Code</label>
                <input class="input" id="receptionist_code" name="receptionist_code"
                  placeholder="Auto-generated if empty" value="<?= e($_POST['receptionist_code'] ?? '') ?>">
              </div>

              <div class="field">
                <label class="label" for="branch_id">Assigned Branch</label>
                <select class="input" id="branch_id" name="branch_id">
                  <option value="">-- Select a branch --</option>
                  <?php foreach (($branches ?? []) as $b): ?>
                    <option value="<?= (int) $b['branch_id'] ?>" <?= (isset($_POST['branch_id']) && (int) $_POST['branch_id'] === (int) $b['branch_id']) ? 'selected' : '' ?>>
                      <?= e(($b['branch_code'] ?? '') !== '' ? ($b['branch_code'] . ' • ' . ($b['name'] ?? 'Branch')) : ($b['name'] ?? 'Branch')) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="field">
                <label class="label" for="status">Status</label>
                <?php $st = $_POST['status'] ?? 'active'; ?>
                <select class="input" id="status" name="status">
                  <option value="active" <?= $st === 'active' ? 'selected' : '' ?>>Active</option>
                  <option value="inactive" <?= $st === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
              </div>
            </div>
          </div>
        </section>

        <div class="form-actions">
          <a class="btn-secondary" href="<?= e($B . '/admin/viewreceptionist') ?>">
            <i class="fa-solid fa-xmark"></i>
            <span>Cancel</span>
          </a>

          <button class="btn-primary" type="submit">
            <i class="fa-solid fa-floppy-disk"></i>
            <span>Save Receptionist</span>
          </button>
        </div>
      </form>
    </div>
  </main>
</body>

</html>