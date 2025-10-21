<?php $current = 'invoices'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AutoNexus - Invoices</title>

  <!-- Shared & Sidebar CSS -->
  <link rel="stylesheet" href="<?= rtrim(BASE_URL,'/') ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="<?= rtrim(BASE_URL,'/') ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="<?= rtrim(BASE_URL,'/') ?>/public/assets/css/admin/invoices/style.css">

  <!-- Icons -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
  <?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

  <main class="main-content">
    <div class="page">
      <h2>Invoices Management</h2>

      <!-- Summary cards -->
      <div class="summary-cards">
        <div class="card total"><p>Total Revenue</p><h3>$1459.92</h3></div>
        <div class="card paid"><p>Paid</p><h3>$919.95</h3></div>
        <div class="card pending"><p>Pending</p><h3>$439.98</h3></div>
        <div class="card overdue"><p>Overdue</p><h3>$99.99</h3></div>
      </div>

      <!-- Filters -->
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

      <!-- Table -->
      <div class="table-wrap">
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
          <tbody id="invoiceTable"></tbody>
        </table>
      </div>
    </div>
  </main>

  <!-- JS for dummy invoices -->
  <script>
  const invoices = [
    { id: "INV-001", customer: "John Smith", service: "Oil Change & Filter Replacement", amount: "$89.99", date: "Aug 10, 2025", status: "Paid" },
    { id: "INV-002", customer: "Sarah Williams", service: "Brake Pad Replacement", amount: "$249.99", date: "Aug 9, 2025", status: "Pending" },
    { id: "INV-003", customer: "Michael Johnson", service: "Tire Rotation & Alignment", amount: "$129.99", date: "Aug 8, 2025", status: "Paid" },
    { id: "INV-004", customer: "Emily Davis", service: "Full Service", amount: "$349.99", date: "Aug 7, 2025", status: "Paid" },
    { id: "INV-005", customer: "Robert Brown", service: "Engine Diagnostic", amount: "$99.99", date: "Aug 6, 2025", status: "Overdue" },
    { id: "INV-006", customer: "Lisa Chen", service: "Transmission Fluid Change", amount: "$149.99", date: "Aug 5, 2025", status: "Paid" },
    { id: "INV-007", customer: "David Wilson", service: "Battery Replacement", amount: "$189.99", date: "Aug 4, 2025", status: "Pending" },
  ];

  const tableBody = document.getElementById('invoiceTable');

  invoices.forEach(inv => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${inv.id}</td>
      <td>${inv.customer}</td>
      <td>${inv.service}</td>
      <td>${inv.amount}</td>
      <td>${inv.date}</td>
      <td><span class="status ${inv.status}">${inv.status}</span></td>
      <td class="actions">
        <button class="icon-btn" title="View"><i class="fa-regular fa-eye"></i></button>
        <button class="icon-btn" title="Download"><i class="fa-solid fa-file-arrow-down"></i></button>
        <button class="icon-btn" title="Email"><i class="fa-regular fa-envelope"></i></button>
      </td>
    `;
    tableBody.appendChild(tr);
  });
  </script>
</body>
</html>
