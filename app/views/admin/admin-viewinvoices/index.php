<!-- admin/admin-viewinvoices -->
<?php $current = 'invoices'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AutoNexus - Invoices</title>

   <!-- Shared neutral styles -->
  <link rel="stylesheet" href="<?= rtrim(BASE_URL,'/') ?>/app/views/layouts/admin-shared/management.css">
  <!-- Sidebar styles -->
<link rel="stylesheet" href="<?= rtrim(BASE_URL,'/') ?>/app/views/layouts/admin-sidebar/styles.css">
<link rel="stylesheet" href="<?= rtrim(BASE_URL,'/') ?>/public/assets/css/admin/invoices/style.css">
  <!-- Icons (optional) -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

  
  <link rel="stylesheet" href="style.css">
<style>
    .sidebar { position: fixed; top:0; left:0; width:260px; height:100vh; overflow-y:auto; }
    .main-content { margin-left:260px; padding:30px; background:#fff; min-height:100vh; }
  </style>
  </head>
<body>
  
<?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>
  <main class="main-content">
    

    <div class="page">
      <h2>Invoices Management</h2>

      <div class="summary-cards">
        <div class="card total"><p>Total Revenue</p><h3>$1459.92</h3></div>
        <div class="card paid"><p>Paid</p><h3>$919.95</h3></div>
        <div class="card pending"><p>Pending</p><h3>$439.98</h3></div>
        <div class="card overdue"><p>Overdue</p><h3>$99.99</h3></div>
      </div>

      <div class="filter-bar">
        <input type="text" placeholder="Search by customer or invoice number...">
        <select>
          <option>All Status</option>
          <option>Paid</option>
          <option>Pending</option>
          <option>Overdue</option>
        </select>
        <input type="date">
      </div>

      <table>
        <thead>
          <tr>
            <th>Invoice #</th>
            <th>Customer</th>
            <th>Service</th>
            <th>Amount</th>
            <th>Date</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="invoiceTable">
          <!-- Rows will be populated by JS -->
        </tbody>
      </table>
    </div>
  </main>

  <script src="script.js"></script>
</body>
</html>
