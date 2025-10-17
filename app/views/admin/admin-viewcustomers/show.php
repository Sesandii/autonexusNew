<?php $current = 'customers'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Customer #<?= (int)$c['customer_id'] ?></title>
<link rel="stylesheet" href="../app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="../../app/views/layouts/admin-sidebar/styles.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>.sidebar{position:fixed;top:0;left:0;width:260px;height:100vh;overflow-y:auto}.main-content{margin-left:260px;padding:30px;background:#fff;min-height:100vh}.card{max-width:800px;border:1px solid #eee;border-radius:12px;padding:16px}</style>
</head>
<body>
<?php include(__DIR__ . '/../../layouts/admin-sidebar/sidebar.php'); ?>
<main class="main-content">
  <h2>Customer Details</h2>
  <div class="card">
    
    <p><strong>Code:</strong> <?= htmlspecialchars($c['customer_code']) ?></p>
    <p><strong>Name:</strong> <?= htmlspecialchars(($c['first_name'] ?? '') . ' ' . ($c['last_name'] ?? '')) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($c['email'] ?? '') ?></p>
    <p><strong>Phone:</strong> <?= htmlspecialchars($c['phone'] ?? '') ?></p>
    <p><strong>Status:</strong> <?= htmlspecialchars(ucfirst($c['status'] ?? 'active')) ?></p>
    <p><strong>Created:</strong> <?= htmlspecialchars($c['created_at']) ?></p>

    <p style="margin-top:14px">
  <a class="add-btn"
     href="<?= rtrim(BASE_URL, '/') ?>/admin/customers/<?= (int)$c['customer_id'] ?>/edit">
     <i class="fas fa-pen"></i> Edit
  </a>

  <a class="btn-secondary"
     href="<?= rtrim(BASE_URL, '/') ?>/admin/customers"
     style="margin-left:8px">
     Back
  </a>
</p>

  </div>
</main>
</body>
</html>
