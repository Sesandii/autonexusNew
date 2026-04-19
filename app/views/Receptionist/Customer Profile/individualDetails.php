<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer Profile</title>

  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/sidebar.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/individualDetails.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

<?php include APP_ROOT . '/views/layouts/receptionist-sidebar.php'; ?>  

<div class="main">

  <div class="header">
    <h2>Customer Profile</h2>
    <div class="top-actions">
      <button class="update-btn"
        onclick="window.location.href='<?= BASE_URL ?>/receptionist/customers/edit/<?= $customer['customer_id'] ?>'">
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
        <p class="id-line">
          Customer ID:
          <span class="id"><?= htmlspecialchars($customer['customer_code']) ?></span>
        </p>
      </div>
    </div>

    <div class="profile-meta">
      <span class="badge <?= strtolower($customer['status']) ?>">
        <?= htmlspecialchars($customer['status']) ?>
      </span>

      <p class="since">
        Customer Since:
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

  <!-- ================= OVERVIEW TAB ================= -->
  <div id="overview" class="tab-content active">
    <div class="overview-grid">

      <div class="overview-card">
        <h3>Contact Information</h3>
        <p>📞 Phone Number: <?= htmlspecialchars($customer['phone']) ?></p>
        <p>📧 Email: <?= htmlspecialchars($customer['email']) ?></p>
        <p>📍 Address: <?= htmlspecialchars($customer['street_address'] . ', ' . $customer['city'] . ', ' . $customer['state']) ?></p>
      </div>

    </div>
  </div>

  <!-- ================= VEHICLE TAB ================= -->
  <div id="vehicle" class="tab-content">
    <h3>Vehicle Details</h3>

    <?php if (!empty($customer['vehicles'])): ?>
      <div class="vehicle-grid">

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

      </div>
    <?php else: ?>
      <p>No vehicles found.</p>
    <?php endif; ?>
  </div>

  <!-- ================= HISTORY TAB ================= -->
  <div id="history" class="tab-content">
    <h3>Service History</h3>

    <?php if (!empty($customer['appointments'])): ?>
      <div class="appointment-grid">

        <?php foreach ($customer['appointments'] as $appt): ?>
          <div class="appointment-card">

            <h4>
              Appointment #<?= htmlspecialchars($appt['appointment_id']) ?>
              - <?= date('F j, Y', strtotime($appt['appointment_date'])) ?>
            </h4>

            <span class="badge-status <?= strtolower($appt['status']) ?>">
              <?= htmlspecialchars($appt['status']) ?>
            </span>

            <p><b>Time:</b> <?= htmlspecialchars($appt['appointment_time']) ?></p>
            <p><b>Service ID:</b> <?= htmlspecialchars($appt['service_id']) ?></p>

            <hr>

            <h5>Work Orders</h5>
            <?php if (!empty($appt['work_orders'])): ?>
              <ul class="nested-list">
                <?php foreach ($appt['work_orders'] as $wo): ?>
                  <li>
                    <b>Mechanic:</b> <?= htmlspecialchars($wo['mechanic_first'] . ' ' . $wo['mechanic_last']) ?><br>
                    <b>Supervisor:</b> <?= htmlspecialchars($wo['supervisor_first'] . ' ' . $wo['supervisor_last']) ?><br>
                    <b>Summary:</b> <?= htmlspecialchars($wo['service_summary'] ?? 'N/A') ?><br>
                    <b>Cost:</b> $<?= htmlspecialchars($wo['total_cost']) ?><br>
                    <b>Status:</b> <?= htmlspecialchars($wo['status']) ?>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php else: ?>
              <p>No work orders.</p>
            <?php endif; ?>

            <hr>

            <h5>Complaints</h5>
            <?php if (!empty($appt['complaints'])): ?>
              <ul class="nested-list">
                <?php foreach ($appt['complaints'] as $c): ?>
                  <li>
                    <b>Subject:</b> <?= htmlspecialchars($c['subject']) ?><br>
                    <b>Description:</b> <?= htmlspecialchars($c['description']) ?><br>
                    <b>Priority:</b> <?= htmlspecialchars($c['priority']) ?><br>
                    <b>Status:</b> <?= htmlspecialchars($c['status']) ?>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php else: ?>
              <p>No complaints.</p>
            <?php endif; ?>

          </div>
        <?php endforeach; ?>

      </div>
    <?php else: ?>
      <p>No service history available.</p>
    <?php endif; ?>
  </div>

</div>

<script src="<?= BASE_URL ?>/public/assets/js/receptionist/individualDetails.js"></script>
</body>
</html>