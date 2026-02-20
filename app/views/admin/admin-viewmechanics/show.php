<?php $current = $current ?? 'mechanics'; $B = rtrim(BASE_URL, '/'); $m = $mechanic; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mechanic #<?= htmlspecialchars($m['mechanic_id']) ?></title>
  <link rel="stylesheet" href="../../app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="../../app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .main-content{margin-left:260px;padding:30px;background:#fff;min-height:100vh}
    .grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}
    .card{border:1px solid #eee;border-radius:10px;padding:16px}
    .label{font-weight:600;color:#555;margin-bottom:4px}
    .pill{display:inline-block;padding:6px 10px;border-radius:999px;font-size:12px}
    .pill.active{background:#16a34a22;color:#166534;border:1px solid #16a34a55}
    .pill.inactive{background:#ef444422;color:#7f1d1d;border:1px solid #ef444455}
    .btn-row{margin-top:16px;display:flex;gap:10px}
    .btn,button{border:none;background:#111;color:#fff;padding:10px 14px;border-radius:8px;cursor:pointer}
    .btn.secondary{background:#666}
    .btn.danger{background:#b91c1c}
  </style>
</head>
<body>
<?php include(__DIR__ . '/../../layouts/admin-sidebar/sidebar.php'); ?>


<main class="main-content">
  <h2>Mechanic Details</h2>

  <div class="card">
    <div class="grid">
      <div><div class="label">ID</div><div><?= htmlspecialchars($m['mechanic_id']) ?></div></div>
      <div><div class="label">Code</div><div><?= htmlspecialchars($m['mechanic_code'] ?? '—') ?></div></div>
      <div><div class="label">Name</div><div><?= htmlspecialchars(($m['first_name'] ?? '').' '.($m['last_name'] ?? '')) ?></div></div>
      <div><div class="label">Email</div><div><?= htmlspecialchars($m['email'] ?? '—') ?></div></div>
      <div><div class="label">Phone</div><div><?= htmlspecialchars($m['phone'] ?? '—') ?></div></div>
      <div>
  <div class="label">Branch</div>
  <div>
    <?php
      $code = $m['branch_code'] ?? null;
      $name = $m['branch_name'] ?? null;
      echo $name || $code
        ? '[' . htmlspecialchars($code ?? '-') . '] ' . htmlspecialchars($name ?? '-')
        : '—';
    ?>
  </div>
</div>

      <div><div class="label">Specialization</div><div><?= htmlspecialchars($m['specialization'] ?? '—') ?></div></div>
      <div><div class="label">Experience</div><div><?= htmlspecialchars($m['experience_years'] ?? 0) ?> yrs</div></div>
      <div>
        <div class="label">Mechanic Status</div>
        <div><span class="pill <?= ($m['mech_status']==='inactive')?'inactive':'active' ?>"><?= htmlspecialchars(ucfirst($m['mech_status'])) ?></span></div>
      </div>
      <div><div class="label">Created At</div><div><?= htmlspecialchars($m['created_at'] ?? '—') ?></div></div>
    </div>

    <div class="btn-row">
      <a class="btn secondary" href="<?= $B ?>/admin-viewmechanics"><i class="fa fa-arrow-left"></i> Back</a>
      <a class="btn" href="<?= $B ?>/admin/mechanics/<?= urlencode($m['mechanic_id']) ?>/edit"><i class="fa fa-pen"></i> Edit</a>
      <form action="<?= $B ?>/admin/mechanics/<?= urlencode($m['mechanic_id']) ?>/delete" method="post" onsubmit="return confirm('Delete this mechanic?');" style="display:inline-block;">
        <button class="btn danger" type="submit"><i class="fa fa-trash"></i> Delete</button>
      </form>
    </div>
  </div>
</main>
</body>
</html>
