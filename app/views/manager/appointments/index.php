<?php $base = rtrim(BASE_URL, '/'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Appointments - AutoNexus</title>

  <!-- Remembered sidebar CSS -->
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/manager/sidebar.css">
  <!-- Page CSS (migrated from sm_css to css/manager) -->
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/manager/appointment.css">
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/manager/sidebar.css"><!-- if your page also needs sidebar layout styles -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>

  <?php include APP_ROOT . '/views/layouts/managersidebar.php'; ?>

  <div class="main">
    <h4 id="appointments-header">Appointments</h4>

    <div class="table-section">
      <table id="appointmentsTable">
        <thead>
          <tr>
            <th>Time</th>
            <th>Customer</th>
            <th>Vehicle Number</th>
            <th>Vehicle</th>
            <th>Service</th>
            <th>Status</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr data-customer="Jane Doe" data-phone="555-1234" data-vehicle="Toyota Camry ABC-1234" data-service="Oil Change" data-status="Not Arrived">
            <td>10:30</td>
            <td>Jane Doe</td>
            <td>ABC-1212</td>
            <td>Toyota Camry</td>
            <td>Oil Change</td>
            <td><span class="status not-arrived">Not Arrived</span></td>
            <td><button class="update-btn" onclick="window.location.href='<?= $base ?>/manager/appointments/update'">Update</button></td>
          </tr>

          <tr data-customer="John Smith" data-phone="555-4567" data-vehicle="Honda Civic XYZ-5678" data-service="Inspection" data-status="Waiting">
            <td>11:00</td>
            <td>John Smith</td>
            <td>ABC-1212</td>
            <td>Honda Civic</td>
            <td>Inspection</td>
            <td><span class="status waiting">Waiting</span></td>
            <td><button class="update-btn" onclick="window.location.href='<?= $base ?>/manager/appointments/update'">Update</button></td>
          </tr>

          <tr data-customer="Emily Johnson" data-phone="555-7890" data-vehicle="Ford Focus QRS-9012" data-service="Inspection" data-status="In Service">
            <td>11:30</td>
            <td>Emily Johnson</td>
            <td>ABC-1212</td>
            <td>Ford Focus</td>
            <td>Inspection</td>
            <td><span class="status in-service">In Service</span></td>
            <td><button class="update-btn" onclick="window.location.href='<?= $base ?>/manager/appointments/update'">Update</button></td>
          </tr>

          <tr data-customer="Michael Brown" data-phone="555-2345" data-vehicle="BMW 3 Series TUV-3456" data-service="Tire Rotation" data-status="Completed">
            <td>12:00</td>
            <td>Michael Brown</td>
            <td>ABC-1212</td>
            <td>BMW 3 Series</td>
            <td>Tire Rotation</td>
            <td><span class="status completed">Completed</span></td>
            <td><button class="update-btn" onclick="window.location.href='<?= $base ?>/manager/appointments/update'">Update</button></td>
          </tr>

          <tr data-customer="Sarah Davis" data-phone="555-6789" data-vehicle="Audi A4 WXY-7890" data-service="Canceled" data-status="Canceled">
            <td>12:30</td>
            <td>Sarah Davis</td>
            <td>ABC-1212</td>
            <td>Audi A4</td>
            <td>Canceled</td>
            <td><span class="status canceled">Canceled</span></td>
            <td><button class="update-btn" onclick="window.location.href='<?= $base ?>/manager/appointments/update'">Update</button></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Page JS (optional; update to your final path if needed) -->
  <script src="<?= $base ?>/public/assets/js/manager/dayAppointment.js"></script>
</body>
</html>
