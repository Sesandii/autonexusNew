<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer Profile</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/sidebar.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/individualDetails.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php include APP_ROOT . '/views/layouts/manager-sidebar.php'; ?>  

<div class="main">
  <div class="header">
    <h2>Customer Profile</h2>
    <div class="top-actions">
     <!-- <button class="update-btn" onclick="window.location.href='<?= BASE_URL ?>/receptionist/customers/edit/<?= $customer['customer_id'] ?>'">
      Update
      </button>-->

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
    <?php if (!empty($customer['appointments'])): ?>
        <div class="appointment-grid">
        <?php foreach ($customer['appointments'] as $appt): ?>
            <div class="appointment-card">
                <h4>Appointment #<?= htmlspecialchars($appt['appointment_id']) ?> 
                    - <?= date('F j, Y', strtotime($appt['appointment_date'])) ?>
                </h4>
                <span class="badge-status <?= htmlspecialchars(strtolower($appt['status'])) ?>">
                    <?= htmlspecialchars($appt['status']) ?>
                </span>

                <p><b>Vehicle:</b> <?= htmlspecialchars($appt['year'] . ' ' . $appt['make'] . ' ' . $appt['model']) ?></p>
                <p><b>Service:</b> <?= htmlspecialchars($appt['service_name']) ?></p>

                <?php if (!empty($appt['work_orders'])): ?>
                    <h5>Work Orders:</h5>
                    <ul>
                        <?php foreach ($appt['work_orders'] as $wo): ?>
                            <li>
                                Mechanic: <?= htmlspecialchars($wo['mechanic_first'] . ' ' . $wo['mechanic_last']) ?>,
                                Supervisor: <?= htmlspecialchars($wo['supervisor_first'] . ' ' . $wo['supervisor_last']) ?>,
                                Summary: <?= htmlspecialchars($wo['service_summary'] ?? 'N/A') ?>,
                                Cost: $<?= htmlspecialchars($wo['total_cost']) ?>,
                                Status: <?= htmlspecialchars($wo['status']) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <?php if (!empty($appt['complaints'])): ?>
                    <h5>Complaints:</h5>
                    <ul>
                        <?php foreach ($appt['complaints'] as $c): ?>
                            <li>
                                Subject: <?= htmlspecialchars($c['subject']) ?>,
                                Status: <?= htmlspecialchars($c['status']) ?>,
                                Priority: <?= htmlspecialchars($c['priority']) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No service history available.</p>
    <?php endif; ?>
</div>

</div>

<script src="<?= BASE_URL ?>/public/assets/js/manager/individualDetails.js"></script>
</body>
</html>
