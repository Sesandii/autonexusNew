<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer Profiles</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/r_css/sidebar.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/r_css/individualDetails.css">
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
  <li><a href="/autonexus/receptionist/appointments">Appointments</a></li>
  <li><a href="/autonexus/receptionist/service">Service & Packages</a></li>
  <li><a href="/autonexus/receptionist/complaints">Complaints</a></li>
  <li><a href="/autonexus/receptionist/billing">Billing & Payments</a></li>
  <li class="active"><a href="/autonexus/receptionist/customers">Customer Profiles</a></li>
  <li><a href="<?= rtrim(BASE_URL, '/') ?>/logout">Sign Out</a></li>
</ul>
  </div>

  <div class="main">
  <div class="header">
  <h2>Customer Profile</h2>

  <div class="top-actions">
    <!-- Create New Appointment Button -->
    <button class="update-btn" onclick="window.location.href='<?= BASE_URL ?>/receptionist/customers/new'">
  Update
</button>

  </div>
  </div>

    <div class="profile-card">
      <div class="avatar-name">
        <div class="avatar"></div>
        <div>
          <h3>Michael Johnson</h3>
          <p>Customer ID: <span class="id">CUS10045</span></p>
        </div>
      </div>
      <div class="profile-meta">
        <p><span class="badge active">Active</span></p>
        <p>Total Visits: <strong>8</strong></p>
        <p>Customer Since: May 15, 2021</p>
        <p>Total Spent: <strong>$2,450</strong></p>
      </div>
    </div>

<nav class="tab-nav">
  <ul class="tab-list">
    <li class="tab-item active" data-tab="overview">Customer Overview</li>
    <li class="tab-item" data-tab="vehicle">Vehicle Information</li>
    <li class="tab-item" data-tab="history">Service History</li>
  </ul>
</nav>

<!-- Overview -->
<div id="overview" class="tab-content active">
  <h3>Contact Information</h3>
  <p>📞 (555) 123-4567</p>
  <p>📧 michael.johnson@example.com</p>
  <p>📍 123 Main St, Anytown, CA 94321</p>

  <h3 style="margin-top:20px;">Vehicle Summary</h3>
  <p>🚗 2019 Toyota Camry</p>
  <p>Color: Silver</p>
  <p>License: ABC123</p>
  <p>VIN: 1HGBH41JXMN109186</p>
</div>

<!-- Vehicle Info -->
<div id="vehicle" class="tab-content">
  <h3>Vehicle Details</h3>
  <div class="vehicle-card">
    <p><b>2019 Toyota Camry</b></p>
    <p>Silver</p>
  </div>
  <ul class="vehicle-details">
    <li>Make: Toyota</li>
    <li>Model: Camry</li>
    <li>Year: 2019</li>
    <li>Color: Silver</li>
    <li>License Plate: ABC123</li>
    <li>VIN: 1HGBH41JXMN109186</li>
  </ul>
</div>

<!-- Service History -->
<div id="history" class="tab-content">
  <h3>Service History</h3>
  <p class="last-visit">Last visit: June 10, 2023</p>
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
      <tr>
        <td>SRV5001</td>
        <td>June 10, 2023</td>
        <td>Oil Change & Tire Rotation</td>
        <td>David Lee</td>
        <td>$89.99</td>
      </tr>
      <tr>
        <td>SRV4852</td>
        <td>April 22, 2023</td>
        <td>Brake Inspection & Replacement</td>
        <td>John Smith</td>
        <td>$350.00</td>
      </tr>
      <tr>
        <td>SRV4720</td>
        <td>February 15, 2023</td>
        <td>Battery Replacement</td>
        <td>Maria Garcia</td>
        <td>$175.50</td>
      </tr>
      <tr>
        <td>SRV4601</td>
        <td>December 3, 2022</td>
        <td>Seasonal Maintenance</td>
        <td>David Lee</td>
        <td>$129.99</td>
      </tr>
      <tr>
        <td>SRV4512</td>
        <td>October 18, 2022</td>
        <td>Air Conditioning Service</td>
        <td>Robert Chen</td>
        <td>$220.00</td>
      </tr>
    </tbody>
  </table>
  <p class="total">Total Spent: <b>$2,450</b></p>
</div>

<tbody>
<?php foreach ($customers as $customer): ?>
<tr class="clickable-row" onclick="window.location.href='<?= BASE_URL ?>/receptionist/customers/<?= $customer['id'] ?>'">
    <td><?= $customer['id'] ?></td>
    <td>
        <div class="customer-info">
            <div class="avatar">👤</div>
            <div>
                <strong><?= $customer['name'] ?></strong><br>
                <span><?= $customer['email'] ?></span>
            </div>
        </div>
    </td>
    <td><?= $customer['vehicle'] ?></td>
    <td><?= $customer['last_visit'] ?></td>
    <td><span class="status <?= strtolower($customer['status']) ?>"><?= $customer['status'] ?></span></td>
</tr>
<?php endforeach; ?>
</tbody>


<script src="<?= BASE_URL ?>/public/assets/r_js/individualDetails.js"></script>

</body>
</html>
