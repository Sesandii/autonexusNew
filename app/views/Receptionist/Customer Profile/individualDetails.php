<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer Profile</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/sidebar.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/individualDetails.css">
</head>
<body>

<?php include APP_ROOT . '/views/layouts/receptionist-sidebar.php'; ?>  

<div class="main">
  <div class="header">
    <h2>Customer Profile</h2>
    <div class="top-actions">
      <button class="update-btn" onclick="window.location.href='<?= BASE_URL ?>/receptionist/customers/edit/<?= $customer['customer_id'] ?>'">
      Update
      </button>

    </div>
  </div>

  <!-- Profile Card -->
  <div class="profile-card">

  <div class="avatar-name">
  <img class="avatar"
     src="https://api.dicebear.com/7.x/initials/svg?seed=<?= urlencode($customer['first_name'] . ' ' . $customer['last_name']) ?>&backgroundColor=b6e3f4,c0aede,d1f2a5,f7c1bb"
     alt="Avatar">


    <div class="name-block">
      <h3><?= htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']) ?></h3>
      <p class="id-line">Customer ID: 
        <span class="id"><?= htmlspecialchars($customer['customer_code']) ?></span>
      </p>
    </div>
  </div>

  <div class="profile-meta">
    <span class="badge <?= strtolower($customer['status']) ?>">
      <?= htmlspecialchars($customer['status']) ?>
    </span>
    <p class="since">Customer Since: 
      <?= date('F j, Y', strtotime($customer['customer_since'])) ?>
    </p>
  </div>

</div>


  <!-- Tabs -->
  <nav class="tab-nav">
    <ul class="tab-list">
      <li class="tab-item active" data-tab="overview">Customer Overview</li>
      <li class="tab-item" data-tab="vehicle">Vehicle Information</li>
      <li class="tab-item" data-tab="history">Service History</li>
    </ul>
  </nav>

  <!-- Overview Tab -->
<div id="overview" class="tab-content active">
  <div class="overview-grid">

    <!-- Contact Information Card -->
    <div class="overview-card">
      <h3>Contact Information</h3>
      <p>📞Phone Number: <?= htmlspecialchars($customer['phone']) ?></p>
      <p>📧Email: <?= htmlspecialchars($customer['email']) ?></p>
      <p>📍Address: <?= htmlspecialchars($customer['street_address'] . ', ' . $customer['city'] . ', ' . $customer['state']) ?></p>
    </div>

    <!-- Recent Services Card -->
    <div class="overview-card">
      <h3>Recent Services</h3>

      <?php if (!empty($customer['services'])): ?>
        <?php 
          $recent = $customer['services'][0]; 
        ?>

        <p><b>Last Appointment:</b> 
          <?= date('F j, Y', strtotime($recent['date'])) ?>
        </p>

        <p><b>Vehicle:</b> 
          <?= htmlspecialchars($recent['vehicle_name'] ?? 'N/A') ?>
        </p>

        <p><b>Service:</b> 
          <?= htmlspecialchars($recent['service_name']) ?>
        </p>

      <?php else: ?>
        <p>No recent services recorded.</p>
      <?php endif; ?>
    </div>

  </div>
</div>

<!--vehicle tab-->
  <div id="vehicle" class="tab-content">
  <h3>Vehicle Details</h3>

  <?php if (!empty($customer['vehicles'])): ?>

    <div class="vehicle-grid">  <!-- NEW GRID WRAPPER -->

      <?php foreach ($customer['vehicles'] as $v): ?>
        
        <div class="vehicle-card">

          <p class="vehicle-title">
            <b><?= htmlspecialchars($v['year'] . ' ' . $v['make'] . ' ' . $v['model']) ?></b>
          </p>

          <ul class="vehicle-details">
            <li><b>Make:</b> <?= htmlspecialchars($v['make']) ?></li>
            <li><b>Model:</b> <?= htmlspecialchars($v['model']) ?></li>
            <li><b>Year:</b> <?= htmlspecialchars($v['year']) ?></li>
            <li><b>Color:</b> <?= htmlspecialchars($v['color']) ?></li>
            <li><b>License Plate:</b> <?= htmlspecialchars($v['license_plate']) ?></li>
            <li><b>VIN:</b> <?= htmlspecialchars($v['vin']) ?></li>
          </ul>

        </div>

      <?php endforeach; ?>

    </div> <!-- END GRID WRAPPER -->

  <?php else: ?>
    <p>No vehicles found.</p>
  <?php endif; ?>
</div>


  <!-- Service History Tab -->
  <div id="history" class="tab-content">
    <h3>Service History</h3>
    <?php if (!empty($customer['services'] ?? [])): ?>
      <table>
        <thead>
          <tr>
            <th>Service ID</th>
            <th>Date</th>
            <th>Service</th>
            <th>Technician</th>
            <th>Cost</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($customer['services'] as $s): ?>
            <tr>
              <td><?= htmlspecialchars($s['service_id']) ?></td>
              <td><?= htmlspecialchars($s['date']) ?></td>
              <td><?= htmlspecialchars($s['service_name']) ?></td>
              <td><?= htmlspecialchars($s['technician']) ?></td>
              <td>$<?= htmlspecialchars($s['cost']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <p class="total">Total Spent: <b>$<?= htmlspecialchars(array_sum(array_column($customer['services'], 'cost'))) ?></b></p>
    <?php else: ?>
      <p>No service history available.</p>
    <?php endif; ?>
  </div>

</div>

<script src="<?= BASE_URL ?>/public/assets/js/receptionist/individualDetails.js"></script>
</body>
</html>
