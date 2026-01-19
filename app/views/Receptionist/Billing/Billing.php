<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Team Schedule - AutoNexus</title>
   <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/sidebar.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/billing.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>

<body>
  
<?php include APP_ROOT . '/views/layouts/receptionist-sidebar.php'; ?>

<div class="main">
<header class="header-bar">
    <h1>Billing & Payments</h1>
    <a href="<?= BASE_URL ?>/receptionist/billing/create" class="create-btn">+ Create Invoice</a>
</header>


<nav class="tab-nav">
  <ul class="tab-list">
    <li class="tab-item active" data-tab="invoice">Invoices</li>
   <!-- <li class="tab-item" data-tab="payment">Process Payments</li>-->
    <li class="tab-item" data-tab="T_history">Transaction History</li>
  </ul>
</nav>

<section id="invoice" class="tab-content active">
 <div class="invoice-list">
  <div class="search-filter">
    <input type="text" placeholder="Search by invoice ID, vehicle number..." class="search-bar">
        <select>
          <option>Paid</option>
          <option>Unpaid</option>
          <option>Partially Paid</option>
        </select>
      </div>
      <table>
        <thead>
          <tr>
            <th>Invoice ID</th>
            <th>Customer</th>
            <th>Vehicle</th>
            <th>Date</th>
            <th>Amount</th>
            <th>Actions</th>
    
          </tr>
        </thead>
</table>
</div>
</section>

<section id="T_history" class="tab-content">
  <div class="T_history">
    <div class="search-filter">
      <input type="text" placeholder="Search by invoice ID, vehicle number..." class="search-bar">
      <select>
        <option>All Transactions</option>
        <option>Last 7 Days</option>
        <option>Last 30 Days</option>
      </select>
    </div>

    <table>
      <thead>
        <tr>
          <th>Transaction ID</th>
          <th>Customer</th>
          <th>Vehicle</th>
          <th>Date</th>
          <th>Amount</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <!-- Transaction data goes here -->
      </tbody>
    </table>
  </div>
</section>



<script src="<?= BASE_URL ?>/public/assets/js/receptionist/billing.js"></script>



</div>
</body>
</html>