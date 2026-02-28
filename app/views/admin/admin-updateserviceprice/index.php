<!-- admin/admin-updateserviceprice -->
<?php $current = 'pricing'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - Service Pricing Management</title>

  <link rel="stylesheet" href="<?= rtrim(BASE_URL,'/') ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="<?= rtrim(BASE_URL,'/') ?>/app/views/layouts/admin-sidebar/styles.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    .sidebar { position: fixed; top: 0; left: 0; width: 260px; height: 100vh; overflow-y: auto; }
    .main-content { margin-left: 260px; padding: 30px; background: #fff; min-height: 100vh; }
    .flash { margin: 10px 0 0; padding: 8px 12px; border-radius: 6px; background:#eef8ee; color:#205c20; display:inline-block; }
    .price-input { width: 140px; }
    .btn-save i { margin-right: 6px; }
    form.inline { display:inline; margin:0; }
  </style>
</head>
<body>
<?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

<main class="main-content">

  <div class="management-header">
    <h2>Service Pricing Management</h2>
    <div class="tools">
      <input type="text" class="search-input" id="searchInput" placeholder="Search by service id/name..." />
      <select class="status-filter">
        <option value="all">All Service Types</option>
      </select>
    </div>

    <?php if (!empty($flash)): ?>
      <div class="flash"><?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>
  </div>

  <table>
    <thead>
      <tr>
        <th>Service ID</th>
        <th>Service Name</th>
        <th>Service Type</th>
        <th>Current Price</th>
        <th>Update Price</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
    <?php if (!empty($services)): ?>
      <?php foreach ($services as $s): ?>
        <tr>
          <td><?= htmlspecialchars($s['service_code']) ?></td>
          <td><?= htmlspecialchars($s['name']) ?></td>
          <td><?= htmlspecialchars($s['type_name'] ?? 'Uncategorized') ?></td>
          <td><?= number_format((float)($s['default_price'] ?? 0), 2) ?> LKR</td>

          <td>
            <form method="post" class="inline"
      action="<?= rtrim(BASE_URL, '/') ?>/admin/admin-updateserviceprice">

              <input type="hidden" name="service_id" value="<?= (int)$s['service_id'] ?>">
              <input class="price-input" type="number" step="0.01" min="0"
                     name="price" placeholder="Enter new price"
                     value="<?= htmlspecialchars((string)($s['default_price'] ?? '')) ?>">
          </td>
          <td>
              <button class="btn-save" type="submit" title="Save">
                <i class="fa-solid fa-floppy-disk"></i> Save
              </button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr><td colspan="6">No services found.</td></tr>
    <?php endif; ?>
    </tbody>
  </table>

</main>
</body>
</html>
