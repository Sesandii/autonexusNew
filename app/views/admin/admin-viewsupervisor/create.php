<?php $current='supervisors'; ?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Create Supervisor</title>
  <link rel="stylesheet" href="../../app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="../../app/views/layouts/admin-sidebar/styles.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>.sidebar{position:fixed;top:0;left:0;width:260px;height:100vh;overflow-y:auto}.main-content{margin-left:260px;padding:30px;background:#fff;min-height:100vh}.form{max-width:700px}.form .row{display:grid;grid-template-columns:1fr 1fr;gap:16px}.form input,.form select{width:100%;padding:10px;border:1px solid #ddd;border-radius:8px}</style>
</head><body>
<?php include(__DIR__ . '/../../layouts/admin-sidebar/sidebar.php'); ?>
<main class="main-content">
  <h2>Create Supervisor</h2>

  <?php if (!empty($errors)): ?>
  <div style="background:#ffecec;color:#b30000;padding:10px;border-radius:8px;margin-bottom:12px">
    <?= implode('<br>', array_map('htmlspecialchars',$errors)) ?>
  </div>
  <?php endif; ?>

  <form class="form" method="post"
      action="<?= rtrim(BASE_URL,'/') ?>/admin/supervisors"> <!-- not /admin/supervisor -->

    <div class="row">
      <div><label>First Name</label>
        <input name="first_name" required value="<?= htmlspecialchars($old['first_name']??'') ?>"></div>
      <div><label>Last Name</label>
        <input name="last_name" required value="<?= htmlspecialchars($old['last_name']??'') ?>"></div>
    </div>

    <div class="row">
      <div><label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($old['email']??'') ?>"></div>
      <div><label>Phone</label>
        <input name="phone" value="<?= htmlspecialchars($old['phone']??'') ?>"></div>
    </div>

    <div class="row">
      <div><label>Status</label>
        <select name="status">
          <?php $s=$old['status']??'active'; ?>
          <option value="active"   <?= $s==='active'?'selected':'' ?>>Active</option>
          <option value="inactive" <?= $s==='inactive'?'selected':'' ?>>Inactive</option>
        </select>
      </div>
      <div><label>Supervisor Code (optional)</label>
        <input name="supervisor_code" placeholder="Auto if empty" value="<?= htmlspecialchars($old['supervisor_code']??'') ?>">
      </div>
    </div>

    <div class="row">
      <div><label>Initial Password (optional)</label>
        <input name="password" type="password" placeholder="Defaults to 'autonexus' if blank"></div>
        <div class="row">
    <div>
      <label>Branch</label>
      <select id="branch_id" name="branch_id" required>
        <option value="">-- Select a branch --</option>
        <?php foreach (($branches ?? []) as $b): 
              $managerName = trim(($b['m_first'] ?? '').' '.($b['m_last'] ?? ''));
        ?>
          <option
            value="<?= (int)$b['branch_id'] ?>"
            data-manager-id="<?= htmlspecialchars($b['manager_id'] ?? '', ENT_QUOTES) ?>"
            data-manager-name="<?= htmlspecialchars($managerName, ENT_QUOTES) ?>"
            data-branch-name="<?= htmlspecialchars($b['name'] ?? '', ENT_QUOTES) ?>"
            <?= (isset($old['branch_id']) && (int)$old['branch_id'] === (int)$b['branch_id']) ? 'selected' : '' ?>
          >
            <?= htmlspecialchars(($b['branch_code'] ? $b['branch_code'].' • ' : '').($b['name'] ?? 'Branch')) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div>
      <label>Branch Manager</label>
      <input id="manager_name" class="input" type="text" readonly placeholder="Auto-filled">
      <input id="manager_id" name="manager_id" type="hidden">
    </div>
  </div>

  <p style="margin-top:18px">
    <button class="add-btn" type="submit">Create</button>
    <a class="btn-secondary" href="<?= rtrim(BASE_URL,'/') ?>/admin/supervisors" style="margin-left:8px">Cancel</a>
  </p>
</form>

<script>
(function(){
  var sel = document.getElementById('branch_id');
  var mgrName = document.getElementById('manager_name');
  var mgrId   = document.getElementById('manager_id');

  function syncManager(){
    var opt = sel.options[sel.selectedIndex];
    if (!opt) return;
    mgrName.value = opt.dataset.managerName || '';
    mgrId.value   = opt.dataset.managerId   || '';
  }
  sel.addEventListener('change', syncManager);
  syncManager(); // initial (covers repopulate after validation error)
})();
</script>
</main>
</body></html>
