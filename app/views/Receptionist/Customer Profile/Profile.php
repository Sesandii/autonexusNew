<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer Profiles</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/sidebar.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/profile.css">
</head>
<body>
 
<?php include APP_ROOT . '/views/layouts/receptionist-sidebar.php'; ?>

<div class="main">
  <div class="header">
    <h2>Appointments</h2>

    <div class="top-actions">
      <button class="add-btn" onclick="window.location.href='<?= BASE_URL ?>/receptionist/customers/new'">
        + New Customer
      </button>

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
<?php if (!empty($customers)): ?>
    <?php foreach ($customers as $customer): ?>
        <?php 
            // Prepare vehicle display text
            $firstVehicle = (!empty($customer['make']) && !empty($customer['model']) && !empty($customer['year']))
                ? $customer['year'] . ' ' . $customer['make'] . ' ' . $customer['model']
                : 'N/A';

            $vehicleCount = $customer['vehicle_count'] ?? 0;
            $moreText = ($vehicleCount > 1) ? "(+ " . ($vehicleCount - 1) . " more)" : '';
        ?>
        <tr class="clickable-row" data-customer-id="<?= $customer['customer_id'] ?>">

            <td><?= htmlspecialchars($customer['customer_code']) ?></td>
            <td>
                <div class="customer-info">
                    <div class="avatar">👤</div>
                    <div>
                        <strong><?= htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']) ?></strong><br>
                        <span><?= htmlspecialchars($customer['email']) ?></span>
                    </div>
                </div>
            </td>
            <td>
                <?= htmlspecialchars($firstVehicle) ?>
                <?php if ($moreText): ?>
                    <br><small><?= htmlspecialchars($moreText) ?></small>
                <?php endif; ?>
            </td>
            <td><?= 'N/A' // Replace with last visit date if available ?></td>
            <td>
                <span class="status <?= ($customer['status'] === 'active') ? 'active' : 'inactive' ?>">
                    <?= ucfirst($customer['status']) ?>
                </span>
            </td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr>
        <td colspan="6" style="text-align:center;">No customers found.</td>
    </tr>
<?php endif; ?>
</tbody>

    </table>
  </div>
</div>

<script>
    // Set a global variable that your external JS can use
    const BASE_URL = '<?= rtrim(BASE_URL, "/") ?>';
</script>
<script src="<?= BASE_URL ?>/public/assets/js/receptionist/profile.js"></script>

</body>
</html>
