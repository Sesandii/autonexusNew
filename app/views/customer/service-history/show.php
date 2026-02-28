<?php
$base = rtrim(BASE_URL, '/');
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
      max-width: 900px;
      margin: 0 auto;
      padding: 24px;
    }
    
    .detail-header {
      margin-bottom: 24px;
    }
    
    .detail-header h1 {
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 1.5rem;
      color: #111827;
      margin-bottom: 8px;
    }
    
    .back-link {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      color: #ef4444;
      text-decoration: none;
      font-size: 0.9rem;
      margin-bottom: 16px;
      font-weight: 500;
    }
    
    .back-link:hover {
      color: #dc2626;
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
    
    @media (max-width: 768px) {
      .detail-grid {
        grid-template-columns: 1fr;
      }
      
      .detail-container {
        padding: 16px;
      }
    }
  </style>
</head>
<body>
  <?php include APP_ROOT . '/views/layouts/customer-sidebar.php'; ?>

  <div class="sh-layout">
    <div class="detail-container">
      <div class="detail-header">
        <a href="<?= $base ?>/customer/service-history" class="back-link">
          <i class="fa-solid fa-arrow-left"></i>
          Back to Service History
        </a>
        
        <h1>
          <i class="fa-solid fa-file-lines"></i>
          Service Details
        </h1>
      </div>

      <?php if ($service): ?>
        <div class="detail-card">
          <!-- Service Information -->
          <div class="detail-section">
            <div class="section-title">Service Information</div>
            <div class="detail-grid">
              <div class="detail-item">
                <span class="detail-label">Work Order #</span>
                <span class="detail-value"><?= htmlspecialchars($service['work_order_id']) ?></span>
              </div>
              
              <div class="detail-item">
                <span class="detail-label">Status</span>
                <span class="detail-value">
                  <span class="sh-status sh-status--completed">
                    <span class="dot"></span>
                    <?= htmlspecialchars(ucfirst($service['status'] ?? 'completed')) ?>
                  </span>
                </span>
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
                <span class="detail-value"><?= htmlspecialchars(date('M d, Y', strtotime($service['date']))) ?></span>
              </div>
              
              <div class="detail-item">
                <span class="detail-label">Time</span>
                <span class="detail-value"><?= htmlspecialchars($service['time'] ?? 'N/A') ?></span>
              </div>
            </div>
          </div>

          <!-- Vehicle Information -->
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

          <!-- Technician & Branch -->
          <div class="detail-section">
            <div class="section-title">Service Provider</div>
            <div class="detail-grid">
              <div class="detail-item">
                <span class="detail-label">Technician</span>
                <span class="detail-value"><?= htmlspecialchars($service['technician'] ?? 'Not assigned') ?></span>
              </div>
              
              <div class="detail-item">
                <span class="detail-label">Branch</span>
                <span class="detail-value"><?= htmlspecialchars($service['branch_name'] ?? 'Main Branch') ?></span>
              </div>
            </div>
          </div>

          <!-- Service Description -->
          <?php if (!empty($service['description'])): ?>
          <div class="detail-section">
            <div class="section-title">Service Summary</div>
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
    </div>
  </div>
</body>
</html>
