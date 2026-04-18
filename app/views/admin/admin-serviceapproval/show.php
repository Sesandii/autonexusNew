<?php
/** @var array  $service */
/** @var string $pageTitle */
/** @var string $current */

$current = $current ?? 'approval';
$B = rtrim(BASE_URL, '/');

$branches = $service['branches'] ?? [];
$submittedName = trim(($service['submitted_first'] ?? '') . ' ' . ($service['submitted_last'] ?? ''));
$approvedName = trim(($service['approved_first'] ?? '') . ' ' . ($service['approved_last'] ?? ''));
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle) ?></title>

  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="<?= $B ?>/app/views/admin/admin-serviceapproval/service-approval.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">


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
        <p class="subtitle"><?= htmlspecialchars($service['name']) ?> • Code:
          <?= htmlspecialchars($service['service_code']) ?></p>
      </div>

      <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a href="<?= $B ?>/admin/admin-serviceapproval" class="back-btn">
          <i class="fa-solid fa-arrow-left"></i> Back to Queue
        </a>

        <a href="<?= $B ?>/admin/admin-serviceapproval/edit?id=<?= (int) $service['service_id'] ?>" class="action-btn">
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
        <div class="kpi-value"><?= (int) $service['base_duration_minutes'] ?> min</div>
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
              <div class="field-value">Rs. <?= number_format((float) $service['default_price'], 2) ?></div>
            </div>
          </div>

          <div class="field-row">
            <div class="field-icon"><i class="fa-solid fa-clock"></i></div>
            <div class="field-content">
              <div class="field-label">Base Duration</div>
              <div class="field-value"><?= (int) $service['base_duration_minutes'] ?> min</div>
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
                <span
                  style="font-size:12px;color:#6b7280;"><?= htmlspecialchars($service['created_at'] ?? '—') ?></span>
              </div>
            </div>
          </div>

          <div class="field-row">
            <div class="field-icon"><i class="fa-solid fa-user-check"></i></div>
            <div class="field-content">
              <div class="field-label">Approved By</div>
              <div class="field-value">
                <?= $approvedName !== '' ? htmlspecialchars($approvedName) : '—' ?><br>
                <span
                  style="font-size:12px;color:#6b7280;"><?= htmlspecialchars($service['approved_at'] ?? '—') ?></span>
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