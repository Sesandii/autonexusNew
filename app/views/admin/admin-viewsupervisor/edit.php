<?php /* Admin view: renders admin-viewsupervisor/edit page. */ ?>
<?php
$current = 'supervisors';
$base = rtrim($base ?? BASE_URL, '/');
$errors = $errors ?? [];
$branches = $branches ?? [];
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Edit Supervisor <?= htmlspecialchars($s['supervisor_code']) ?></title>
  <link rel="stylesheet" href="<?= $base ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="<?= $base ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="<?= $base ?>/app/views/admin/admin-viewsupervisor/supervisors.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body>
  <?php include(__DIR__ . '/../../layouts/admin-sidebar/sidebar.php'); ?>
  <main class="main-content">
    <h2>Edit Supervisor</h2>

    <?php if (!empty($errors)): ?>
      <div style="background:#ffecec;color:#b30000;padding:10px;border-radius:8px;margin-bottom:12px">
        <?= implode('<br>', array_map('htmlspecialchars', $errors)) ?>
      </div>
    <?php endif; ?>

    <form class="form" method="post" action="<?= $base ?>/admin/supervisors/<?= urlencode($s['supervisor_code']) ?>">
      <input type="hidden" name="user_id" value="<?= htmlspecialchars((string) ($s['user_id'] ?? '')) ?>">

      <div class="row">
        <div>
          <label>Supervisor ID</label>
          <input value="<?= htmlspecialchars((string) ($s['supervisor_id'] ?? '')) ?>" readonly>
        </div>
        <div>
          <label>Supervisor Code</label>
          <input name="supervisor_code" value="<?= htmlspecialchars($s['supervisor_code'] ?? '') ?>" required>
        </div>
      </div>

      <div class="row">
        <div>
          <label>First Name</label>
          <input name="first_name" required value="<?= htmlspecialchars($s['first_name'] ?? '') ?>">
        </div>
        <div>
          <label>Last Name</label>
          <input name="last_name" required value="<?= htmlspecialchars($s['last_name'] ?? '') ?>">
        </div>
      </div>

      <div class="row">
        <div>
          <label>Email</label>
          <input type="email" name="email" required value="<?= htmlspecialchars($s['email'] ?? '') ?>">
        </div>
        <div>
          <label>Phone</label>
          <input name="phone" type="tel" inputmode="numeric" pattern="^0[0-9]{9}$" maxlength="10"
            placeholder="0712345678" value="<?= htmlspecialchars($s['phone'] ?? '') ?>">
        </div>
      </div>

      <div class="row">
        <div>
          <label>Status</label>
          <select name="status">
            <?php $st = $s['status'] ?? 'active'; ?>
            <option value="active" <?= $st === 'active' ? 'selected' : '' ?>>Active</option>
            <option value="inactive" <?= $st === 'inactive' ? 'selected' : '' ?>>Inactive</option>
          </select>
        </div>
        <div>
          <label>Created At</label>
          <input value="<?= htmlspecialchars((string) ($s['created_at'] ?? '')) ?>" readonly>
        </div>
      </div>

      <div class="row">
        <div>
          <label>Branch</label>
          <select id="branch_id" name="branch_id" required>
            <option value="">-- Select a branch --</option>
            <?php foreach ($branches as $b):
              $managerName = trim(($b['m_first'] ?? '') . ' ' . ($b['m_last'] ?? ''));
              $managerCode = trim((string) ($b['m_code'] ?? ''));
              $selected = ((int) ($s['branch_id'] ?? 0) === (int) ($b['branch_id'] ?? 0));
              ?>
              <option value="<?= (int) $b['branch_id'] ?>"
                data-manager-id="<?= htmlspecialchars((string) ($b['manager_id'] ?? ''), ENT_QUOTES) ?>"
                data-manager-name="<?= htmlspecialchars($managerName, ENT_QUOTES) ?>"
                data-manager-code="<?= htmlspecialchars($managerCode, ENT_QUOTES) ?>" <?= $selected ? 'selected' : '' ?>>
                <?= htmlspecialchars(($b['branch_code'] ? $b['branch_code'] . ' • ' : '') . ($b['name'] ?? 'Branch')) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label>Manager Code</label>
          <input id="manager_code" value="<?= htmlspecialchars((string) ($s['manager_code'] ?? 'Not assigned')) ?>"
            readonly>
        </div>
      </div>

      <div class="row">
        <div>
          <label>Branch Manager</label>
          <input id="manager_name"
            value="<?= htmlspecialchars(trim(($s['manager_first_name'] ?? '') . ' ' . ($s['manager_last_name'] ?? '')) ?: 'Not assigned') ?>"
            readonly>
          <input id="manager_id" name="manager_id" type="hidden"
            value="<?= htmlspecialchars((string) ($s['manager_id'] ?? '')) ?>">
        </div>
        <div>
          <label>Reset Password (optional)</label>
          <input name="password" type="password" placeholder="Leave blank to keep the same">
        </div>
      </div>

      <p style="margin-top:18px">
        <button class="add-btn" type="submit">Save Changes</button>
        <a class="btn-secondary" href="<?= $base ?>/admin/supervisors" style="margin-left:8px">Cancel</a>
      </p>
    </form>

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
  </main>
</body>

</html>