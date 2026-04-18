<?php $current='supervisors'; ?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Edit Supervisor <?= htmlspecialchars($s['supervisor_code']) ?></title>
 <link rel="stylesheet" href="../../../app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="../../../app/views/layouts/admin-sidebar/styles.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>.sidebar{position:fixed;top:0;left:0;width:260px;height:100vh;overflow-y:auto}.main-content{margin-left:260px;padding:30px;background:#fff;min-height:100vh}.form{max-width:700px}.form .row{display:grid;grid-template-columns:1fr 1fr;gap:16px}.form input,.form select{width:100%;padding:10px;border:1px solid #ddd;border-radius:8px}</style>
</head><body>
<?php include(__DIR__ . '/../../layouts/admin-sidebar/sidebar.php'); ?>
<main class="main-content">
  <h2>Edit Supervisor</h2>

  <form class="form" method="post" action="<?= rtrim(BASE_URL,'/') ?>/admin/supervisors/<?= urlencode($s['supervisor_code']) ?>">
    <div class="row">
      <div><label>First Name</label>
        <input name="first_name" required value="<?= htmlspecialchars($s['first_name']??'') ?>"></div>
      <div><label>Last Name</label>
        <input name="last_name" required value="<?= htmlspecialchars($s['last_name']??'') ?>"></div>
    </div>

    <div class="row">
      <div><label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($s['email']??'') ?>"></div>
      <div><label>Phone</label>
        <input name="phone" value="<?= htmlspecialchars($s['phone']??'') ?>"></div>
    </div>

    <div class="row">
      <div><label>Status</label>
        <select name="status">
          <?php $st=$s['status']??'active'; ?>
          <option value="active"   <?= $st==='active'?'selected':'' ?>>Active</option>
          <option value="inactive" <?= $st==='inactive'?'selected':'' ?>>Inactive</option>
        </select>
      </div>
      <div><label>Supervisor Code</label>
        <input name="supervisor_code" value="<?= htmlspecialchars($s['supervisor_code']??'') ?>">
      </div>
    </div>

    <div class="row">
      <div><label>Reset Password (optional)</label>
        <input name="password" type="password" placeholder="Leave blank to keep the same"></div>
      <div><label>Branch ID (optional)</label>
        <input name="branch_id" type="number" value="<?= htmlspecialchars($s['branch_id']??'') ?>"></div>
    </div>

    <div class="row">
      <div><label>Manager ID (optional)</label>
        <input name="manager_id" type="number" value="<?= htmlspecialchars($s['manager_id']??'') ?>"></div>
    </div>

    <p style="margin-top:18px">
      <button class="add-btn" type="submit">Save Changes</button>
      <a class="btn-secondary" href="<?= rtrim(BASE_URL,'/') ?>/admin/supervisors" style="margin-left:8px">Cancel</a>
    </p>
  </form>
</main>
</body></html>
