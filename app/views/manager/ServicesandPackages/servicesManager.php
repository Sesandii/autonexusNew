<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Services & Packages - AutoNexus</title>
   <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/sidebar.css">
   <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/servicesManager.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
  
<?php include APP_ROOT . '/views/layouts/manager-sidebar.php'; ?>

<div class="main">
    <div class="header">
  <h2>Services & Packages</h2>

  <div class="top-actions">
    <!-- Create New Appointment Button -->
 <button class="add-btn" 
        onclick="window.location.href='<?= BASE_URL ?>/manager/services/create'">
  + Add new
</button>
</div>
</div>

  <nav class="tab-nav">
    <ul class="tab-list">
      <li class="tab-item active" data-tab="service">Services</li>
      <li class="tab-item" data-tab="packages">Packages</li>
    </ul>
  </nav>

  <section id="service" class="tab-content active">
    <div class="packages">

    <div class="filter-container">
  <select class="servicetype-filter">
    <option value="">All Services</option>
    <option value="maintenance">Maintenance</option>
    <option value="tyre">Tyre</option>
    <option value="cleaning">Cleaning</option>
    <option value="nano">Nano</option>
    <option value="paint">Paint</option>
    <option value="electrical">Electrical</option>
    <option value="brakes">Brakes</option>
    <option value="air-conditioning">Air Conditioning</option>
    <option value="packages">Packages</option>
  </select>
</div>

<?php if (!empty($package['services']) && is_array($package['services'])): ?>
    <?php foreach ($package['services'] as $service): ?>
        <li><?= htmlspecialchars($service['name']) ?> - <?= $service['base_duration_minutes'] ?> min - Rs.<?= $service['default_price'] ?></li>
    <?php endforeach; ?>
<?php else: ?>
    <li></li>
<?php endif; ?>


     <div class="service-grid">
  <?php foreach ($services as $row): ?>
    <div class="service-tile">
  

      <h4 class="tile-title">
        <?= htmlspecialchars($row['name']) ?>
      </h4>

      <p class="tile-desc">
        <?= htmlspecialchars($row['description']) ?>
      </p>

      <p class="tile-duration">
        <?= $row['base_duration_minutes'] ?> min
      </p>

      <p class="tile-price">
        Rs. <?= number_format($row['default_price'], 2) ?>
      </p>
    </div>
  <?php endforeach; ?>
</div>


    </div>
  </section>

<section id="packages" class="tab-content">
  
      
<?php $packages = $packages ?? []; ?>
<?php foreach($packages as $package): ?>
<div class="package-card">
    <div class="package-header">
        <h2><?= htmlspecialchars($package['name']) ?></h2>
    </div>
    <p><?= htmlspecialchars($package['description']) ?></p>

    <ul class="package-services">
        <?php foreach($package['services'] as $service): ?>
        <li>✅ <?= htmlspecialchars($service['name']) ?> - <?= $service['base_duration_minutes'] ?> min - Rs.<?= $service['default_price'] ?></li>
        <?php endforeach; ?>
    </ul>

    <div class="package-footer">
        <span class="duration">Takes <?= $package['total_duration_minutes'] ?> min</span>
        <span class="price">Rs.<?= $package['total_price'] ?></span>
    </div>
</div>
<?php endforeach; ?>

</section>
</div>

<script src="<?= BASE_URL ?>/public/assets/js/manager/service.js"></script>
</body>
</html>
