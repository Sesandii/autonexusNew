<?php
/** @var array  $service */
/** @var string $pageTitle */
/** @var string $current */

$current = $current ?? 'approval';
$B = rtrim(BASE_URL, '/');

$branches = $service['branches'] ?? [];
$submittedName = trim(($service['submitted_first'] ?? '') . ' ' . ($service['submitted_last'] ?? ''));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle) ?></title>

  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    .main-content{margin-left:260px;padding:30px;background:#f4f5f7;min-height:100vh;}
    .card{background:#fff;border-radius:16px;padding:20px;box-shadow:0 1px 4px rgba(15,23,42,.08);max-width:720px;}
    .label{font-weight:600;color:#4b5563;font-size:13px;}
    .value{font-size:14px;margin-bottom:8px;}
    .chips{display:flex;flex-wrap:wrap;gap:6px;margin-top:4px;margin-bottom:8px;}
    .chip{background:#eef2ff;color:#3730a3;font-size:12px;padding:3px 8px;border-radius:999px;}
    .btn{border:none;border-radius:8px;padding:8px 14px;font-size:14px;cursor:pointer;display:inline-flex;align-items:center;gap:6px;}
    .btn-back{background:#6b7280;color:#fff;text-decoration:none;}
    .btn-approve{background:#16a34a;color:#fff;}
    .btn-reject{background:#b91c1c;color:#fff;}
  </style>
</head>
<body>
<?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

<main class="main-content">
  <a href="<?= $B ?>/admin/admin-serviceapproval" class="btn btn-back" style="margin-bottom:16px;">
    <i class="fa-solid fa-arrow-left"></i> Back to queue
  </a>

  <div class="card">
    <h2 style="margin-top:0;">
      Review Service: <?= htmlspecialchars($service['name']) ?>
      <span style="font-size:12px;color:#6b7280;">(Code: <?= htmlspecialchars($service['service_code']) ?>)</span>
    </h2>

    <p class="label">Service Type</p>
    <p class="value"><?= htmlspecialchars($service['type_name'] ?? '—') ?></p>

    <p class="label">Branches</p>
    <div class="chips">
      <?php if (empty($branches)): ?>
        <span style="font-size:13px;color:#6b7280;">No branches linked yet</span>
      <?php else: ?>
        <?php foreach ($branches as $bName): ?>
          <span class="chip"><?= htmlspecialchars($bName) ?></span>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <p class="label">Base Duration</p>
    <p class="value"><?= (int)$service['base_duration_minutes'] ?> min</p>

    <p class="label">Default Price</p>
    <p class="value"><?= number_format((float)$service['default_price'], 2) ?></p>

    <p class="label">Description</p>
    <p class="value"><?= nl2br(htmlspecialchars($service['description'] ?? '')) ?></p>

    <hr style="margin:16px 0;">

    <p class="label">Submitted By</p>
    <p class="value">
      <?= $submittedName !== '' ? htmlspecialchars($submittedName) : '—' ?>
      <?php if (!empty($service['created_at'])): ?>
        <br><span style="font-size:12px;color:#6b7280;"><?= htmlspecialchars($service['created_at']) ?></span>
      <?php endif; ?>
    </p>

    <div style="margin-top:18px;display:flex;gap:10px;">
      <form method="post" action="<?= $B ?>/admin/admin-serviceapproval/update">
        <input type="hidden" name="id" value="<?= (int)$service['service_id'] ?>">
        <input type="hidden" name="action" value="approve">
        <button type="submit" class="btn btn-approve">
          <i class="fa-solid fa-check"></i> Approve
        </button>
      </form>

      <form method="post" action="<?= $B ?>/admin/admin-serviceapproval/update">
        <input type="hidden" name="id" value="<?= (int)$service['service_id'] ?>">
        <input type="hidden" name="action" value="reject">
        <button type="submit" class="btn btn-reject">
          <i class="fa-solid fa-xmark"></i> Reject
        </button>
      </form>
    </div>
  </div>
</main>
</body>
</html>
