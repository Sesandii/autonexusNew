<?php /* Admin view: renders admin-viewsupervisor/create page. */ ?>
<?php
$current = 'supervisors';
$base = rtrim($base ?? BASE_URL, '/');
$old = $old ?? [];
$errors = $errors ?? [];

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
  <title>Add Workshop Supervisor</title>
  <link rel="stylesheet" href="<?= $base ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="<?= $base ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/admin/branches/create.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body>
  <?php include(__DIR__ . '/../../layouts/admin-sidebar/sidebar.php'); ?>

  <main class="main-content branch-create-page">
    <div class="branch-create-shell">
      <header class="create-header">
        <div class="create-title">
          <h1>Add Workshop Supervisor</h1>
          <p>Create a new supervisor profile with branch assignment and login credentials.</p>
        </div>
        <a href="<?= e($base . '/admin/supervisors') ?>" class="btn-secondary">
          <i class="fa-solid fa-arrow-left"></i>
          <span>Back to Supervisors</span>
        </a>
      </header>

      <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
          <?php foreach ($errors as $err): ?>
            <div><?= e($err) ?></div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form class="create-form-shell" method="post" action="<?= e($base . '/admin/supervisors') ?>">
        <section class="create-card">
          <div class="create-card-header">
            <i class="fa-solid fa-user-check"></i>
            <h2>Supervisor Information</h2>
          </div>

          <div class="create-card-body">
            <div class="form-grid">
              <div class="field">
                <label class="label">First Name</label>
                <input class="input" name="first_name" value="<?= e($old['first_name'] ?? '') ?>" required>
              </div>

              <div class="field">
                <label class="label">Last Name</label>
                <input class="input" name="last_name" value="<?= e($old['last_name'] ?? '') ?>" required>
              </div>

              <div class="field">
                <label class="label">Email</label>
                <input class="input" type="email" name="email" value="<?= e($old['email'] ?? '') ?>" required>
              </div>

              <div class="field">
                <label class="label">Phone</label>
                <input class="input" name="phone" type="tel" inputmode="numeric" pattern="^0[0-9]{9}$" maxlength="10"
                  placeholder="0712345678" value="<?= e($old['phone'] ?? '') ?>">
              </div>

              <div class="field">
                <label class="label">Assigned Branch</label>
                <select id="branch_id" name="branch_id" class="input" required>
                  <option value="">-- Select a branch --</option>
                  <?php foreach (($branches ?? []) as $b):
                    $managerName = trim(($b['m_first'] ?? '') . ' ' . ($b['m_last'] ?? ''));
                    $managerCode = trim((string) ($b['m_code'] ?? ''));
                    ?>
                    <option value="<?= (int) $b['branch_id'] ?>"
                      data-manager-id="<?= e((string) ($b['manager_id'] ?? '')) ?>"
                      data-manager-name="<?= e($managerName) ?>" data-manager-code="<?= e($managerCode) ?>"
                      <?= (isset($old['branch_id']) && (int) $old['branch_id'] === (int) $b['branch_id']) ? 'selected' : '' ?>>
                      <?= e(($b['branch_code'] ? $b['branch_code'] . ' • ' : '') . ($b['name'] ?? 'Branch')) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="field">
                <label class="label">Status</label>
                <select name="status" class="input" required>
                  <?php $st = $old['status'] ?? 'active'; ?>
                  <option value="active" <?= $st === 'active' ? 'selected' : '' ?>>Active</option>
                  <option value="inactive" <?= $st === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
              </div>

              <div class="field">
                <label class="label">Password</label>
                <input class="input" type="password" name="password"
                  value="<?= e($old['password'] ?? 'Supervisor@123') ?>">
                <div class="hint">Default password is Supervisor@123.</div>
              </div>

              <div class="field">
                <label class="label">Branch Manager</label>
                <input id="manager_name" class="input" type="text" readonly
                  placeholder="Auto-filled from branch selection">
                <input id="manager_id" name="manager_id" type="hidden" value="<?= e($old['manager_id'] ?? '') ?>">
              </div>

              <div class="field">
                <label class="label">Manager Code</label>
                <input id="manager_code" class="input" type="text" readonly
                  placeholder="Auto-filled from branch selection">
              </div>
            </div>
          </div>
        </section>

        <div class="form-actions">
          <a href="<?= e($base . '/admin/supervisors') ?>" class="btn-secondary">
            <i class="fa-solid fa-xmark"></i>
            <span>Cancel</span>
          </a>

          <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            <span>Save Supervisor</span>
          </button>
        </div>
      </form>
    </div>
  </main>

  <script>
    (function () {
      var sel = document.getElementById('branch_id');
      var mgrName = document.getElementById('manager_name');
      var mgrCode = document.getElementById('manager_code');
      var mgrId = document.getElementById('manager_id');

      function syncManager() {
        var opt = sel.options[sel.selectedIndex];
        if (!opt) {
          return;
        }
        mgrName.value = opt.dataset.managerName || 'Not assigned';
        mgrCode.value = opt.dataset.managerCode || 'Not assigned';
        mgrId.value = opt.dataset.managerId || '';
      }

      sel.addEventListener('change', syncManager);
      syncManager();
    })();
  </script>
</body>

</html>