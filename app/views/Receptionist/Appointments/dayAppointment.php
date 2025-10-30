<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Team Schedule - AutoNexus</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/r_css/dayAppointment.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/r_css/sidebar.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
  <div class="sidebar">
    <div class="logo">
         <img src="<?= BASE_URL ?>/public/assets/img/Auto.png" alt="AutoNexus Logo">
      <h2>AUTONEXUS</h2>
      <p>VEHICLE SERVICE</p>
    </div>
        <ul class="menu">
  <li><a href="/autonexus/receptionist/dashboard">Dashboard</a></li>
  <li class="active"><a href="/autonexus/receptionist/appointments">Appointments</a></li>
  <li><a href="/autonexus/receptionist/service">Service & Packages</a></li>
  <li><a href="/autonexus/receptionist/complaints">Complaints</a></li>
  <li><a href="/autonexus/receptionist/billing">Billing & Payments</a></li>
  <li><a href="/autonexus/receptionist/customers">Customer Profiles</a></li>
  <li><a href="<?= rtrim(BASE_URL, '/') ?>/logout">Sign Out</a></li>
</ul>
  </div>


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
            <th>Action</th>
          </tr>
        </thead>
        <?php foreach($appointments as $appointment): ?>
<tr data-customer="<?= $appointment['customer'] ?>" data-phone="<?= $appointment['phone'] ?? '' ?>" data-vehicle="<?= $appointment['vehicle'] ?>" data-service="<?= $appointment['service'] ?>" data-status="<?= $appointment['status'] ?>">
    <td><?= $appointment['time'] ?></td>
    <td><?= $appointment['customer'] ?></td>
    <td><?= $appointment['vehicle_number'] ?></td>
    <td><?= $appointment['vehicle'] ?></td>
    <td><?= $appointment['service'] ?></td>
    <td><span class="status"><?= $appointment['status'] ?></span></td>
    <td>
        <button class="update-btn" onclick="window.location.href='<?= BASE_URL ?>/receptionist/appointments/edit/<?= $appointment['id'] ?>'">Update</button>
    </td>
</tr>
<?php endforeach; ?>
</tbody>
        <tbody>
          <tr data-customer="Jane Doe" data-phone="555-1234" data-vehicle="Toyota Camry ABC-1234" data-service="Oil Change" data-status="Not Arrived">
            <td>10:30</td>
            <td>Jane Doe</td>
            <td>ABC-1212</td>
            <td>Toyota Camry</td>
            <td>Oil Change</td>
            <td><span class="status not-arrived">Not Arrived</span></td>
            <td>
            <button class="update-btn" 
                onclick="window.location.href='<?= BASE_URL ?>/receptionist/appointments/edit/<?= $appointment['id'] ?>'">
                Update
            </button>

            </td>
          </tr>
          <tr data-customer="John Smith" data-phone="555-4567" data-vehicle="Honda Civic XYZ-5678" data-service="Inspection" data-status="Waiting">
            <td>11:00</td>
            <td>John Smith</td>
            <td>ABC-1212</td>
            <td>Honda Civic</td>
            <td>Inspection</td>
            <td><span class="status waiting">Waiting</span></td>
            <td>
            <button class="update-btn" onclick="window.location.href='updateApp.php'">Update</button>
            </td>
          </tr>
          <tr data-customer="Emily Johnson" data-phone="555-7890" data-vehicle="Ford Focus QRS-9012" data-service="Inspection" data-status="In Service">
            <td>11:30</td>
            <td>Emily Johnson</td>
            <td>ABC-1212</td>
            <td>Ford Focus</td>
            <td>Inspection</td>
            <td><span class="status in-service">In Service</span></td>
            <td>
            <button class="update-btn" onclick="window.location.href='updateApp.php'">Update</button>
            </td>            
          </tr>
          <tr data-customer="Michael Brown" data-phone="555-2345" data-vehicle="BMW 3 Series TUV-3456" data-service="Tire Rotation" data-status="Completed">
            <td>12:00</td>
            <td>Michael Brown</td>
            <td>ABC-1212</td>
            <td>BMW 3 Series</td>
            <td>Tire Rotation</td>
            <td><span class="status completed">Completed</span></td>
            <td>
            <button class="update-btn" onclick="window.location.href='updateApp.php'">Update</button>
            </td>           
          </tr>
          <tr data-customer="Sarah Davis" data-phone="555-6789" data-vehicle="Audi A4 WXY-7890" data-service="Canceled" data-status="Canceled">
            <td>12:30</td>
            <td>Sarah Davis</td>
            <td>ABC-1212</td>
            <td>Audi A4</td>
            <td>Canceled</td>
            <td><span class="status canceled">Canceled</span></td>
            <td>
            <button class="update-btn" onclick="window.location.href='updateApp.php'">Update</button>
            </td>            
          </tr>
        </tbody>
      </table>
    </div>

    <tbody>



  </div>

   <script src="<?= BASE_URL ?>/public/assets/r_js/dayAppointment.js"></script>
   </body>
   </html>