<?php $current = 'services'; $base = rtrim(BASE_URL,'/'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Service Management</title>

  <link rel="stylesheet" href="<?= $base ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/admin/services/styles.css">
  <style>
    .sidebar{position:fixed;top:0;left:0;width:260px;height:100vh;overflow-y:auto}
    .main-content{margin-left:260px;padding:30px;background:#fff;min-height:100vh}
    .admin-title{margin:0}
    .section-header{display:flex;align-items:end;justify-content:space-between;margin-bottom:14px}
    .btn-primary{background:#0ea5e9;color:#fff;border:0;border-radius:10px;padding:10px 14px;text-decoration:none}
    .btn-primary:hover{background:#0284c7}
    .tabs{display:flex;gap:8px;flex-wrap:wrap;margin:10px 0 16px}
    .tab{border:1px solid #e5e7eb;border-radius:999px;padding:8px 12px;background:#f8fafc;cursor:pointer}
    .tab.active{background:#0ea5e9;color:#fff;border-color:#0ea5e9}
    .table-container{overflow:auto;border:1px solid #e5e7eb;border-radius:12px}
    table{width:100%;border-collapse:separate;border-spacing:0}
    th,td{padding:12px 14px;border-bottom:1px solid #eef2f7;text-align:left}
    th{background:#f8fafc;font-weight:700}
    .badge{display:inline-block;padding:4px 8px;border-radius:999px;background:#eef2ff}
    .actions .icon-btn{cursor:pointer;margin-right:8px}
    .muted{color:#6b7280;font-size:12px}
  </style>
</head>
<body>
  <?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

  <div class="container">
    <main class="main-content">
      <section class="service-section">
        <div class="section-header">
          <div>
            <h1 class="admin-title">Service Management</h1>
            <p class="muted">Manage your service offerings</p>
          </div>
          <a class="btn-primary" href="<?= $base ?>/admin/services/create">+ Add New Service</a>
        </div>

        <!-- Tabs -->
        <div class="tabs" id="tabs">
          <button class="tab active" data-tab="all">All</button>
          <?php foreach (($tabs ?? []) as $t): ?>
            <button class="tab" data-tab="type-<?= (int)$t['type_id'] ?>">
              <?= htmlspecialchars($t['type_name']) ?>
            </button>
          <?php endforeach; ?>
        </div>

        <!-- Table -->
        <div class="table-container">
          <table class="service-table" id="serviceTable">
            <thead>
              <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Description</th>
                <th>Category</th>
                <th>Branches</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($services)): ?>
                <?php foreach ($services as $row): ?>
                  <tr data-type="type-<?= (int)($row['type_id'] ?? 0) ?>">
                    <td><?= htmlspecialchars($row['service_code']) ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['description'] ?? '') ?></td>
                    <td><span class="badge"><?= htmlspecialchars($row['type_name'] ?? 'Uncategorized') ?></span></td>
                    <td>
                      <?php if ((int)$row['branch_count'] > 0): ?>
                        <?= htmlspecialchars($row['branches']) ?>
                        <span class="muted">(<?= (int)$row['branch_count'] ?>)</span>
                      <?php else: ?>
                        <span class="muted">Not assigned</span>
                      <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                    <td class="actions">
  <a class="icon-btn" title="Edit"
     href="<?= $base ?>/admin/services/<?= (int)$row['service_id'] ?>/edit">✏️</a>

  <form action="<?= $base ?>/admin/services/<?= (int)$row['service_id'] ?>/delete"
        method="post" style="display:inline"
        onsubmit="return confirm('Delete this service? This cannot be undone.');">
    <button class="icon-btn" title="Delete" style="background:none;border:0;cursor:pointer">🗑️</button>
  </form>
</td>

                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="7" class="muted">No services found.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>
    </main>
  </div>
<!-- <script src="<?= rtrim(BASE_URL,'/') ?>/public/assets/js/admin/services/script.js"></script> -->
  <script>
    // Simple tab filter (All or by type id)
    const tabs = document.querySelectorAll('#tabs .tab');
    const rows = document.querySelectorAll('#serviceTable tbody tr');
    tabs.forEach(tab => {
      tab.addEventListener('click', () => {
        tabs.forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        const key = tab.getAttribute('data-tab');
        rows.forEach(r => {
          if (key === 'all') { r.style.display = ''; return; }
          r.style.display = (r.getAttribute('data-type') === key) ? '' : 'none';
        });
      });
    });
  </script>
</body>
</html>

  

