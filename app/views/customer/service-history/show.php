<?php
$base = rtrim(BASE_URL, '/');
$service = $service ?? null;
$serviceTasks = $serviceTasks ?? [];
$photos = $photos ?? [];
$hasFinalReport = is_array($service) && !empty($service['report_id']);
$qualityRating = max(0, min(5, (int)($service['quality_rating'] ?? 0)));

$summaryText = trim((string)($service['report_summary'] ?? ''));
if ($summaryText === '') {
  $summaryText = (string)($service['description'] ?? '-');
}

$nextDue = !empty($service['last_service_mileage'])
  ? ((string)$service['last_service_mileage'] . ' km')
  : 'Not set';

$serviceInterval = !empty($service['service_interval_km'])
  ? ((string)$service['service_interval_km'] . ' km')
  : '5000 km';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($title ?? 'Service Details') ?> - AutoNexus</title>

  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/service-history.css">
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/sidebar.css">
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .detail-container {
      max-width: 1100px;
      margin: 0 auto;
      padding: 24px;
    }

    .detail-hero {
      background: #fff8f8;
      border: 1px solid #ffe2e2;
      border-radius: 16px;
      padding: 24px 28px;
      margin-bottom: 28px;
      box-shadow: 0 10px 30px rgba(220, 38, 38, 0.05);
    }

    .hero-subtitle {
      font-size: 0.85rem;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      color: #b91c1c;
      font-weight: 700;
      margin: 10px 0 6px;
    }

    .hero-title {
      font-size: 2rem;
      font-weight: 800;
      color: #0f172a;
      margin: 0 0 10px;
    }

    .hero-desc {
      color: #4b5563;
      font-size: 1rem;
      margin: 0 0 18px;
    }
    
    .back-link {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      color: #ef4444;
      text-decoration: none;
      font-size: 0.95rem;
      padding: 10px 14px;
      border-radius: 10px;
      background: #fff7f7;
      border: 1px solid #ffe2e2;
      font-weight: 600;
      transition: all 0.2s ease;
    }

    .back-link:hover {
      color: #dc2626;
      border-color: #fca5a5;
      box-shadow: 0 4px 12px rgba(220, 38, 38, 0.08);
    }
    
    .detail-card {
      background: #ffffff;
      border-radius: 14px;
      padding: 24px;
      box-shadow: 0 8px 18px rgba(15, 23, 42, 0.04);
      border: 1px solid #e5e7eb;
      margin-bottom: 20px;
    }
    
    .detail-section {
      margin-bottom: 24px;
    }
    
    .detail-section:last-child {
      margin-bottom: 0;
    }
    
    .section-title {
      font-size: 1.1rem;
      font-weight: 600;
      color: #111827;
      margin-bottom: 12px;
      padding-bottom: 8px;
      border-bottom: 2px solid #fecaca;
    }
    
    .detail-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 16px;
    }
    
    .detail-item {
      display: flex;
      flex-direction: column;
      gap: 4px;
    }
    
    .detail-label {
      font-size: 0.75rem;
      color: #9ca3af;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      font-weight: 500;
    }
    
    .detail-value {
      font-size: 0.95rem;
      color: #111827;
      font-weight: 500;
    }
    
    .description-box {
      background: #f9fafb;
      padding: 14px;
      border-radius: 8px;
      border-left: 3px solid #ef4444;
      font-size: 0.9rem;
      color: #4b5563;
      line-height: 1.6;
    }

    .report-alert {
      margin-bottom: 18px;
      padding: 12px 14px;
      border-radius: 10px;
      border: 1px solid #fed7aa;
      background: #fff7ed;
      color: #9a3412;
      font-weight: 600;
      font-size: 0.92rem;
    }

    .task-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    .task-table th,
    .task-table td {
      text-align: left;
      padding: 10px 8px;
      border-bottom: 1px solid #e5e7eb;
      font-size: 0.9rem;
    }

    .task-table th {
      font-size: 0.8rem;
      color: #6b7280;
      text-transform: uppercase;
      letter-spacing: 0.04em;
    }

    .task-status {
      display: inline-flex;
      align-items: center;
      padding: 4px 10px;
      border-radius: 999px;
      font-size: 0.78rem;
      font-weight: 700;
      text-transform: capitalize;
      border: 1px solid #e5e7eb;
      color: #374151;
      background: #f3f4f6;
    }

    .task-status--completed {
      background: #dcfce7;
      border-color: #bbf7d0;
      color: #166534;
    }

    .task-status--done {
      background: #dcfce7;
      border-color: #bbf7d0;
      color: #166534;
    }

    .task-status--pending {
      background: #fef3c7;
      border-color: #fde68a;
      color: #92400e;
    }

    .task-status--in_progress {
      background: #dbeafe;
      border-color: #bfdbfe;
      color: #1d4ed8;
    }

    .inspection-grid {
      display: grid;
      grid-template-columns: 1.2fr 1fr;
      gap: 18px;
      margin-top: 10px;
    }

    .rating-stars {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      margin-top: 8px;
    }

    .rating-stars i.active {
      color: #f59e0b;
    }

    .rating-stars i.inactive {
      color: #d1d5db;
    }

    .rating-score {
      margin-left: 8px;
      font-size: 0.86rem;
      color: #4b5563;
      font-weight: 700;
    }

    .report-checklist {
      list-style: none;
      margin: 12px 0 0;
      padding: 0;
      display: grid;
      gap: 8px;
    }

    .report-checklist li {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 10px 12px;
      border-radius: 8px;
      font-size: 0.9rem;
      font-weight: 600;
    }

    .report-checklist li.ok {
      color: #166534;
      background: #ecfdf5;
      border: 1px solid #bbf7d0;
    }

    .report-checklist li.no {
      color: #991b1b;
      background: #fef2f2;
      border: 1px solid #fecaca;
    }

    .photo-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
      gap: 12px;
      margin-top: 10px;
    }

    .photo-grid img {
      width: 100%;
      height: 130px;
      object-fit: cover;
      border-radius: 10px;
      border: 1px solid #e5e7eb;
      cursor: pointer;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .photo-grid img:hover {
      transform: translateY(-1px);
      box-shadow: 0 8px 16px rgba(15, 23, 42, 0.12);
    }

    .text-muted {
      color: #6b7280;
      font-size: 0.9rem;
      margin-top: 10px;
    }

    .modal-lightbox {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.8);
      z-index: 2000;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .modal-lightbox img {
      max-width: min(1000px, 95vw);
      max-height: 85vh;
      border-radius: 10px;
      box-shadow: 0 18px 40px rgba(0, 0, 0, 0.45);
    }

    .close-cursor {
      position: absolute;
      top: 16px;
      right: 20px;
      color: #ffffff;
      font-size: 2rem;
      line-height: 1;
      cursor: pointer;
    }
    
    @media (max-width: 768px) {
      .detail-grid {
        grid-template-columns: 1fr;
      }

      .inspection-grid {
        grid-template-columns: 1fr;
      }
      
      .detail-container {
        padding: 16px;
      }
    }
    
    .download-btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 12px 20px;
      background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
      color: #ffffff;
      text-decoration: none;
      border-radius: 10px;
      font-weight: 700;
      font-size: 0.95rem;
      transition: all 0.25s ease;
      box-shadow: 0 6px 16px rgba(220, 38, 38, 0.2);
      white-space: nowrap;
    }

    .download-btn:hover {
      transform: translateY(-1px);
      box-shadow: 0 10px 24px rgba(220, 38, 38, 0.28);
    }

    .download-btn i {
      font-size: 1rem;
    }
  </style>
</head>
<body>
  <?php include APP_ROOT . '/views/layouts/customer-sidebar.php'; ?>

  <div class="sh-layout customer-layout-main">
    <div class="detail-container">
      <div class="detail-hero">
        <a href="<?= $base ?>/customer/service-history" class="back-link">
          <i class="fa-solid fa-arrow-left"></i>
          Back to Service History
        </a>

        <div class="hero-subtitle">Customer Portal</div>
        <h1 class="hero-title"><?= $hasFinalReport ? 'Final Service Report' : 'Service Details' ?></h1>
        <p class="hero-desc"><?= $hasFinalReport
          ? 'Review the final inspection, service checklist, photo evidence, and download your final report.'
          : 'Service details are available, but the final report has not been submitted yet.' ?>
        </p>

        <?php if ($service && $hasFinalReport): ?>
        <a href="<?= $base ?>/customer/service-history/<?= (int)$service['work_order_id'] ?>/pdf" 
           class="download-btn" 
           title="Download Final Report PDF">
          <i class="fa-solid fa-file-pdf"></i>
          Download Final Report PDF
        </a>
        <?php endif; ?>
      </div>

      <?php if ($service): ?>
        <?php
          $serviceStatusRaw = strtolower((string)($service['status'] ?? 'completed'));
          $statusClass = 'sh-status--other';
          if ($serviceStatusRaw === 'completed') {
            $statusClass = 'sh-status--completed';
          } elseif ($serviceStatusRaw === 'in-progress' || $serviceStatusRaw === 'in progress') {
            $statusClass = 'sh-status--inprogress';
          } elseif ($serviceStatusRaw === 'cancelled' || $serviceStatusRaw === 'canceled') {
            $statusClass = 'sh-status--cancelled';
          }
        ?>

        <?php if (!$hasFinalReport): ?>
          <div class="report-alert">
            Final report is not available yet. Please check again after the supervisor submits it.
          </div>
        <?php endif; ?>

        <div class="detail-card">
          <div class="detail-section">
            <div class="section-title">Job Inspection & Reporting</div>
            <div class="detail-grid">
              <div class="detail-item">
                <span class="detail-label">Work Order #</span>
                <span class="detail-value"><?= htmlspecialchars($service['work_order_id']) ?></span>
              </div>
              
              <div class="detail-item">
                <span class="detail-label">Service Status</span>
                <span class="detail-value">
                  <span class="sh-status <?= $statusClass ?>">
                    <span class="dot"></span>
                    <?= htmlspecialchars(ucfirst($service['status'] ?? 'completed')) ?>
                  </span>
                </span>
              </div>

              <div class="detail-item">
                <span class="detail-label">Customer</span>
                <span class="detail-value"><?= htmlspecialchars($service['customer_name'] ?? 'N/A') ?></span>
              </div>

              <div class="detail-item">
                <span class="detail-label">Assigned Mechanic</span>
                <span class="detail-value"><?= htmlspecialchars($service['technician'] ?? 'Not assigned') ?></span>
              </div>
              
              <div class="detail-item">
                <span class="detail-label">Service Type</span>
                <span class="detail-value"><?= htmlspecialchars($service['service_type'] ?? 'N/A') ?></span>
              </div>
              
              <div class="detail-item">
                <span class="detail-label">Total Cost</span>
                <span class="detail-value">Rs. <?= number_format((float)($service['price'] ?? 0), 2) ?></span>
              </div>
              
              <div class="detail-item">
                <span class="detail-label">Date</span>
                <span class="detail-value"><?= htmlspecialchars(!empty($service['date']) ? date('M d, Y', strtotime((string)$service['date'])) : 'N/A') ?></span>
              </div>
              
              <div class="detail-item">
                <span class="detail-label">Time</span>
                <span class="detail-value"><?= htmlspecialchars($service['time'] ?? 'N/A') ?></span>
              </div>
            </div>
          </div>

          <div class="detail-section">
            <div class="section-title">Vehicle Information</div>
            <div class="detail-grid">
              <div class="detail-item">
                <span class="detail-label">License Plate</span>
                <span class="detail-value"><?= htmlspecialchars($service['license_plate'] ?? 'N/A') ?></span>
              </div>
              
              <div class="detail-item">
                <span class="detail-label">Make & Model</span>
                <span class="detail-value"><?= htmlspecialchars(($service['make'] ?? '') . ' ' . ($service['model'] ?? '')) ?></span>
              </div>
              
              <?php if (!empty($service['vehicle_year'])): ?>
              <div class="detail-item">
                <span class="detail-label">Year</span>
                <span class="detail-value"><?= htmlspecialchars($service['vehicle_year']) ?></span>
              </div>
              <?php endif; ?>
            </div>
          </div>

          <div class="detail-section">
            <div class="section-title">Service Summary</div>
            <table class="task-table">
              <thead>
                <tr>
                  <th>Service Task</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($serviceTasks)): ?>
                  <?php foreach ($serviceTasks as $task): ?>
                    <?php
                      $taskStatus = strtolower((string)($task['status'] ?? 'pending'));
                      $taskStatusClass = preg_replace('/[^a-z0-9_]/', '_', $taskStatus) ?: 'pending';
                    ?>
                    <tr>
                      <td><?= htmlspecialchars($task['item_name'] ?? 'Service task') ?></td>
                      <td>
                        <span class="task-status task-status--<?= htmlspecialchars($taskStatusClass) ?>">
                          <?= htmlspecialchars(ucfirst($taskStatus)) ?>
                        </span>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="2" class="text-muted">No service details available.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

          <div class="detail-section">
            <div class="section-title">Final Inspection</div>
            <div class="inspection-grid">
              <div>
                <div class="detail-label">Inspection Notes</div>
                <div class="description-box"><?= nl2br(htmlspecialchars($service['inspection_notes'] ?? '-')) ?></div>
              </div>

              <div>
                <div class="detail-label">Quality Rating</div>
                <div class="rating-stars" aria-label="Quality rating">
                  <?php for ($i = 1; $i <= 5; $i++): ?>
                    <i class="<?= $i <= $qualityRating ? 'fa-solid fa-star active' : 'fa-regular fa-star inactive' ?>"></i>
                  <?php endfor; ?>
                  <span class="rating-score"><?= (int)$qualityRating ?>/5</span>
                </div>

                <ul class="report-checklist">
                  <li class="<?= !empty($service['checklist_verified']) ? 'ok' : 'no' ?>">
                    <i class="fa-solid <?= !empty($service['checklist_verified']) ? 'fa-circle-check' : 'fa-circle-xmark' ?>"></i>
                    <span>Tasks verified</span>
                  </li>
                  <li class="<?= !empty($service['test_driven']) ? 'ok' : 'no' ?>">
                    <i class="fa-solid <?= !empty($service['test_driven']) ? 'fa-circle-check' : 'fa-circle-xmark' ?>"></i>
                    <span>Vehicle test driven</span>
                  </li>
                  <li class="<?= !empty($service['concerns_addressed']) ? 'ok' : 'no' ?>">
                    <i class="fa-solid <?= !empty($service['concerns_addressed']) ? 'fa-circle-check' : 'fa-circle-xmark' ?>"></i>
                    <span>Customer concerns addressed</span>
                  </li>
                </ul>
              </div>
            </div>
          </div>

          <div class="detail-section">
            <div class="section-title">Work Photos</div>
            <?php if (!empty($photos)): ?>
              <div class="photo-grid">
                <?php foreach ($photos as $photo): ?>
                  <?php $photoPath = htmlspecialchars(ltrim((string)($photo['file_path'] ?? ''), '/')); ?>
                  <img
                    src="<?= $base ?>/public/<?= $photoPath ?>"
                    alt="Service report photo"
                    onclick="openLightbox(this.src)"
                    loading="lazy"
                  />
                <?php endforeach; ?>
              </div>
            <?php else: ?>
              <p class="text-muted">No photos uploaded for this report.</p>
            <?php endif; ?>
          </div>

          <div class="detail-section">
            <div class="section-title">Final Report</div>
            <div class="description-box"><?= nl2br(htmlspecialchars($summaryText)) ?></div>
          </div>

          <div class="detail-section">
            <div class="section-title">Service Continuity</div>
            <div class="detail-grid">
              <div class="detail-item">
                <span class="detail-label">Next Service Due</span>
                <span class="detail-value"><?= htmlspecialchars($nextDue) ?></span>
              </div>
              
              <div class="detail-item">
                <span class="detail-label">Service Interval</span>
                <span class="detail-value"><?= htmlspecialchars($serviceInterval) ?></span>
              </div>
            </div>
          </div>

          <?php if (!empty($service['next_service_recommendation'])): ?>
          <div class="detail-section">
            <div class="section-title">Next Service Recommendation</div>
            <div class="description-box">
              <?= nl2br(htmlspecialchars($service['next_service_recommendation'])) ?>
            </div>
          </div>
          <?php endif; ?>

          <?php if (!empty($service['description'])): ?>
          <div class="detail-section">
            <div class="section-title">Internal Service Notes</div>
            <div class="description-box">
              <?= nl2br(htmlspecialchars($service['description'])) ?>
            </div>
          </div>
          <?php endif; ?>
        </div>
      <?php else: ?>
        <div class="detail-card">
          <p style="text-align: center; color: #6b7280;">Service record not found.</p>
        </div>
      <?php endif; ?>

      <div id="imageModal" class="modal-lightbox" onclick="closeLightbox()">
        <span class="close-cursor">&times;</span>
        <img id="modalImage" src="" alt="Preview" />
      </div>
    </div>
  </div>

  <script>
    function openLightbox(imageSrc) {
      const modal = document.getElementById('imageModal');
      const image = document.getElementById('modalImage');
      image.src = imageSrc;
      modal.style.display = 'flex';
      document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
      const modal = document.getElementById('imageModal');
      modal.style.display = 'none';
      document.body.style.overflow = 'auto';
    }

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') {
        closeLightbox();
      }
    });
  </script>
</body>
</html>
