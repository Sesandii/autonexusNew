<?php $base = rtrim(BASE_URL, '/'); ?>
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
  <div class="main">
<h1>Info</h1>
  <div class="job-section">
<div class="job-card">
  <h2>Work Order Information</h2>
  <div class="info-row">
    <span class="label">Job Title:</span>
    <span><?= htmlspecialchars($job['service_summary']) ?></span>
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
  <div class="info-row"><span class="label">Date:</span> <span><?= $job['appointment_date'] ?></span></div>
  <div class="info-row"><span class="label">Time:</span> <span><?= $job['appointment_time'] ?></span></div>
</div>
<div class="job-card">
  <h2>Customer Information</h2>
  <div class="info-row"><span class="label">Customer:</span> <span><?= htmlspecialchars($job['first_name'] . " " . $job['last_name']) ?></span></div>
  <div class="info-row"><span class="label">Phone:</span> <span><?= htmlspecialchars($job['phone'] ?? '-') ?></span></div>
  <div class="info-row"><span class="label">Street Address:</span> <span><?= htmlspecialchars($job['street_address'] ?? '-') ?></span></div>
  <div class="info-row"><span class="label">City:</span> <span><?= htmlspecialchars($job['city'] ?? '-') ?></span></div>
  <div class="info-row"><span class="label">State:</span> <span><?= htmlspecialchars($job['state'] ?? '-') ?></span></div>
  <div class="info-row"><span class="label">Customer Code:</span> <span><?= htmlspecialchars($job['customer_code'] ?? '-') ?></span></div>
</div>
<div class="job-card">
  <h2>Vehicle Information</h2>
  <div class="info-row"><span class="label">Make:</span> <span><?= htmlspecialchars($job['make']) ?></span></div>
  <div class="info-row"><span class="label">Model:</span> <span><?= htmlspecialchars($job['model']) ?></span></div>
  <div class="info-row"><span class="label">Year:</span> <span><?= htmlspecialchars($job['year']) ?></span></div>
  <div class="info-row"><span class="label">License:</span> <span><?= htmlspecialchars($job['license_plate']) ?></span></div>
  <div class="info-row"><span class="label">Mileage:</span> <span><?= htmlspecialchars($job['mileage']) ?></span></div>
  <div class="info-row"><span class="label">Color:</span> <span><?= htmlspecialchars($job['color']) ?></span></div>
  <div class="info-row"><span class="label">VIN:</span> <span><?= htmlspecialchars($job['vin']) ?></span></div>
</div>
<div class="job-card">
    <h2>Service Summary</h2>
    <table>
      <thead>
        <tr><th>Service Task</th><th>Status</th><th>Notes</th></tr>
      </thead>
      <tbody>
        <?php if (!empty($services)): ?>
          <?php foreach ($services as $s): ?>
            <tr>
              <td><?= htmlspecialchars($s['item_name']) ?></td>
              <td class="statu <?= htmlspecialchars($s['status']) ?>"><?= ucfirst(htmlspecialchars($s['status'])) ?></td>
              <td><?= htmlspecialchars($s['remarks'] ?? '-') ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="3" style="text-align:center;">No service details available</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php if (!empty($photos)): ?>
  <div class="report-card">
    <h2>Work Photos</h2>
    <div class="photo-card">

      <?php foreach ($photos as $photo): ?>
        <img
                        src="/autonexus/public/assets/img/service_photos/<?= htmlspecialchars($photo['file_name']) ?>"
                        class="photo-img"
                        onclick="openModal(this.src)"
                    >
      <?php endforeach; ?>
    </div>
 
  </div>
  <?php endif; ?>
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
