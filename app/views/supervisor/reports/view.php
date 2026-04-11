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

<div class="grid-row two-columns">
  <!-- Job Inspection -->
  <div class="report-card">
    <h2>Job Inspection & Reporting</h2>
    <span class="status <?= htmlspecialchars($report['status']) ?>">
      <?= ucfirst(htmlspecialchars($report['status'])) ?>
    </span>
    <div class="info-grid">
      <div><p class="label">Job ID</p><p class="value"><?= htmlspecialchars($workOrder['service_name']) ?></p></div>
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
        <tr><th>Service Task</th><th>Status</th></tr>
      </thead>
      <tbody>
        <?php if (!empty($services)): ?>
          <?php foreach ($services as $s): ?>
            <tr>
              <td><?= htmlspecialchars($s['item_name']) ?></td>
              <td class="status <?= htmlspecialchars($s['status']) ?>"><?= ucfirst(htmlspecialchars($s['status'])) ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="3" style="text-align:center;">No service details available</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  </div>
  <!-- Final Inspection -->
  <div class="grid-row three-columns">
  <div class="report-card">
    <h2>Final Inspection</h2>
    <div class="inspection-details">
        <p><strong>Inspection Notes:</strong></p>
        <div class="notes-box"><?= nl2br(htmlspecialchars($report['inspection_notes'] ?? '-')) ?></div>
        
        <div class="rating-section">
            <p><strong>Quality Rating:</strong></p>
            <div class="rating-stars">
                <?= str_repeat('<span class="star active">★</span>', (int)$report['quality_rating']) ?>
                <?= str_repeat('<span class="star inactive">★</span>', 5 - (int)$report['quality_rating']) ?>
            </div>
        </div>

        <p><strong>Checklist Confirmation:</strong></p>
        <ul class="professional-checklist">
            <li class="<?= $report['checklist_verified'] ? 'checked' : 'unchecked' ?>">
                <div class="status-icon"></div> 
                <span class="text">Tasks verified</span>
            </li>
            <li class="<?= $report['test_driven'] ? 'checked' : 'unchecked' ?>">
                <div class="status-icon"></div> 
                <span class="text">Vehicle test driven</span>
            </li>
            <li class="<?= $report['concerns_addressed'] ? 'checked' : 'unchecked' ?>">
                <div class="status-icon"></div> 
                <span class="text">Customer concerns addressed</span>
            </li>
        </ul>
    </div>
</div>

<div class="report-card">
    <h2>Work Photos</h2>
    <div class="photo-gallery">
        <?php if (!empty($photos)): ?>
            <?php foreach ($photos as $photo): ?>
                <div class="photo-item">
                    <img src="<?= $base ?>/public/<?= htmlspecialchars($photo['file_path']) ?>" 
                         alt="Report Photo" 
                         onclick="openLightbox(this.src)"/>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="dim-text text-center">No photos uploaded for this report.</p>
        <?php endif; ?>
    </div>
</div>

  <!-- Final Report -->
  <div class="report-card">
    <h2>Final Report</h2>
    <p><strong>Report Summary:</strong></p>
    <p><?= nl2br(htmlspecialchars($report['report_summary'] ?? '-')) ?></p>

    <p><strong>Service Continuity:</strong></p>
    <div class="mileage-info">
        <p>
            <span class="label">Next Service Due:</span> 
            <strong><?= htmlspecialchars($workOrder['last_service_mileage'] ?? 'Not set') ?> km</strong>
        </p>
        <p>
            <span class="label">Service Interval:</span> 
            <strong><?= htmlspecialchars($workOrder['service_interval_km'] ?? '5000') ?> km</strong>
        </p>
    </div>
</div>
  </div>
</section>

<div class="actions">
  <a href="<?= $base ?>/supervisor/reports/indexp" class="btn secondary">Back to Reports</a>
</div>

<?php else: ?>
  <p style="text-align:center;">Report details not found.</p>
<?php endif; ?>

<div id="imageModal" class="modal-lightbox" onclick="closeLightbox()">
  <span class="close-cursor">&times;</span>
  <div class="modal-content-container">
    <img id="bigImage" src="">
  </div>
</div>

</main>
<script>
function openLightbox(imageSrc) {
    const modal = document.getElementById("imageModal");
    const bigImg = document.getElementById("bigImage");
    
    // Set the source of the big image to the one clicked
    bigImg.src = imageSrc;
    
    // Show the modal
    modal.style.display = "flex";
    
    // Disable scrolling on the main page while viewing
    document.body.style.overflow = "hidden";
}

function closeLightbox() {
    const modal = document.getElementById("imageModal");
    modal.style.display = "none";
    
    // Re-enable scrolling
    document.body.style.overflow = "auto";
}

// Also close if the user presses the 'Escape' key
document.addEventListener('keydown', function(event) {
    if (event.key === "Escape") {
        closeLightbox();
    }
});
</script>
</body>
</html>
