<?php $current = $current ?? 'mechanics'; $B = rtrim(BASE_URL, '/'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mechanics</title>
 <link rel="stylesheet" href="../app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="../app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .sidebar{position:fixed;top:0;left:0;width:260px;height:100vh;overflow-y:auto}
    .main-content{margin-left:260px;padding:30px;background:#fff;min-height:100vh}
    .actions a,.actions form{display:inline-block;margin-right:6px}
    .actions a.btn{background:#111;color:#fff;padding:6px 10px;border-radius:8px}
    .actions a.btn.secondary{background:#555}
    .actions button{border:none;background:#d33;color:#fff;padding:6px 10px;border-radius:8px;cursor:pointer}
    .status--active{color:#166534;background:#16a34a22;border:1px solid #16a34a55;padding:2px 8px;border-radius:999px}
    .status--inactive{color:#7f1d1d;background:#ef444422;border:1px solid #ef444455;padding:2px 8px;border-radius:999px}
  </style>
</head>
<body>
<?php include(__DIR__ . '/../../layouts/admin-sidebar/sidebar.php'); ?>

<main class="main-content">
  <div class="management-header">
    <h2>Mechanics</h2>
    <div class="tools">
      <input type="text" class="search-input" id="searchInput" placeholder="Search by name/code/email/phone…">
      <a class="add-btn" href="<?= $B ?>/admin/mechanics/create">+ Add New Mechanic</a>
    </div>
  </div>

  <table id="mechanicsTable">
    <thead>
      <tr>
        <th>ID</th>
        <th>Code</th>
        <th>Full Name</th>
        <th>Specialization</th>
        <th>Exp</th>
        <th>Phone</th>
        <th>Branch</th>
        <th>Status</th>
        <th>Created</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
    <?php if (!empty($mechanics)): foreach ($mechanics as $m): ?>
      <tr>
        <td><?= htmlspecialchars($m['mechanic_id']) ?></td>
        <td><?= htmlspecialchars($m['mechanic_code'] ?? '') ?></td>
        <td><?= htmlspecialchars(($m['first_name'] ?? '').' '.($m['last_name'] ?? '')) ?></td>
        <td><?= htmlspecialchars($m['specialization'] ?? '') ?></td>
        <td><?= htmlspecialchars($m['experience_years'] ?? 0) ?></td>
        <td><?= htmlspecialchars($m['phone'] ?? '') ?></td>
        <td>
  <?php
    $code = $m['branch_code'] ?? null;
    $name = $m['branch_name'] ?? null;
    echo $name || $code
      ? '[' . htmlspecialchars($code ?? '-') . '] ' . htmlspecialchars($name ?? '-')
      : '—';
  ?>
</td>
        <td class="<?= ($m['mech_status'] === 'inactive') ? 'status--inactive' : 'status--active' ?>">
          <?= htmlspecialchars(ucfirst($m['mech_status'])) ?>
        </td>
        <td><?= htmlspecialchars($m['created_at'] ?? '') ?></td>
        <td class="actions">
          <a class="btn" href="<?= $B ?>/admin/mechanics/<?= urlencode($m['mechanic_id']) ?>"><i class="fa fa-eye"></i> View</a>
          <a class="btn secondary" href="<?= $B ?>/admin/mechanics/<?= urlencode($m['mechanic_id']) ?>/edit"><i class="fa fa-pen"></i> Edit</a>
          <form action="<?= $B ?>/admin/mechanics/<?= urlencode($m['mechanic_id']) ?>/delete" method="post" onsubmit="return confirm('Delete this mechanic?');">
            <button type="submit"><i class="fa fa-trash"></i> Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; else: ?>
      <tr><td colspan="10" style="text-align:center;padding:16px;">No mechanics found.</td></tr>
    <?php endif; ?>
    </tbody>
  </table>
</main>

<script>
(function(){
  var q=document.getElementById('searchInput');
  if(!q) return;
  var rows=[...document.querySelectorAll('#mechanicsTable tbody tr')];
  q.addEventListener('input',function(){
    var v=this.value.toLowerCase();
    rows.forEach(function(tr){
      tr.style.display = tr.innerText.toLowerCase().includes(v) ? '' : 'none';
    });
  });
})();
</script>
</body>
</html>
