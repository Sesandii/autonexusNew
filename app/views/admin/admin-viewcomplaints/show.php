<?php /* Admin view: renders admin-viewcomplaints/show page. */ ?>
<?php
$B = rtrim(BASE_URL, '/');
$current = $current ?? 'complaints';
$r = $record;
$timeline = $r['timeline'] ?? [];
include 'helpers.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle ?? 'Complaint Details') ?></title>
  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
  <!-- <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-shared/management.css"> -->
  <link rel="stylesheet" href="<?= $B ?>/app/views/admin/admin-viewcomplaints/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
  <?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

  <main class="main-content">
    <div class="page-header">
      <div class="breadcrumb">
        <a href="<?= $B ?>/admin/admin-viewcomplaints">Complaints</a>
        <span>›</span>
        <span>Complaint #<?= (int) $r['complaint_id'] ?></span>
      </div>
      <h1 class="page-title" style="margin:0;">Complaint #<?= (int) $r['complaint_id'] ?></h1>
<p class="muted" style="margin:6px 0 0;">View details, update handling, and manage resolution notes.</p>
    </div>

    <div class="grid-two">
      <section class="card">
        <h3>Complaint Information</h3>
        <div class="stats-grid">
          <div class="mini">
            <div class="small">Aging</div>
            <div class="value"><?= htmlspecialchars($r['aging_label']) ?></div>
          </div>
          <div class="mini">
            <div class="small">SLA</div>
            <div class="value"><?= slaStatus($r['sla_status']) ?></div>
          </div>
          <div class="mini">
            <div class="small">Escalation</div>
            <div class="value"><?= escalationFlag($r['escalated']) ?></div>
          </div>
          <div class="mini">
            <div class="small">Resolution Time</div>
            <div class="value"><?= htmlspecialchars($r['resolution_time_label']) ?></div>
          </div>
        </div>

        <?= fieldRow('Complaint ID', '#' . (int) $r['complaint_id']) ?>
        <?= fieldRow('Subject', $r['subject']) ?>
        <div class="field"><span class="label">Priority:</span><?= priority($r['priority']) ?></div>
        <div class="field"><span class="label">Status:</span><?= badge($r['status']) ?></div>
        <?= fieldRow('Created At', $r['created_at']) ?>
        <?= fieldRow('Updated At', $r['updated_at']) ?>
        <?= fieldRow('Resolved At', $r['resolved_at'] ?? '—') ?>

        <hr>
        <div class="field"><span class="label">Original / Full Description:</span></div>
        <div class="field mono"><?= htmlspecialchars($r['description']) ?></div>

        <hr>
        <h3>Customer & Related Info</h3>
        <?= fieldRow('Customer', $r['customer_name']) ?>
        <?= fieldRow('Customer Code', $r['customer_code'] ?? '—') ?>
        <?= fieldRow('Customer Email', $r['customer_email'] ?? '—') ?>
        <?= fieldRow('Customer Phone', $r['customer_phone'] ?? '—') ?>

        <hr>
        <?= fieldRow('Branch', $r['branch_name'] ?? '—') ?>
        <?= fieldRow('Branch Code', $r['branch_code'] ?? '—') ?>
        <?= fieldRow('Branch City', $r['branch_city'] ?? '—') ?>
        <?= fieldRow('Branch Phone', $r['branch_phone'] ?? '—') ?>

        <hr>
        <?= fieldRow('Appointment ID', $r['appointment_id'] ?? '—') ?>
        <?= fieldRow('Appointment Date', $r['appointment_date'] ?? '—') ?>
        <?= fieldRow('Appointment Time', $r['appointment_time'] ?? '—') ?>
        <?= fieldRow('Appointment Status', $r['appointment_status'] ?? '—') ?>
        <?= fieldRow('Service', $r['service_name'] ?? '—') ?>

        <hr>
        <?= fieldRow('Vehicle Code', $r['vehicle_code'] ?? '—') ?>
        <?= fieldRow('License Plate', $r['license_plate'] ?? '—') ?>
        <?= fieldRow('Vehicle', trim(($r['make'] ?? '') . ' ' . ($r['model'] ?? '')) ?: '—') ?>
        <?= fieldRow('Year', $r['year'] ?? '—') ?>
        <?= fieldRow('Color', $r['color'] ?? '—') ?>
      </section>

      <section class="card">
        <h3>Admin Handling</h3>
        <div class="field"><span class="label">Currently Assigned To:</span><?= assignedUserInfo($r) ?></div>

        <form method="POST" action="<?= $B ?>/admin/admin-viewcomplaints/update">
          <input type="hidden" name="complaint_id" value="<?= (int) $r['complaint_id'] ?>">

          <div class="field">
            <label class="label" style="display:block;margin-bottom:6px;">Status</label>
            <select name="status" required>
              <?php foreach (['open' => 'Open', 'in_progress' => 'In Progress', 'resolved' => 'Resolved', 'closed' => 'Closed'] as $value => $label): ?>
                <option value="<?= $value ?>" <?= (($r['status'] ?? '') === $value) ? 'selected' : '' ?>><?= $label ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="field">
            <label class="label" style="display:block;margin-bottom:6px;">Priority</label>
            <select name="priority" required>
              <?php foreach (['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'] as $value => $label): ?>
                <option value="<?= $value ?>" <?= (($r['priority'] ?? '') === $value) ? 'selected' : '' ?>><?= $label ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="field">
            <label class="label" style="display:block;margin-bottom:6px;">Assign To</label>
            <select name="assigned_to_user_id">
              <option value="">Unassigned</option>
              <?php foreach ($assignableUsers as $u): ?>
                <option value="<?= (int) $u['user_id'] ?>" <?= ((string) ($r['assigned_to_user_id'] ?? '') === (string) $u['user_id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($u['full_name']) ?> (<?= htmlspecialchars($u['role']) ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="field">
            <label class="label" style="display:block;margin-bottom:6px;">Admin / Resolution Note</label>
            <textarea name="resolution_note" rows="6"
              placeholder="Add action taken, escalation reason, follow-up, or resolution note..."></textarea>
          </div>

          <div class="actions">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save Changes</button>
            <?php if (!empty($r['can_reopen'])): ?>
              <button type="submit" name="reopen" value="1" class="btn btn-warning"><i
                  class="fa-solid fa-rotate-left"></i> Reopen Complaint</button>
            <?php endif; ?>
            <a href="<?= $B ?>/admin/admin-viewcomplaints" class="btn btn-secondary">Back to Complaints</a>
          </div>
        </form>

        <hr style="margin:20px 0;">
        <h3>Resolution Notes / History Timeline</h3>

        <?php if (!empty($timeline)): ?>
          <div class="timeline">
            <?php foreach ($timeline as $item): ?>
              <div class="timeline-item">
                <div><strong><?= htmlspecialchars($item['type']) ?></strong></div>
                <div class="muted"><?= htmlspecialchars($item['stamp']) ?></div>
                <div style="margin-top:6px;white-space:pre-line;"><?= htmlspecialchars($item['message']) ?></div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p class="muted">No admin timeline entries yet.</p>
        <?php endif; ?>
      </section>
    </div>
  </main>
</body>

</html>