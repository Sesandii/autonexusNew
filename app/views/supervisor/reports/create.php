<?php $base = rtrim(BASE_URL, '/'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Create Report | AutoNexus</title>
  <link rel="stylesheet" href="/autonexus/public/assets/css/supervisor/style-report.css"/>
</head>
<body>
<?php include __DIR__ . '/../partials/sidebar.php'; ?>

<main class="main-content">

<?php if (!empty($completedOrders)): ?>
  <!-- Step 1: Choose a completed work order -->
  <div class="form-section">
    <h2>Select Completed Work Order</h2>
    <form method="get" action="<?= $base ?>/supervisor/reports/create">
      <select name="id" required>
        <option value="">-- Select Work Order --</option>
        <?php foreach ($completedOrders as $wo): ?>
          <option value="<?= $wo['work_order_id'] ?>">
            <?= htmlspecialchars($wo['work_order_id']) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="btn primary">Select</button>
    </form>
  </div>

<?php elseif (!empty($workOrder)): ?>
  <!-- Step 2: Show report form for selected work order -->
  <form action="<?= $base ?>/supervisor/reports/store" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="work_order_id" value="<?= $workOrder['work_order_id'] ?>">
    <div class="grid-row two-columns">
  <!-- Job Inspection -->
  <div class="card">
    <div class="card-header">
      <h2>Job Inspection & Reporting</h2>
      <span class="status pending">Pending Inspection</span>
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
    <a href="#" class="job-log-link">View full job log</a>
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
    <?php foreach ($services as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item['item_name']) ?></td>
            <td class="status <?= htmlspecialchars($item['status']) ?>">
                <?= ucfirst(htmlspecialchars($item['status'])) ?>
            </td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr>
        <td colspan="3" style="text-align:center;">
            No checklist items found for this work order.
        </td>
    </tr>
<?php endif; ?>
</tbody>

    </table>
  </div>
</div>
</div>

<!-- Final Inspection -->
<div class="grid-row three-columns">
<div class="form-section">
  <h2>Final Inspection Form</h2>

  <label>Inspection Notes</label>
  <textarea name="inspection_notes" required></textarea>

  <label>Work Quality Rating</label>
<select name="quality_rating" required>
  <option value="">-- Select Rating --</option>
  <option value="1">1 Star</option>
  <option value="2">2 Stars</option>
  <option value="3">3 Stars</option>
  <option value="4">4 Stars</option>
  <option value="5">5 Stars</option>
</select>


  <div class="checklist">
    <strong>Checklist Confirmation</strong>
    <label><input type="checkbox" name="checklist[]" value="tasks_verified"> All service tasks verified</label>
    <label><input type="checkbox" name="checklist[]" value="test_driven"> Vehicle test driven</label>
    <label><input type="checkbox" name="checklist[]" value="concerns_addressed"> Customer concerns addressed</label>
  </div>
</div>

<!-- Attach Photo -->
<div class="form-section">
  <h2>Attach Work Photo</h2>
  <input type="file" name="work_images[]" multiple accept=".png,.jpg,.jpeg,.gif">
</div>

<!-- Final Report -->
<div class="form-section">
  <h2>Final Report</h2>

  <label>Report Summary</label>
  <textarea name="report_summary" required></textarea>

  <label>Next Service Recommendation</label>
  <input type="date" name="next_service_recommendation">
</div>
</div>
<!-- Actions -->
<div class="actions">
  <button type="submit" name="status" value="draft" class="btn secondary">Save as Draft</button>
  <button type="submit" name="status" value="submitted" class="btn primary">Submit Final Report</button>
</div>

</form>

<?php else: ?>
  <p>No completed work orders available.</p>
<?php endif; ?>

</main>

<script src="/autonexus/public/assets/js/supervisor/script-report.js"></script>
</body>
</html>
