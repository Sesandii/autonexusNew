<?php $base = rtrim(BASE_URL, '/'); ?>
<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$message = $_SESSION['message'] ?? null;
unset($_SESSION['message']);

$logged_user_id = $_SESSION['user']['user_id'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Service Dashboard</title>
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/mechanic/style-view.css">
</head>
<body>
<?php include __DIR__ . '/../partials/sidebar.php'; ?>
<?php if (!empty($message)): ?>
    <div class="toast-container">
        <div class="toast-message <?= htmlspecialchars($message['type']) ?>">
            <?= htmlspecialchars($message['text']) ?>
        </div>
    </div>
<?php endif; ?>
  <div class="main">
  <div class="breadcrumb-text">
    Mechanic <span class="sep">&gt;</span> 
    Jobs <span class="sep">&gt;</span> 
    <?= htmlspecialchars($job['work_order_id']) ?> <span class="sep"></span> 
    <span class="active-page">View</span>
  </div>
<h1>Info</h1>
  <div class="job-section">
<div class="job-card">
  <h2>Work Order Information</h2>
  <div class="info-row">
    <span class="label">Job Title:</span>
    <span><?= htmlspecialchars($job['name']) ?></span>
  </div>
  <div class="info-row">
    <span class="label">Assigned by:</span>
    <span><?= htmlspecialchars(($job['supervisor_first_name'] ?? '-') . ' ' . ($job['supervisor_last_name'] ?? '')) ?></span>
  </div>
  <div class="info-row">
    <span class="label">Status:</span>
    <span class="status"><?= htmlspecialchars($job['status']) ?></span>
  </div>
  <?php if (!empty($can_edit) && $can_edit === true): ?>
    <form method="POST" action="<?= $base ?>/mechanic/jobs/update-status">
      <input type="hidden" name="work_order_id" value="<?= $job['work_order_id'] ?>">
      <div class="info-row">
        <span class="label">Change Status:</span>
        <select name="status" class="status-dropdown">
          <option value="open" <?= $job['status']==='open'?'selected':''?>>Open</option>
          <option value="in_progress" <?= $job['status']==='in_progress'?'selected':''?>>In Progress</option>
          <option value="on_hold" <?= $job['status']==='on_hold'?'selected':''?>>On Hold</option>
          <option value="completed" <?= $job['status']==='completed'?'selected':''?>>Completed</option>
        </select>
        <button class="btn btn-primary">Update</button>
      </div>
    </form>
  <?php endif; ?>
  <div class="info-row"><span class="label">Created:</span> <span><?= $job['started_at'] ?></span></div>
  <div class="info-row"><span class="label">Started:</span> <span><?= $job['job_start_time'] ?></span></div>
  <div class="info-row"><span class="label">Completed:</span> <span><?= $job['completed_at'] ?></span></div>
</div>
<div class="job-card">
  <h2>Customer Information</h2>
  <div class="info-row"><span class="label">Customer:</span> <span><?= htmlspecialchars($job['first_name'] . " " . $job['last_name']) ?></span></div>
  <div class="info-row"><span class="label">Phone:</span> <span><?= htmlspecialchars($job['phone'] ?? '-') ?></span></div>
  <div class="info-row"><span class="label">Street Address:</span> <span><?= htmlspecialchars($job['street_address'] ?? '-') ?></span></div>
  <div class="info-row"><span class="label">City:</span> <span><?= htmlspecialchars($job['city'] ?? '-') ?></span></div>
  <div class="info-row"><span class="label">State:</span> <span><?= htmlspecialchars($job['state'] ?? '-') ?></span></div>
  <div class="info-row"><span class="label">Appointment:</span> <span><?= htmlspecialchars(($job['appointment_date'] ?? '-') . ' ' . ($job['appointment_time'] ?? '')) ?></span></div>
</div>
<div class="job-card">
  <h2>Vehicle Information</h2>
  <div class="info-row"><span class="label">Make:</span> <span><?= htmlspecialchars($job['make']) ?></span></div>
  <div class="info-row"><span class="label">Model:</span> <span><?= htmlspecialchars($job['model']) ?></span></div>
  <div class="info-row"><span class="label">Year:</span> <span><?= htmlspecialchars($job['year']) ?></span></div>
  <div class="info-row"><span class="label">License:</span> <span><?= htmlspecialchars($job['license_plate']) ?></span></div>
  <div class="info-row"><span class="label">Mileage:</span> <span><?= htmlspecialchars($job['current_mileage']) ?></span></div>
  <div class="info-row"><span class="label">Color:</span> <span><?= htmlspecialchars($job['color']) ?></span></div>
  <div class="info-row"><span class="label">VIN:</span> <span><?= htmlspecialchars($job['vin']) ?></span></div>
</div>
<div class="job-card">
    <h2>Service Summary</h2>
    <table>
      <thead>
        <tr><th>Service Task</th><th>Status</th></tr>
      </thead>
      <tbody>
        <?php if (!empty($services)): ?>
          <?php foreach ($services as $s): ?>
            <tr>
              <td><?= htmlspecialchars($s['item_name']) ?></td>
              <td class="statu <?= htmlspecialchars($s['status']) ?>"><?= ucfirst(htmlspecialchars($s['status'])) ?></td>

            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="3" style="text-align:center;">No service details available</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php if (!empty($photos)): ?>
    <div class="job-card">
    <h2>Work Photos</h2>
    <div class="photo-card">
        <?php if (!empty($photos)): ?>
            <?php foreach ($photos as $photo): ?>
                <img
                    src="/autonexus/public/assets/img/service_photos/<?= htmlspecialchars($photo['file_name']) ?>"
                    class="photo-img"
                    alt="Service Photo"
                    onclick="openModal(this.src)"
                >
            <?php endforeach; ?>
        <?php else: ?>
            <p class="label" style="font-style: italic;">No photos uploaded for this job.</p>
        <?php endif; ?>
    </div>
</div>
 
  </div>
  <?php endif; ?>
  
 
  <div class="view-footer">
      <a href="<?= $base ?>/mechanic/jobs" class="btn-secondary">
          Back
      </a>

      </div>


<div id="imageModal" style="display:none;
     position:fixed; top:0; left:0; width:100%; height:100%;
     background:rgba(0,0,0,0.8);
     justify-content:center; align-items:center;
     z-index:9999;"
     onclick="closeModal()"
>
    <img id="modalImage" style="max-width:90%; max-height:90%; border-radius:10px;">
</div>

<script>
function openModal(src) {
    document.getElementById('modalImage').src = src;
    document.getElementById('imageModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('imageModal').style.display = 'none';
}
</script>
</body>
</html>
