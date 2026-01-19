<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Services & Packages- AutoNexus</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/sidebar.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/service-packages.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
 
<?php include APP_ROOT . '/views/layouts/receptionist-sidebar.php'; ?>

  <div class="main">

    <div class="header">
      <h2>Services & Packages</h2>
    </div>
  
  <nav class="tab-nav">
  <ul class="tab-list">
    <li class="tab-item active" data-tab="invoice">Services</li>
   <!-- <li class="tab-item" data-tab="payment">Process Payments</li>-->
    <li class="tab-item" data-tab="T_history">Packages</li>
  </ul>
</nav>

<section id="invoice" class="tab-content active">
    <div class="packages">
      <table>
        <thead>
          <tr>
            <th>Service</th>
            <th>Description</th>
            <th>Duration</th>
            <th>Price</th>
          </tr>
        </thead>
        <tbody>       
         
<?php if (!empty($services)): ?>
    <?php foreach ($services as $row): ?>
        <tr class="clickable-row" onclick="toggleDropdown(this)">
            <td><?= htmlspecialchars($row['name']) ?></td>

            <td>
                <?= htmlspecialchars($row['description']) ?>
                <div class="Service-item hidden">
                    <h4>Service Items</h4>
                    <ul class="dropdown-list">
                        <li>No items added yet</li>
                    </ul>
                </div>
            </td>

            <td><?= htmlspecialchars($row['base_duration_minutes']) ?> min</td>
            <td class="price">Rs.<?= htmlspecialchars($row['default_price']) ?></td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr>
        <td colspan="4">No active services found.</td>
    </tr>
<?php endif; ?>


        </tbody>
      </table>
    </div>
  </div>
  <section>

  <section id="invoice" class="tab-content active">
  <section>


    <script src="<?= BASE_URL ?>/public/assets/js/receptionist/services.js"></script>


</body>
</html>
