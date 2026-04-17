<?php
/** @var array  $service */
/** @var array  $serviceTypes */
/** @var string $pageTitle */
/** @var string $current */

$current = $current ?? 'approval';
$B = rtrim(BASE_URL, '/');

$branches = $service['branches'] ?? [];
$submittedName = trim(($service['submitted_first'] ?? '') . ' ' . ($service['submitted_last'] ?? ''));

$message = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
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

    .flash {
      margin-bottom: 20px;
      padding: 12px 14px;
      border-radius: 10px;
      background: #ecfdf5;
      color: #047857;
      border: 1px solid #a7f3d0;
      font-size: 14px;
      font-weight: 600;
    }

    .grid-three {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
      margin-bottom: 20px;
    }

    .grid-two {
      display: grid;
      grid-template-columns: 2fr 1fr;
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

    .form-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
    }

    .form-group {
      display: flex;
      flex-direction: column;
      gap: 6px;
    }

    .form-group.full {
      grid-column: 1 / -1;
    }

    .form-label {
      font-size: 11px;
      font-weight: 700;
      color: #9ca3af;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .form-control {
      width: 100%;
      border: 1px solid #d1d5db;
      border-radius: 10px;
      padding: 11px 12px;
      font-size: 14px;
      color: #111827;
      background: #fff;
      outline: none;
    }

    .form-control:focus {
      border-color: #2563eb;
      box-shadow: 0 0 0 3px rgba(37, 99, 235, .10);
    }

    textarea.form-control {
      min-height: 130px;
      resize: vertical;
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

    .back-btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 10px 16px;
      background: #f3f4f6;
      color: #111827;
      border: 1px solid #e5e7eb;
      border-radius: 10px;
      text-decoration: none;
      font-size: 14px;
      font-weight: 600;
      transition: all 0.15s ease;
    }

    .back-btn:hover {
      background: #e5e7eb;
    }

    .action-form {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      padding: 12px 16px;
      border: none;
      border-radius: 10px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      text-decoration: none;
      width: 100%;
      transition: all .15s ease;
    }

    .btn-save {
      background: #2563eb;
      color: #fff;
    }

    .btn-save:hover {
      background: #1d4ed8;
    }

    .btn-approve {
      background: #16a34a;
      color: #fff;
    }

    .btn-approve:hover {
      background: #15803d;
    }

    .btn-reject {
      background: #dc2626;
      color: #fff;
    }

    .btn-reject:hover {
      background: #b91c1c;
    }

    .btn-view {
      background: #111827;
      color: #fff;
    }

    .btn-view:hover {
      background: #1f2937;
    }

    @media (max-width: 992px) {
      .main-content {
        margin-left: 0;
        padding: 16px;
      }

      .grid-three,
      .grid-two,
      .form-grid {
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
      <h1 class="page-title">Edit / Review Service</h1>
      <p class="subtitle"><?= htmlspecialchars($service['name']) ?> • Code: <?= htmlspecialchars($service['service_code']) ?></p>
    </div>

    <a href="<?= $B ?>/admin/admin-serviceapproval" class="back-btn">
      <i class="fa-solid fa-arrow-left"></i> Back to Queue
    </a>
  </header>

  <?php if (!empty($message)): ?>
    <div class="flash"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

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
      <div class="kpi-icon"><i class="fa-solid fa-sack-dollar"></i></div>
      <div class="kpi-label">Default Price</div>
      <div class="kpi-value">Rs. <?= number_format((float)$service['default_price'], 2) ?></div>
    </div>
  </div>

  <div class="grid-two">
    <div class="detail-card">
      <div class="card-header">
        <i class="fa-solid fa-pen-to-square"></i>
        <h3>Edit Service Details</h3>
      </div>
      <div class="card-body">
        <form method="post" action="<?= $B ?>/admin/admin-serviceapproval/update">
          <input type="hidden" name="id" value="<?= (int)$service['service_id'] ?>">
          <input type="hidden" name="action" value="save">

          <div class="form-grid">
            <div class="form-group full">
              <label class="form-label">Service Name</label>
              <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($service['name']) ?>" required>
            </div>

            <div class="form-group">
              <label class="form-label">Service Type</label>
              <select name="type_id" class="form-control" required>
                <option value="">Select service type</option>
                <?php foreach ($serviceTypes as $type): ?>
                  <option value="<?= (int)$type['type_id'] ?>" <?= (int)$service['type_id'] === (int)$type['type_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($type['type_name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label">Base Duration (Minutes)</label>
              <input type="number" name="base_duration_minutes" class="form-control" min="1" value="<?= (int)$service['base_duration_minutes'] ?>" required>
            </div>

            <div class="form-group">
              <label class="form-label">Default Price</label>
              <input type="number" name="default_price" class="form-control" min="0" step="0.01" value="<?= htmlspecialchars((string)$service['default_price']) ?>" required>
            </div>

            <div class="form-group full">
              <label class="form-label">Description</label>
              <textarea name="description" class="form-control"><?= htmlspecialchars($service['description'] ?? '') ?></textarea>
            </div>
          </div>

          <div style="margin-top:18px;">
            <button type="submit" class="btn btn-save">
              <i class="fa-solid fa-floppy-disk"></i> Save Changes
            </button>
          </div>
        </form>
      </div>
    </div>

    <div class="detail-card">
      <div class="card-header">
        <i class="fa-solid fa-list-check"></i>
        <h3>Approval Actions</h3>
      </div>
      <div class="card-body">
        <div style="margin-bottom:16px;">
          <div class="field-label">Current Status</div>
          <div class="field-value">Pending Review</div>
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

        <div style="padding:10px 0 14px;">
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

        <div class="action-form">
          <form method="post" action="<?= $B ?>/admin/admin-serviceapproval/update">
            <input type="hidden" name="id" value="<?= (int)$service['service_id'] ?>">
            <input type="hidden" name="action" value="approve">
            <button type="submit" class="btn btn-approve">
              <i class="fa-solid fa-check"></i> Approve Service
            </button>
          </form>

          <form method="post" action="<?= $B ?>/admin/admin-serviceapproval/update">
            <input type="hidden" name="id" value="<?= (int)$service['service_id'] ?>">
            <input type="hidden" name="action" value="reject">
            <button type="submit" class="btn btn-reject">
              <i class="fa-solid fa-xmark"></i> Reject Service
            </button>
          </form>

          <a href="<?= $B ?>/admin/admin-serviceapproval/show?id=<?= (int)$service['service_id'] ?>" class="btn btn-view">
            <i class="fa-regular fa-eye"></i> Open Detail View
          </a>
        </div>
      </div>
    </div>
  </div>
</main>
</body>
</html>