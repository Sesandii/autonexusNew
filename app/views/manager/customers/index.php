<?php $base = rtrim(BASE_URL, '/'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Customer Profiles</title>

  <!-- Remembered sidebar CSS -->
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/manager/sidebar.css">
  <!-- Page CSS (changed from sm_css to css/manager) -->
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/manager/profile.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

  <?php include APP_ROOT . '/views/layouts/managersidebar.php'; ?>

  <div class="main">
     <div class="header">
  <h2>Customers</h2>

  <div class="top-actions">
    <!-- Create New Appointment Button -->
    <button class="add-btn" onclick="window.location.href='<?= $base ?>/manager/customers/create'">
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
</tr>
        </thead>
        <tbody>
          <tr onclick="window.location='<?= $base ?>/manager/customers/newCustomer'">
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
          <tr onclick="window.location='<?= $base ?>/manager/customers/newCustomer'">
            <td>CUS10046</td>
            <td>
              <div class="customer-info">
                <div class="avatar">👤</div>
                <div>
                  <strong>Sarah De Silva</strong><br>
                  <span>sarah.s@example.com</span>
                </div>
              </div>
            </td>
            <td>2020 Honda Accord</td>
            <td>June 5, 2023</td>
            <td><span class="status active">Active</span></td>
          </tr>
         <tr onclick="window.location='<?= $base ?>/manager/customers/newCustomer'">
            <td>CUS10047</td>
            <td>
              <div class="customer-info">
                <div class="avatar">👤</div>
                <div>
                  <strong>Danush Jay</strong><br>
                  <span>danujay@example.com</span>
                </div>
              </div>
            </td>
            <td>2018 Ford Fusion</td>
            <td>May 28, 2023</td>
            <td><span class="status inactive">Inactive</span></td>
          </tr>
          <tr>
            <td>CUS10048</td>
            <td>
              <div class="customer-info">
                <div class="avatar">👤</div>
                <div>
                  <strong>Nathasha Davis</strong><br>
                  <span>ndavis@example.com</span>
                </div>
              </div>
            </td>
            <td>2021 Chevrolet Malibu</td>
            <td>June 12, 2023</td>
            <td><span class="status active">Active</span></td>
          </tr>
          <tr>
            <td>CUS10049</td>
            <td>
              <div class="customer-info">
                <div class="avatar">👤</div>
                <div>
                  <strong>Ruwan Perera</strong><br>
                  <span>rperp@example.com</span>
                </div>
              </div>
            </td>
            <td>2017 Nissan Altima</td>
            <td>April 30, 2023</td>
            <td><span class="status active">Active</span></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
