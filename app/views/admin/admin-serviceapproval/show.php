<?php
/** @var array  $service */
/** @var string $pageTitle */
/** @var string $current */

$current = $current ?? 'approval';
$B = rtrim(BASE_URL, '/');

$branches = $service['branches'] ?? [];
$submittedName = trim(($service['submitted_first'] ?? '') . ' ' . ($service['submitted_last'] ?? ''));
$approvedName  = trim(($service['approved_first'] ?? '') . ' ' . ($service['approved_last'] ?? ''));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle) ?></title>

  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    .main-content {
      min-height: 100vh;
      padding: 24px;
      background: #f8fafc;
      margin-left: 260px;
    }

    .topbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      margin-bottom: 24px;
      flex-wrap: wrap;
    }

    .page-title {
      font-size: 28px;
      font-weight: 700;
      margin: 10px 0 2px;
      color: #111827;
    }

    .subtitle {
      color: #6b7280;
      margin: 0 0 8px;
      font-size: 14px;
    }

    .grid-three {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
      margin-bottom: 20px;
    }

    .grid-two {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      margin-bottom: 20px;
    }

    .detail-card {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 2px 8px rgba(15, 23, 42, .06);
      overflow: hidden;
    }

    .card-header {
      background: #f9fafb;
      padding: 16px;
      border-bottom: 1px solid #e5e7eb;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .card-header h3 {
      margin: 0;
      font-size: 15px;
      font-weight: 700;
      color: #111827;
    }

    .card-header i {
      font-size: 18px;
      color: #6b7280;
    }

    .card-body {
      padding: 16px;
    }

    .kpi-card {
      background: #fff;
      border-radius: 16px;
      padding: 16px;
      box-shadow: 0 2px 8px rgba(15, 23, 42, .06);
      text-align: center;
    }

    .kpi-icon {
      height: 48px;
      width: 48px;
      border-radius: 12px;
      display: grid;
      place-items: center;
      background: #f3f4f6;
      margin: 0 auto 10px;
    }

    .kpi-icon i {
      font-size: 22px;
      color: #2563eb;
    }

    .kpi-label {
      font-size: 12px;
      color: #6b7280;
      font-weight: 600;
      margin-bottom: 6px;
      text-transform: uppercase;
      letter-spacing: .5px;
    }

    .kpi-value {
      font-size: 24px;
      font-weight: 700;
      color: #111827;
    }

    .status-badge {
      display: inline-block;
      padding: 4px 10px;
      border-radius: 999px;
      font-size: 11px;
      font-weight: 700;
      background: #fff7ed;
      color: #c2410c;
    }

    .chips {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
    }

    .chip {
      background: #eff6ff;
      color: #1d4ed8;
      border-radius: 999px;
      padding: 6px 10px;
      font-size: 12px;
      font-weight: 600;
    }

    .field-row {
      display: flex;
      gap: 12px;
      padding: 10px 0;
      border-bottom: 1px solid #f3f4f6;
    }

    .field-row:last-child {
      border-bottom: none;
    }

    .field-icon {
      color: #6b7280;
      width: 24px;
      text-align: center;
    }

    .field-content {
      flex: 1;
    }

    .field-label {
      font-size: 11px;
      font-weight: 700;
      color: #9ca3af;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 4px;
    }

    .field-value {
      font-size: 14px;
      color: #111827;
      line-height: 1.6;
    }

    .summary-box {
      background: #f8fafc;
      border-left: 4px solid #2563eb;
      padding: 12px 14px;
      border-radius: 8px;
      font-size: 13px;
      line-height: 1.6;
      color: #374151;
    }

    .back-btn,
    .action-btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 10px 16px;
      border-radius: 10px;
      text-decoration: none;
      font-size: 14px;
      font-weight: 600;
      transition: all .15s ease;
    }

    .back-btn {
      background: #f3f4f6;
      color: #111827;
      border: 1px solid #e5e7eb;
    }

    .back-btn:hover {
      background: #e5e7eb;
    }

    .action-btn {
      background: #2563eb;
      color: #fff;
      border: 1px solid #2563eb;
    }

    .action-btn:hover {
      background: #1d4ed8;
    }

    @media (max-width: 992px) {
      .main-content {
        margin-left: 0;
        padding: 16px;
      }

      .grid-three,
      .grid-two {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
<?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

<main class="main-content">
  <header class="topbar">
    <div>
      <h1 class="page-title">Service Details</h1>
      <p class="subtitle"><?= htmlspecialchars($service['name']) ?> • Code: <?= htmlspecialchars($service['service_code']) ?></p>
    </div>

    <div style="display:flex; gap:10px; flex-wrap:wrap;">
      <a href="<?= $B ?>/admin/admin-serviceapproval" class="back-btn">
        <i class="fa-solid fa-arrow-left"></i> Back to Queue
      </a>

      <a href="<?= $B ?>/admin/admin-serviceapproval/edit?id=<?= (int)$service['service_id'] ?>" class="action-btn">
        <i class="fa-solid fa-pen-to-square"></i> Review / Approve
      </a>
    </div>
  </header>

  <div class="grid-three">
    <div class="kpi-card">
      <div class="kpi-icon"><i class="fa-solid fa-tag"></i></div>
      <div class="kpi-label">Service Type</div>
      <div class="kpi-value"><?= htmlspecialchars($service['type_name'] ?? '—') ?></div>
    </div>

    <div class="kpi-card">
      <div class="kpi-icon"><i class="fa-solid fa-clock"></i></div>
      <div class="kpi-label">Duration</div>
      <div class="kpi-value"><?= (int)$service['base_duration_minutes'] ?> min</div>
    </div>

    <div class="kpi-card">
      <div class="kpi-icon"><i class="fa-solid fa-circle-check"></i></div>
      <div class="kpi-label">Status</div>
      <div style="margin-top:8px;"><span class="status-badge"><?= htmlspecialchars($service['status']) ?></span></div>
    </div>
  </div>

  <div class="grid-two">
    <div class="detail-card">
      <div class="card-header">
        <i class="fa-solid fa-wrench"></i>
        <h3>Service Information</h3>
      </div>
      <div class="card-body">
        <div class="field-row">
          <div class="field-icon"><i class="fa-solid fa-tools"></i></div>
          <div class="field-content">
            <div class="field-label">Service Name</div>
            <div class="field-value"><?= htmlspecialchars($service['name']) ?></div>
          </div>
        </div>

        <div class="field-row">
          <div class="field-icon"><i class="fa-solid fa-barcode"></i></div>
          <div class="field-content">
            <div class="field-label">Service Code</div>
            <div class="field-value"><?= htmlspecialchars($service['service_code']) ?></div>
          </div>
        </div>

        <div class="field-row">
          <div class="field-icon"><i class="fa-solid fa-tag"></i></div>
          <div class="field-content">
            <div class="field-label">Type</div>
            <div class="field-value"><?= htmlspecialchars($service['type_name'] ?? '—') ?></div>
          </div>
        </div>

        <div class="field-row">
          <div class="field-icon"><i class="fa-solid fa-sack-dollar"></i></div>
          <div class="field-content">
            <div class="field-label">Default Price</div>
            <div class="field-value">Rs. <?= number_format((float)$service['default_price'], 2) ?></div>
          </div>
        </div>

        <div class="field-row">
          <div class="field-icon"><i class="fa-solid fa-clock"></i></div>
          <div class="field-content">
            <div class="field-label">Base Duration</div>
            <div class="field-value"><?= (int)$service['base_duration_minutes'] ?> min</div>
          </div>
        </div>
      </div>
    </div>

    <div class="detail-card">
      <div class="card-header">
        <i class="fa-solid fa-code-branch"></i>
        <h3>Branches & Approval</h3>
      </div>
      <div class="card-body">
        <div style="margin-bottom:16px;">
          <div class="field-label">Branches</div>
          <?php if (empty($branches)): ?>
            <div class="field-value">No branches linked yet</div>
          <?php else: ?>
            <div class="chips">
              <?php foreach ($branches as $bName): ?>
                <span class="chip"><?= htmlspecialchars($bName) ?></span>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>

        <div class="field-row">
          <div class="field-icon"><i class="fa-solid fa-user"></i></div>
          <div class="field-content">
            <div class="field-label">Submitted By</div>
            <div class="field-value">
              <?= $submittedName !== '' ? htmlspecialchars($submittedName) : '—' ?><br>
              <span style="font-size:12px;color:#6b7280;"><?= htmlspecialchars($service['created_at'] ?? '—') ?></span>
            </div>
          </div>
        </div>

        <div class="field-row">
          <div class="field-icon"><i class="fa-solid fa-user-check"></i></div>
          <div class="field-content">
            <div class="field-label">Approved By</div>
            <div class="field-value">
              <?= $approvedName !== '' ? htmlspecialchars($approvedName) : '—' ?><br>
              <span style="font-size:12px;color:#6b7280;"><?= htmlspecialchars($service['approved_at'] ?? '—') ?></span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="detail-card">
    <div class="card-header">
      <i class="fa-solid fa-note-sticky"></i>
      <h3>Description</h3>
    </div>
    <div class="card-body">
      <div class="summary-box">
        <?= !empty($service['description']) ? nl2br(htmlspecialchars($service['description'])) : 'No description provided.' ?>
      </div>
    </div>
  </div>
</main>
</body>
</html>