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
</head>
<body>

  <?php include APP_ROOT . '/views/layouts/managersidebar.php'; ?>

  <div class="main">
    <header>
      <h1>Customer Profiles</h1>
    </header>

    <!-- Search Bar -->
    <div class="search-bar">
      <input type="text" placeholder="Search customer by ID...">
      <button>Search</button>
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
          <tr onclick="window.location='<?= $base ?>/manager/customers/10045/history'">
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
          <tr onclick="window.location='<?= $base ?>/manager/customers/10046/history'">
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
          <tr onclick="window.location='<?= $base ?>/manager/customers/10047/history'">
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
          <tr onclick="window.location='<?= $base ?>/manager/customers/10048/history'">
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
          <tr onclick="window.location='<?= $base ?>/manager/customers/10049/history'">
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
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
