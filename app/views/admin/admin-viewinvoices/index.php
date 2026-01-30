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

      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
  <div></div>

  <a href="<?= rtrim(BASE_URL,'/') ?>/admin/admin-viewinvoices/create"
     class="btn btn-primary">
     <i class="fa-solid fa-plus"></i> Create New Invoice
  </a>
</div>


      <!-- Summary cards -->
     <div class="summary-cards">
  <div class="card total">
    <p>Total Revenue</p>
    <h3>Rs.<?= number_format($summary['total'], 2) ?></h3>
  </div>

  <div class="card paid">
    <p>Paid</p>
    <h3>Rs.<?= number_format($summary['paid'], 2) ?></h3>
  </div>

  <div class="card pending">
    <p>Pending</p>
    <h3>Rs.<?= number_format($summary['pending'], 2) ?></h3>
  </div>

  <div class="card overdue">
    <p>Overdue</p>
    <h3>Rs.<?= number_format(
        max($summary['pending'] - 0, 0), 
        2
    ) ?></h3>
  </div>
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
         <tbody id="invoiceTable">
<?php foreach ($invoices as $inv): ?>
<tr>
  <td><?= htmlspecialchars($inv['invoice_no']) ?></td>
  <td><?= htmlspecialchars($inv['first_name'].' '.$inv['last_name']) ?></td>
  <td><?= htmlspecialchars($inv['service_name']) ?></td>
  <td>Rs.<?= number_format($inv['grand_total'],2) ?></td>
  <td><?= date('M d, Y', strtotime($inv['issued_at'])) ?></td>
  <td>
    <span class="status <?= htmlspecialchars($inv['status']) ?>">
      <?= ucfirst($inv['status']) ?>
    </span>
  </td>
  <td class="actions">
    <a class="icon-btn"
       href="<?= rtrim(BASE_URL,'/') ?>/admin/admin-viewinvoices/show?id=<?= (int)$inv['invoice_id'] ?>">
       <i class="fa-regular fa-eye"></i>
    </a>
  
    <a class="icon-btn"
   href="<?= rtrim(BASE_URL,'/') ?>/admin/admin-viewinvoices/download?id=<?= (int)$inv['invoice_id'] ?>">
   <i class="fa-solid fa-file-arrow-down"></i>
</a>

<!-- <a class="icon-btn"
   href="<?= rtrim(BASE_URL,'/') ?>/admin/admin-viewinvoices/email?id=<?= (int)$inv['invoice_id'] ?>"
   title="Email Invoice">
   <i class="fa-regular fa-envelope"></i>
</a> -->

    
  </td>
</tr>
<?php endforeach; ?>
</tbody>

        </table>
      </div>
    </div>
  </main>

 <script>
const search = document.querySelector('.filter-bar input');
const rows = document.querySelectorAll('#invoiceTable tr');

search.addEventListener('input', () => {
  const q = search.value.toLowerCase();
  rows.forEach(r => {
    r.style.display = r.innerText.toLowerCase().includes(q) ? '' : 'none';
  });
});
</script>

</body>
</html>
