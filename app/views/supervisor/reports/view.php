<?php $base = rtrim(BASE_URL, '/'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>View Report | AutoNexus</title>
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/supervisor/style-report.css"/>
</head>
<body>

<div class="sidebar">
  <div class="logo-container">
    <img src="<?= $base ?>/public/assets/img/Auto.png" alt="Logo" class="logo">
  </div>
  <h2>AUTONEXUS</h2>

  <a href="<?= $base ?>/supervisor/dashboard"><img src="<?= $base ?>/public/assets/img/dashboard.png"/>Dashboard</a>
  <a href="<?= $base ?>/supervisor/workorders"><img src="<?= $base ?>/public/assets/img/jobs.png"/>Work Orders</a>
  <a href="<?= $base ?>/supervisor/assignedjobs"><img src="<?= $base ?>/public/assets/img/assigned.png"/>Assigned</a>
  <a href="<?= $base ?>/supervisor/history"><img src="<?= $base ?>/public/assets/img/history.png"/>Vehicle History</a>
  <a href="<?= $base ?>/supervisor/complaints"><img src="<?= $base ?>/public/assets/img/Complaints.png"/>Complaints</a>
  <a href="<?= $base ?>/supervisor/feedbacks"><img src="<?= $base ?>/public/assets/img/Feedbacks.png"/>Feedbacks</a>
  <a href="<?= $base ?>/supervisor/reports" class="nav"><img src="<?= $base ?>/public/assets/img/Inspection.png"/>Reports</a>
</div>

<main class="main-content">

<header>
  <h1>Job Report</h1>
</header>

<?php if (!empty($report) && !empty($workOrder)): ?>

<!-- Job Inspection -->
<div class="card">
  <div class="card-header">
    <h2>Job Inspection & Reporting</h2>
    <span class="status <?= htmlspecialchars($report['status']) ?>">
      <?= ucfirst(htmlspecialchars($report['status'])) ?>
    </span>
  </div>

  <div class="card-content">
    <div class="info-grid">
      <div>
        <p class="label">Job ID</p>
        <p class="value"><?= htmlspecialchars($workOrder['work_order_id']) ?></p>
      </div>
      <div>
        <p class="label">Vehicle Number</p>
        <p class="value"><?= htmlspecialchars($workOrder['license_plate'] ?? '-') ?></p>
      </div>
      <div>
        <p class="label">Customer</p>
        <p class="value"><?= htmlspecialchars(($workOrder['customer_first_name'] ?? '') . ' ' . ($workOrder['customer_last_name'] ?? '-')) ?></p>
      </div>
      <div>
        <p class="label">Assigned Mechanic</p>
        <p class="value"><?= htmlspecialchars($workOrder['mechanic_code'] ?? 'N/A') ?></p>
      </div>
    </div>
  </div>
</div>

<!-- Service Summary -->
<div class="card">
  <div class="card-header">
    <h2>Service Summary</h2>
  </div>

  <div class="card-content">
    <table>
      <thead>
        <tr>
          <th>Service Task</th>
          <th>Status</th>
          <th>Notes</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($services)): ?>
          <?php foreach ($services as $s): ?>
            <tr>
              <td><?= htmlspecialchars($s['item_name']) ?></td>
              <td class="status <?= htmlspecialchars($s['status']) ?>">
                <?= ucfirst(htmlspecialchars($s['status'])) ?>
              </td>
              <td><?= htmlspecialchars($s['remarks'] ?? '-') ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="3" style="text-align:center;">No service details available</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Final Inspection -->
<div class="form-section">
  <h2>Final Inspection</h2>

  <p><strong>Inspection Notes:</strong></p>
  <p><?= nl2br(htmlspecialchars($report['inspection_notes'] ?? '-')) ?></p>

  <p><strong>Quality Rating:</strong></p>
  <p><?= (int)($report['quality_rating'] ?? 0) ?>/5 ⭐</p>

  <p><strong>Checklist Confirmation:</strong></p>
  <ul>
    <li><?= $report['checklist_verified'] ? '✔ Tasks verified' : '✖ Tasks not verified' ?></li>
    <li><?= $report['test_driven'] ? '✔ Vehicle test driven' : '✖ Not test driven' ?></li>
    <li><?= $report['concerns_addressed'] ? '✔ Customer concerns addressed' : '✖ Not addressed' ?></li>
  </ul>
</div>
<?php if (!empty($photos)): ?>
<div class="form-section">
  <h2>Work Photos</h2>
  <div class="photo-gallery" style="display:flex; flex-wrap:wrap;">
    <?php foreach ($photos as $photo): ?>
      <img 
    src="<?= $base ?>/public/<?= htmlspecialchars($photo['file_path']) ?>" 
    alt="Report Photo" 
    style="width:150px; margin:5px; border-radius:6px; box-shadow:0 1px 4px rgba(0,0,0,0.2);"
/>
   

    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>


<!-- Final Report -->
<div class="form-section">
  <h2>Final Report</h2>

  <p><strong>Report Summary:</strong></p>
  <p><?= nl2br(htmlspecialchars($report['report_summary'] ?? '-')) ?></p>

  <p><strong>Next Service Recommendation:</strong></p>
  <p>
  <?php if (!empty($report['next_service_recommendation']) && 
            $report['next_service_recommendation'] !== '0000-00-00'): ?>

    <?= date('d M Y', strtotime($report['next_service_recommendation'])) ?>

  <?php else: ?>
    <em>Not scheduled</em>
  <?php endif; ?>
</p>

</div>

<div class="actions">
  <a href="<?= $base ?>/supervisor/reports" class="btn secondary">Back to Reports</a>
</div>

<?php else: ?>
  <p style="text-align:center;">Report details not found.</p>
<?php endif; ?>

</main>
</body>
</html>
