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

<?php include __DIR__ . '/../partials/sidebar.php'; ?>

<main class="main-content">

<header>
  <h1>Job Report</h1>
</header>

<?php if (!empty($report) && !empty($workOrder)): ?>

<section class="report-grid">

  <!-- Job Inspection -->
  <div class="report-card">
    <h2>Job Inspection & Reporting</h2>
    <span class="status <?= htmlspecialchars($report['status']) ?>">
      <?= ucfirst(htmlspecialchars($report['status'])) ?>
    </span>
    <div class="info-grid">
      <div><p class="label">Job ID</p><p class="value"><?= htmlspecialchars($workOrder['work_order_id']) ?></p></div>
      <div><p class="label">Vehicle Number</p><p class="value"><?= htmlspecialchars($workOrder['license_plate'] ?? '-') ?></p></div>
      <div><p class="label">Customer</p><p class="value"><?= htmlspecialchars(($workOrder['customer_first_name'] ?? '') . ' ' . ($workOrder['customer_last_name'] ?? '-')) ?></p></div>
      <div><p class="label">Assigned Mechanic</p><p class="value"><?= htmlspecialchars($workOrder['mechanic_code'] ?? 'N/A') ?></p></div>
    </div>
  </div>

  <!-- Service Summary -->
  <div class="report-card">
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
              <td class="status <?= htmlspecialchars($s['status']) ?>"><?= ucfirst(htmlspecialchars($s['status'])) ?></td>
              <td><?= htmlspecialchars($s['remarks'] ?? '-') ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="3" style="text-align:center;">No service details available</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Final Inspection -->
  <div class="report-card">
    <h2>Final Inspection</h2>
    <p><strong>Inspection Notes:</strong></p>
    <p><?= nl2br(htmlspecialchars($report['inspection_notes'] ?? '-')) ?></p>
    <p><strong>Quality Rating:</strong> <?= (int)($report['quality_rating'] ?? 0) ?>/5 </p>
    <p><strong>Checklist Confirmation:</strong></p>
    <ul>
      <li><?= $report['checklist_verified'] ? ' Tasks verified' : ' Tasks not verified' ?></li>
      <li><?= $report['test_driven'] ? ' Vehicle test driven' : ' Not test driven' ?></li>
      <li><?= $report['concerns_addressed'] ? ' Customer concerns addressed' : ' Not addressed' ?></li>
    </ul>
  </div>

  <!-- Work Photos -->
  <?php if (!empty($photos)): ?>
  <div class="report-card">
    <h2>Work Photos</h2>
    <div class="photo-gallery">
      <?php foreach ($photos as $photo): ?>
        <img src="<?= $base ?>/public/<?= htmlspecialchars($photo['file_path']) ?>" alt="Report Photo"/>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <!-- Final Report -->
  <div class="report-card">
    <h2>Final Report</h2>
    <p><strong>Report Summary:</strong></p>
    <p><?= nl2br(htmlspecialchars($report['report_summary'] ?? '-')) ?></p>

    <p><strong>Next Service Recommendation:</strong></p>
    <p>
      <?php if (!empty($report['next_service_recommendation']) && $report['next_service_recommendation'] !== '0000-00-00'): ?>
        <?= date('d M Y', strtotime($report['next_service_recommendation'])) ?>
      <?php else: ?>
        <em>Not scheduled</em>
      <?php endif; ?>
    </p>
  </div>

</section>

<div class="actions">
  <a href="<?= $base ?>/supervisor/reports" class="btn secondary">Back to Reports</a>
</div>

<?php else: ?>
  <p style="text-align:center;">Report details not found.</p>
<?php endif; ?>

</main>
</body>
</html>
