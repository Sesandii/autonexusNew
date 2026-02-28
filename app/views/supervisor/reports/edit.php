<?php $base = rtrim(BASE_URL, '/'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Edit Report | AutoNexus</title>
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/supervisor/style-report.css"/>
</head>
<body>

<?php include __DIR__ . '/../partials/sidebar.php'; ?>

<main class="main-content">

<header>
  <h1>Edit Report</h1>
</header>

<?php if (!empty($report) && !empty($workOrder)): ?>

    <form 
    action="<?= rtrim(BASE_URL, '/') ?>/supervisor/reports/update/<?= $report['report_id'] ?>" 
    method="POST" 
    enctype="multipart/form-data"
>

<input type="hidden" name="report_id" value="<?= $report['report_id'] ?>">
<input type="hidden" name="work_order_id" value="<?= $workOrder['work_order_id'] ?>">
<div class="grid-row two-columns">
<!-- Job Info -->
<div class="card">
  <div class="card-header">
    <h2>Job Information</h2>
    <span class="status <?= htmlspecialchars($report['status']) ?>">
      <?= ucfirst($report['status']) ?>
    </span>
  </div>

  <div class="card-content">
    <div class="info-grid">
      <div>
        <p class="label">Job ID</p>
        <p class="value"><?= $workOrder['work_order_id'] ?></p>
      </div>
      <div>
        <p class="label">Vehicle Number</p>
        <p class="value"><?= $workOrder['license_plate'] ?></p>
      </div>
      <div>
        <p class="label">Customer</p>
        <p class="value"><?= htmlspecialchars(($workOrder['customer_first_name'] ?? '') . ' ' . ($workOrder['customer_last_name'] ?? '-')) ?></p>
      </div>
      <div>
        <p class="label">Mechanic</p>
        <p class="value"><?= $workOrder['mechanic_code'] ?></p>
      </div>
    </div>
  </div>
</div>

<!-- Service Summary (Read-only) -->
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
        <?php foreach ($services as $s): ?>
        <tr>
          <td><?= htmlspecialchars($s['item_name']) ?></td>
          <td class="status <?= $s['status'] ?>">
            <?= ucfirst($s['status']) ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</div>

<!-- Final Inspection -->
<div class="grid-row three-columns">
<div class="form-section">
  <h2>Final Inspection</h2>

  <label>Inspection Notes</label>
  <textarea name="inspection_notes" required><?= htmlspecialchars($report['inspection_notes']) ?></textarea>

  <label>Quality Rating</label>
  <select name="quality_rating" required>
    <?php for ($i = 1; $i <= 5; $i++): ?>
      <option value="<?= $i ?>" <?= $report['quality_rating'] == $i ? 'selected' : '' ?>>
        <?= $i ?> Star<?= $i > 1 ? 's' : '' ?>
      </option>
    <?php endfor; ?>
  </select>

  <div class="checklist">
  <label>
    <input type="checkbox" name="checklist[]" value="tasks_verified"
      <?= !empty($report['checklist_verified']) ? 'checked' : '' ?>>
    All service tasks verified
  </label>

  <label>
    <input type="checkbox" name="checklist[]" value="test_driven"
      <?= !empty($report['test_driven']) ? 'checked' : '' ?>>
    Vehicle test driven
  </label>

  <label>
    <input type="checkbox" name="checklist[]" value="concerns_addressed"
      <?= !empty($report['concerns_addressed']) ? 'checked' : '' ?>>
    Customer concerns addressed
  </label>
</div>

</div>


<!-- Work Photos -->
<div class="form-section">
  <h2>Work Photos</h2>

  <?php if (!empty($photos)): ?>
  <div class="photo-gallery" style="display:flex; flex-wrap:wrap;">
    <?php foreach ($photos as $photo): ?>
      <div style="position:relative; margin:5px;">
      <img 
    src="<?= $base ?>/public/<?= htmlspecialchars($photo['file_path']) ?>" 
    alt="Report Photo" 
    style="width:150px; margin:5px; border-radius:6px; box-shadow:0 1px 4px rgba(0,0,0,0.2);"
/>

        <a
  href="<?= BASE_URL ?>/supervisor/reports/delete-photo/<?= (int)$photo['photo_id'] ?>"
  onclick="return confirm('Are you sure you want to delete this photo?');"
  style="position:absolute; top:5px; right:5px; background:red; color:white; padding:2px 6px; border-radius:50%; text-decoration:none; font-weight:bold;"
>
  ×
</a>

      </div>
    <?php endforeach; ?>
  </div>
<?php else: ?>
  <p>No photos uploaded yet.</p>
<?php endif; ?>

  <!-- Upload additional photos -->
  <label>Upload Additional Photos</label>
  <input type="file" name="work_images[]" multiple accept=".png,.jpg,.jpeg,.gif">
</div>



<!-- Final Report -->
<div class="form-section">
  <h2>Final Report</h2>

  <label>Report Summary</label>
  <textarea name="report_summary" required><?= htmlspecialchars($report['report_summary']) ?></textarea>

  <label>Next Service Recommendation</label>
<input 
  type="date"
  name="next_service_recommendation"
  value="<?= (!empty($report['next_service_recommendation']) && $report['next_service_recommendation'] !== '0000-00-00') ? htmlspecialchars($report['next_service_recommendation']) : '' ?>"
>
</div>

<!-- Actions -->
<div class="actions">
  <button type="submit" name="status" value="draft" class="btn secondary">
    Save as Draft
  </button>

  <button type="submit" name="status" value="submitted" class="btn primary">
    Submit Final Report
  </button>

  <a href="<?= $base ?>/supervisor/reports" class="btn secondary">
    Cancel
  </a>
</div>

</form>

<?php else: ?>
  <p>Report not found.</p>
<?php endif; ?>

</main>
</body>
</html>
