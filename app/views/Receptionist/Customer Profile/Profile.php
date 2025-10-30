<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer Profiles</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/r_css/sidebar.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/r_css/Profile.css">
</head>
<body>
  <!-- Sidebar -->
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

  <!-- Main Content -->
  <div class="main">
  
  <div class="header">
  <h2>Appointments</h2>

  <div class="top-actions">
    <!-- Create New Appointment Button -->
   <button class="add-btn" onclick="window.location.href='<?= BASE_URL ?>/receptionist/customers/new'">
    + New Customer
</button>


    <!-- Search Appointment Bar -->
    <div class="search-bar">
      <input type="text" id="searchInput" placeholder="Search Customer...." />
      <button id="searchBtn">🔍</button>
    </div>
  </div>
</div>

    <!-- Customer List -->
    <div class="card customer-list">
      <div class="card-header">
        <h3>Customer List</h3>
        <select>
          <option>All Customers</option>
          <option>Active</option>
          <option>Inactive</option>
        </select>
      </div>
      <table>
        <thead>
          <tr>
            <th>Customer ID</th>
            <th>Customer</th>
            <th>Vehicle</th>
            <th>Last Visit</th>
            <th>Status</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
  <tr onclick="window.location.href='<?= BASE_URL ?>/receptionist/customers/details'" style="cursor:pointer;">
    <td>CUS10045</td>
    <td>
      <div class="customer-info">
        <div class="avatar">👤</div>
        <div>
          <strong>Michael Johnson</strong><br>
          <span>michael.johnson@example.com</span>
        </div>
      </div>
    </td>
    <td>2019 Toyota Camry</td>
    <td>June 10, 2023</td>
    <td><span class="status active">Active</span></td>
  </tr>

  <tr onclick="window.location.href='<?= BASE_URL ?>/receptionist/customers/details'" style="cursor:pointer;">
    <td>CUS10046</td>
    <td>
      <div class="customer-info">
        <div class="avatar">👤</div>
        <div>
          <strong>Sarah Williams</strong><br>
          <span>sarah.w@example.com</span>
        </div>
      </div>
    </td>
    <td>2020 Honda Accord</td>
    <td>June 5, 2023</td>
    <td><span class="status active">Active</span></td>
  </tr>

  <!-- Repeat for other rows -->
<tr class="clickable-row" data-href="individualDetails.php">
            <td>CUS10047</td>
            <td>
              <div class="customer-info">
                <div class="avatar">👤</div>
                <div>
                  <strong>David Brown</strong><br>
                  <span>dbrown@example.com</span>
                </div>
              </div>
            </td>
            <td>2018 Ford Fusion</td>
            <td>May 28, 2023</td>
            <td><span class="status inactive">Inactive</span></td>
          </tr>
           <tr onclick="window.location.href='<?= BASE_URL ?>/receptionist/customers/details'" style="cursor:pointer;">
            <td>CUS10048</td>
            <td>
              <div class="customer-info">
                <div class="avatar">👤</div>
                <div>
                  <strong>Jennifer Davis</strong><br>
                  <span>jdavis@example.com</span>
                </div>
              </div>
            </td>
            <td>2021 Chevrolet Malibu</td>
            <td>June 12, 2023</td>
            <td><span class="status active">Active</span></td>
          </tr>
           <tr onclick="window.location.href='<?= BASE_URL ?>/receptionist/customers/details'" style="cursor:pointer;">
            <td>CUS10049</td>
            <td>
              <div class="customer-info">
                <div class="avatar">👤</div>
                <div>
                  <strong>Robert Wilson</strong><br>
                  <span>rwilson@example.com</span>
                </div>
              </div>
            </td>
            <td>2017 Nissan Altima</td>
            <td>April 30, 2023</td>
            <td><span class="status active">Active</span></td>
          </tr>
           <tr onclick="window.location.href='<?= BASE_URL ?>/receptionist/customers/details'" style="cursor:pointer;">
        </tbody>
      </table>
    </div>
  </div>

  <script src="<?= BASE_URL ?>/public/assets/r_js/Profile.js"></script>

</body>
</html>
