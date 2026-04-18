<?php $current='supervisors'; ?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Supervisor <?= htmlspecialchars($s['supervisor_code']) ?></title>
<link rel="stylesheet" href="../../app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="../../app/views/layouts/admin-sidebar/styles.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>.sidebar{position:fixed;top:0;left:0;width:260px;height:100vh;overflow-y:auto}.main-content{margin-left:260px;padding:30px;background:#fff;min-height:100vh}.card{max-width:800px;border:1px solid #eee;border-radius:12px;padding:16px}</style>
</head><body>
<?php include(__DIR__ . '/../../layouts/admin-sidebar/sidebar.php'); ?>
<main class="main-content">
  <h2>Supervisor Details</h2>
  <div class="card">
    <p><strong>Code:</strong> <?= htmlspecialchars($s['supervisor_code']) ?></p>
    <p><strong>Name:</strong> <?= htmlspecialchars(($s['first_name']??'').' '.($s['last_name']??'')) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($s['email']??'') ?></p>
    <p><strong>Phone:</strong> <?= htmlspecialchars($s['phone']??'') ?></p>
    <p><strong>Status:</strong> <?= htmlspecialchars(ucfirst($s['status']??'active')) ?></p>
    <p><strong>Created:</strong> <?= htmlspecialchars($s['created_at']) ?></p>

    <p style="margin-top:14px">
      <a class="add-btn" href="<?= rtrim(BASE_URL,'/') ?>/admin/supervisors/<?= urlencode($s['supervisor_code']) ?>/edit"><i class="fas fa-pen"></i> Edit</a>
      <a class="btn-secondary" href="<?= rtrim(BASE_URL,'/') ?>/admin/supervisors" style="margin-left:8px">Back</a>
    </p>
  </div>
</main>
</body></html>
