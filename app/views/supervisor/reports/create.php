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
<div class="breadcrumb-text">
    Supervisor <span class="sep">&gt;</span> 
    Reports <span class="sep">&gt;</span> 
    Vehicle Report <span class="sep"></span> 
    <span class="active-page">Create</span>
  </div>

<?php if (!empty($completedOrders)): ?>
  <!-- Step 1: Choose a completed work order -->
  <div class="form-section">
  <h2>Select Completed Work Order</h2>
  <form method="get" action="<?= $base ?>/supervisor/reports/create">
    <select name="id" required>
      <option value="">-- Select Work Order --</option>
      <?php foreach ($completedOrders as $wo): ?>
        <option value="<?= $wo['work_order_id'] ?>">
          <?= htmlspecialchars($wo['vehicle_number']) ?> (<?= htmlspecialchars($wo['customer_first_name'] ?? 'N/A') ?>)
        </option>
      <?php endforeach; ?>
    </select>
    
    <div class="form-actions" style="margin-top: 15px; display: flex; gap: 10px;">
      <button type="submit" class="btn primary">Select</button>
      
      <a href="<?= $base ?>/supervisor/reports/indexp" class="btn secondary" style="text-decoration: none; display: inline-block; text-align: center;">Back</a>
    </div>
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
          <p class="label">Service</p>
          <p class="value"><?= htmlspecialchars($workOrder['service_name']) ?></p>
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
  <div class="upload-container">
    <label for="image-upload" class="btn secondary" style="cursor: pointer; display: inline-block;">
       Upload Photo
    </label>
    <input type="file" id="image-upload" name="work_images[]" multiple accept="image/*" style="display: none;">
    
    <div id="preview-grid" class="preview-grid"></div>
  </div>
</div>

<!-- Final Report -->
<div class="form-section">
    <h2>Final Report & Mileage</h2>

    <label>Report Summary</label>
    <textarea name="report_summary" placeholder="Overall condition of the vehicle..." required></textarea>

    <div style="margin-top: 15px; padding: 15px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
        <div style="margin-bottom: 15px;">
            <label class="required">Current Vehicle Mileage (km)</label>
            <input type="number" name="current_mileage" id="current_mileage" 
                   placeholder="Enter odometer reading" required 
                   style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #cbd5e1;">
            <small style="color: #64748b;">Last recorded: <b><?= htmlspecialchars($workOrder['last_service_mileage'] ?? '0') ?> km</b></small>
        </div>

        <div style="margin-bottom: 15px;">
            <label class="required">Service Interval (km)</label>
            <input type="number" name="service_interval" id="service_interval" value="5000" 
                   style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #cbd5e1;">
            <small style="color: #64748b;">Standard is 5000km. Adjust if necessary.</small>
        </div>

        <div>
            <label style="color: var(--primary); font-weight: bold;">Calculated Next Service Due</label>
            <div id="next_service_display" style="font-size: 1.2rem; font-weight: 800; color: #1e293b; padding: 10px; background: #fff; border: 2px dashed #cbd5e1; border-radius: 6px; text-align: center;">
                - km
            </div>
            <input type="hidden" name="next_service_due" id="next_service_due_val">
        </div>
    </div>
</div>
</div>
<!-- Actions -->
<div class="actions">
    <button type="button" onclick="history.back()" class="btn secondary">
        Cancel & Go Back
    </button>

    <button type="submit" name="status" value="draft" class="btn btn-draft">
        Save as Draft
    </button>

    <button type="submit" name="status" value="submitted" class="btn primary">
        Submit Final Report
    </button>
</div>
</form>

<?php else: ?>
  <p>No completed work orders available.</p>
  <button onclick="history.back()" class="btn secondary">Back</button>
<?php endif; ?>

<div id="imageModal" class="image-modal">
  <span class="close-modal">&times;</span>
  <img class="modal-content" id="modalImg">
</div>

<div id="deleteModal" class="modal-overlay" style="display: none;">
  <div class="modal-box">
    <h3>Confirm Deletion</h3>
    <p>Are you sure you want to delete this photo?</p>
    <div class="modal-actions">
      <button id="cancelDelete" class="btn secondary">Cancel</button>
      <button id="confirmDelete" class="btn danger">Delete</button>
    </div>
  </div>
</div>
</main>

<script src="/autonexus/public/assets/js/supervisor/script-report.js"></script>
</body>
</html>
