<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Services & Packages - AutoNexus</title>
   <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/sidebar.css">
   <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/services.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>

<?php include APP_ROOT . '/views/layouts/receptionist-sidebar.php'; ?>

<div class="main">
  <header class="header-bar">
    <h1>Services & Packages</h1>
  </header>

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

      <table>
        <thead>
          <tr>
            <th>Service</th>
            <th>Description</th>
            <th>Duration</th>
            <th>Price</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($services)): ?>
            <?php foreach ($services as $row): ?>
              <tr class="clickable-row">
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['description']) ?></td>
                <td><?= htmlspecialchars($row['base_duration_minutes']) ?> min</td>
                <td class="price">Rs.<?= htmlspecialchars($row['default_price']) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="4">No active services found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
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

<script src="<?= BASE_URL ?>/public/assets/js/receptionist/x.js"></script>
</body>
</html>
