<?php
// app/views/manager/viewservices/index.php
$base = rtrim(BASE_URL, '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Service Packages - AutoNexus</title>

  <!-- Remembered sidebar CSS location -->
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/manager/sidebar.css">

  <!-- Page-specific CSS (keep your existing asset path) -->
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/manager/viewservices.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body>

  <!-- Remembered sidebar include -->
  <?php include APP_ROOT . '/views/layouts/managersidebar.php'; ?>

  <div class="main">
    <div class="header">
      <h2>Service Packages</h2>
      <!-- Point to a manager route (you can create later) -->
      <button class="add-btn" id="openModal" onclick="window.location.href='<?= $base ?>/manager/services/create'">+ Add Package</button>
    </div>

    <div class="packages">
      <table>
        <thead>
          <tr>
            <th>Package Name</th>
            <th>Description</th>
            <th>Duration</th>
            <th>Price</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>

          <tr class="clickable-row" onclick="toggleDropdown(this)">
            <td>Full Service</td>
            <td>
              Oil change, filter replacement, basic inspection ...
              <div class="Service-item hidden">
                <h4>Service Item</h4>
                <ul class="dropdown-list">
                  <li>Item 1</li>
                  <li>Item 2</li>
                  <li>Item 3</li>
                </ul>
              </div>
            </td>
            <td>120 min</td>
            <td>$149.99</td>
            <td class="actions">
              <a href="<?= $base ?>/manager/services/1/edit">✎</a> 🗑
            </td>
          </tr>

          <tr class="clickable-row" onclick="toggleDropdown(this)">
            <td>Full Service</td>
            <td>
              Oil change, filter replacement, basic inspection ...
              <div class="Service-item hidden">
                <h4>Service Item</h4>
                <ul class="dropdown-list">
                  <li>Item 1</li>
                  <li>Item 2</li>
                  <li>Item 3</li>
                </ul>
              </div>
            </td>
            <td>120 min</td>
            <td>$149.99</td>
            <td class="actions">
              <a href="<?= $base ?>/manager/services/2/edit">✎</a> 🗑
            </td>
          </tr>

          <tr class="clickable-row" onclick="toggleDropdown(this)">
            <td>Full Service</td>
            <td>
              Oil change, filter replacement, basic inspection ...
              <div class="Service-item hidden">
                <h4>Service Item</h4>
                <ul class="dropdown-list">
                  <li>Item 1</li>
                  <li>Item 2</li>
                  <li>Item 3</li>
                </ul>
              </div>
            </td>
            <td>120 min</td>
            <td>$149.99</td>
            <td class="actions">
              <a href="<?= $base ?>/manager/services/3/edit">✎</a> 🗑
            </td>
          </tr>

          <tr class="clickable-row" onclick="toggleDropdown(this)">
            <td>Brake Service</td>
            <td>
              Complete brake system
              <div class="Service-item hidden">
                <h4>Service Item</h4>
                <ul class="dropdown-list">
                  <li>Item 1</li>
                  <li>Item 2</li>
                  <li>Item 3</li>
                </ul>
              </div>
            </td>
            <td>120 min</td>
            <td>$199.99</td>
            <td class="actions">
              <a href="<?= $base ?>/manager/services/4/edit">✎</a> 🗑
            </td>
          </tr>

        </tbody>
      </table>
    </div>
  </div>

  <!-- Keep your existing JS (toggle handlers) -->
  <script src="<?= $base ?>/public/assets/sm_js/Services.js"></script>
  <script>
    // Minimal defensive init in case Services.js isn’t loaded yet
    window.toggleDropdown = window.toggleDropdown || function(row) {
      const details = row.querySelector('.Service-item');
      if (details) details.classList.toggle('hidden');
    };
  </script>
</body>
</html>
