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
    
    @media (max-width: 768px) {
      .detail-grid {
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
        <h1 class="hero-title">Service Details</h1>
        <p class="hero-desc">View the completed service record, vehicle information, assigned technician, and download the service summary as a PDF.</p>

        <?php if ($service): ?>
        <a href="<?= $base ?>/customer/service-history/<?= (int)$service['work_order_id'] ?>/pdf" 
           class="download-btn" 
           title="Download PDF">
          <i class="fa-solid fa-file-pdf"></i>
          Download PDF
        </a>
        <?php endif; ?>
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
