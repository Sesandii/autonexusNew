<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>New Invoice - AutoNexus</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/sidebar.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/createInvoice.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>

<?php include APP_ROOT . '/views/layouts/receptionist-sidebar.php'; ?>

<div class="main">
  <div class="details-section">

<h2>Create Invoice</h2>

<div class="cards">
  <?php if (empty($orders)): ?>
    <p>No completed work orders available.</p>
  <?php endif; ?>

  <?php foreach ($orders as $order): ?>
    <div class="card"
        onclick="window.location.href='<?= BASE_URL ?>/receptionist/billing/invoice/<?= $order['work_order_id'] ?>'">

      <span class="icon">🧾</span>
<h2>
  Vehicle: <?= htmlspecialchars($order['vehicle_no']) ?>
</h2>

<h4>
  <?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?>
</h4>


      <p class="green">
        Rs. <?= number_format($order['total_cost'], 2) ?>
      </p>

      <small>
        Work Order #<?= $order['work_order_id'] ?>
      </small>

    </div>
  <?php endforeach; ?>
</div>

<!-- Define BASE_URL for JS -->
<script>
  const BASE_URL = "<?= BASE_URL ?>";
</script>

<script src="<?= BASE_URL ?>/public/assets/js/receptionist/createInvoice.js"></script>

</body>
</html>
