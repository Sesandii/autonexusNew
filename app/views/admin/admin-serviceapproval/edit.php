<?php /* Admin view: renders admin-serviceapproval/edit page. */ ?>
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
  <link rel="stylesheet" href="<?= $B ?>/app/views/admin/admin-serviceapproval/service-approval.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
  <?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

  <main class="main-content">
    <header class="topbar">
      <div>
        <h1 class="page-title">Edit / Review Service</h1>
        <p class="subtitle"><?= htmlspecialchars($service['name']) ?> • Code:
          <?= htmlspecialchars($service['service_code']) ?>
        </p>
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
        <div class="kpi-value"><?= (int) $service['base_duration_minutes'] ?> min</div>
      </div>

      <div class="kpi-card">
        <div class="kpi-icon"><i class="fa-solid fa-sack-dollar"></i></div>
        <div class="kpi-label">Default Price</div>
        <div class="kpi-value">Rs. <?= number_format((float) $service['default_price'], 2) ?></div>
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
            <input type="hidden" name="id" value="<?= (int) $service['service_id'] ?>">
            <input type="hidden" name="action" value="save">

            <div class="form-grid">
              <div class="form-group full">
                <label class="form-label">Service Name</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($service['name']) ?>"
                  required>
              </div>

              <div class="form-group">
                <label class="form-label">Service Type</label>
                <select name="type_id" class="form-control" required>
                  <option value="">Select service type</option>
                  <?php foreach ($serviceTypes as $type): ?>
                    <option value="<?= (int) $type['type_id'] ?>" <?= (int) $service['type_id'] === (int) $type['type_id'] ? 'selected' : '' ?>>
                      <?= htmlspecialchars($type['type_name']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="form-group">
                <label class="form-label">Base Duration (Minutes)</label>
                <input type="number" name="base_duration_minutes" class="form-control" min="1"
                  value="<?= (int) $service['base_duration_minutes'] ?>" required>
              </div>

              <div class="form-group">
                <label class="form-label">Default Price</label>
                <input type="number" name="default_price" class="form-control" min="0" step="0.01"
                  value="<?= htmlspecialchars((string) $service['default_price']) ?>" required>
              </div>

              <div class="form-group full">
                <label class="form-label">Description</label>
                <textarea name="description"
                  class="form-control"><?= htmlspecialchars($service['description'] ?? '') ?></textarea>
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
                <span
                  style="font-size:12px;color:#6b7280;"><?= htmlspecialchars($service['created_at'] ?? '—') ?></span>
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
            <form method="post" action="<?= $B ?>/admin/admin-serviceapproval/update"
              onsubmit="return confirm('Approve this service request?');">
              <input type="hidden" name="id" value="<?= (int) $service['service_id'] ?>">
              <input type="hidden" name="action" value="approve">
              <button type="submit" class="btn btn-approve">
                <i class="fa-solid fa-check"></i> Approve Service
              </button>
            </form>

            <form method="post" action="<?= $B ?>/admin/admin-serviceapproval/update"
              onsubmit="return confirm('Reject this service request?');">
              <input type="hidden" name="id" value="<?= (int) $service['service_id'] ?>">
              <input type="hidden" name="action" value="reject">
              <button type="submit" class="btn btn-reject">
                <i class="fa-solid fa-xmark"></i> Reject Service
              </button>
            </form>

            <a href="<?= $B ?>/admin/admin-serviceapproval/show?id=<?= (int) $service['service_id'] ?>"
              class="btn btn-view">
              <i class="fa-regular fa-eye"></i> Open Detail View
            </a>
          </div>
        </div>
      </div>
    </div>
  </main>
</body>

</html>