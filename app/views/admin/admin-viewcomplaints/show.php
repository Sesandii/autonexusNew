<?php
/** @var array $record */
/** @var array $assignableUsers */
/** @var string $pageTitle */
/** @var string $current */

$B = rtrim(BASE_URL, '/');
$current = $current ?? 'complaints';
$r = $record;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle ?? 'Complaint Details') ?></title>

  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    .main-content{margin-left:280px;padding:30px;background:#f4f5f7;min-height:100vh;}
    .page-header{margin-bottom:18px;}
    .breadcrumb{font-size:14px;color:#6b7280;margin-bottom:10px;}
    .breadcrumb a{text-decoration:none;color:#2563eb;}
    .grid-two{display:grid;grid-template-columns:1.2fr .9fr;gap:18px;}
    .card{background:#fff;border-radius:16px;box-shadow:0 1px 4px rgba(15,23,42,.08);padding:18px;}
    .card h3{margin-top:0;margin-bottom:14px;}
    .field{margin-bottom:10px;font-size:14px;}
    .label{font-weight:700;color:#374151;margin-right:6px;}
    .muted{color:#6b7280;}
    .badge{display:inline-block;padding:4px 10px;border-radius:999px;font-size:12px;font-weight:700;}
    .badge.open{background:#fef2f2;color:#b91c1c;}
    .badge.in_progress{background:#fff7ed;color:#c2410c;}
    .badge.resolved{background:#ecfdf5;color:#047857;}
    .badge.closed{background:#eef2ff;color:#3730a3;}
    .priority.low{color:#2563eb;font-weight:700;}
    .priority.medium{color:#d97706;font-weight:700;}
    .priority.high{color:#dc2626;font-weight:700;}
    .field input,.field select,.field textarea{width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:10px;font-size:14px;background:#fff;}
    .btn{padding:10px 14px;border:none;border-radius:10px;cursor:pointer;font-size:14px;text-decoration:none;display:inline-flex;align-items:center;gap:8px;}
    .btn-primary{background:#111827;color:#fff;}
    .btn-secondary{background:#e5e7eb;color:#111827;}
    .actions{margin-top:18px;display:flex;gap:10px;flex-wrap:wrap;}
    @media (max-width: 1200px){
      .grid-two{grid-template-columns:1fr;}
      .main-content{margin-left:0;padding:20px;}
    }
  </style>
</head>
<body>
<?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

<main class="main-content">
  <div class="page-header">
    <div class="breadcrumb">
      <a href="<?= $B ?>/admin/admin-viewcomplaints">Complaints</a>
      <span>›</span>
      <span>Complaint #<?= (int)$r['complaint_id'] ?></span>
    </div>
    <h1 style="margin:0;">Complaint Details</h1>
  </div>

  <div class="grid-two">
    <section class="card">
      <h3>Complaint Information</h3>

      <div class="field"><span class="label">Complaint ID:</span>#<?= (int)$r['complaint_id'] ?></div>
      <div class="field"><span class="label">Subject:</span><?= htmlspecialchars($r['subject']) ?></div>
      <div class="field">
        <span class="label">Priority:</span>
        <span class="priority <?= htmlspecialchars($r['priority']) ?>"><?= htmlspecialchars(ucwords($r['priority'])) ?></span>
      </div>
      <div class="field">
        <span class="label">Status:</span>
        <span class="badge <?= htmlspecialchars($r['status']) ?>"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $r['status']))) ?></span>
      </div>
      <div class="field"><span class="label">Created At:</span><?= htmlspecialchars($r['created_at']) ?></div>
      <div class="field"><span class="label">Updated At:</span><?= htmlspecialchars($r['updated_at']) ?></div>
      <div class="field"><span class="label">Resolved At:</span><?= htmlspecialchars($r['resolved_at'] ?? '—') ?></div>
      <hr>

      <div class="field"><span class="label">Description:</span></div>
      <div class="field" style="white-space:pre-line;line-height:1.6;"><?= htmlspecialchars($r['description']) ?></div>

      <hr>
      <h3>Customer & Related Info</h3>

      <div class="field"><span class="label">Customer:</span><?= htmlspecialchars($r['customer_name']) ?></div>
      <div class="field"><span class="label">Customer Code:</span><?= htmlspecialchars($r['customer_code'] ?? '—') ?></div>
      <div class="field"><span class="label">Customer Email:</span><?= htmlspecialchars($r['customer_email'] ?? '—') ?></div>
      <div class="field"><span class="label">Customer Phone:</span><?= htmlspecialchars($r['customer_phone'] ?? '—') ?></div>

      <hr>
      <div class="field"><span class="label">Branch:</span><?= htmlspecialchars($r['branch_name'] ?? '—') ?></div>
      <div class="field"><span class="label">Branch Code:</span><?= htmlspecialchars($r['branch_code'] ?? '—') ?></div>
      <div class="field"><span class="label">Branch City:</span><?= htmlspecialchars($r['branch_city'] ?? '—') ?></div>
      <div class="field"><span class="label">Branch Phone:</span><?= htmlspecialchars($r['branch_phone'] ?? '—') ?></div>

      <hr>
      <div class="field"><span class="label">Appointment ID:</span><?= htmlspecialchars($r['appointment_id'] ?? '—') ?></div>
      <div class="field"><span class="label">Appointment Date:</span><?= htmlspecialchars($r['appointment_date'] ?? '—') ?></div>
      <div class="field"><span class="label">Appointment Time:</span><?= htmlspecialchars($r['appointment_time'] ?? '—') ?></div>
      <div class="field"><span class="label">Appointment Status:</span><?= htmlspecialchars($r['appointment_status'] ?? '—') ?></div>

      <hr>
      <div class="field"><span class="label">Vehicle Code:</span><?= htmlspecialchars($r['vehicle_code'] ?? '—') ?></div>
      <div class="field"><span class="label">License Plate:</span><?= htmlspecialchars($r['license_plate'] ?? '—') ?></div>
      <div class="field"><span class="label">Vehicle:</span><?= htmlspecialchars(trim(($r['make'] ?? '') . ' ' . ($r['model'] ?? '')) ?: '—') ?></div>
      <div class="field"><span class="label">Year:</span><?= htmlspecialchars($r['year'] ?? '—') ?></div>
      <div class="field"><span class="label">Color:</span><?= htmlspecialchars($r['color'] ?? '—') ?></div>
    </section>

    <section class="card">
      <h3>Admin Handling</h3>

      <div class="field">
        <span class="label">Currently Assigned To:</span>
        <?= htmlspecialchars($r['assigned_user_name'] ?? 'Unassigned') ?>
        <?php if (!empty($r['assigned_user_role'])): ?>
          <span class="muted">(<?= htmlspecialchars($r['assigned_user_role']) ?>)</span>
        <?php endif; ?>
      </div>

      <form method="POST" action="<?= $B ?>/admin/admin-viewcomplaints/update">
        <input type="hidden" name="complaint_id" value="<?= (int)$r['complaint_id'] ?>">

        <div class="field">
          <label class="label" style="display:block;margin-bottom:6px;">Status</label>
          <select name="status" required>
            <?php foreach (['open' => 'Open', 'in_progress' => 'In Progress', 'resolved' => 'Resolved', 'closed' => 'Closed'] as $value => $label): ?>
              <option value="<?= $value ?>" <?= (($r['status'] ?? '') === $value) ? 'selected' : '' ?>><?= $label ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="field">
          <label class="label" style="display:block;margin-bottom:6px;">Priority</label>
          <select name="priority" required>
            <?php foreach (['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'] as $value => $label): ?>
              <option value="<?= $value ?>" <?= (($r['priority'] ?? '') === $value) ? 'selected' : '' ?>><?= $label ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="field">
          <label class="label" style="display:block;margin-bottom:6px;">Assign To</label>
          <select name="assigned_to_user_id">
            <option value="">Unassigned</option>
            <?php foreach ($assignableUsers as $u): ?>
              <option value="<?= (int)$u['user_id'] ?>" <?= ((string)($r['assigned_to_user_id'] ?? '') === (string)$u['user_id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($u['full_name']) ?> (<?= htmlspecialchars($u['role']) ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="field">
          <label class="label" style="display:block;margin-bottom:6px;">Admin Note / Resolution Note</label>
          <textarea name="resolution_note" rows="6" placeholder="Add follow-up note, action taken, or resolution note..."></textarea>
        </div>

        <div class="actions">
          <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-floppy-disk"></i> Save Changes
          </button>

          <a href="<?= $B ?>/admin/admin-viewcomplaints" class="btn btn-secondary">
            Back to Complaints
          </a>
        </div>
      </form>
    </section>
  </div>
</main>
</body>
</html>