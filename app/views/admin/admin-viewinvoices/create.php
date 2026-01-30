<?php $B = rtrim(BASE_URL,'/'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>Create Invoice</title>

  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="<?= $B ?>/public/assets/css/admin/invoices/create.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

<main class="main-content invoices-main">

  <header class="page-header">
    <div class="page-breadcrumb">
      <span>Admin</span>
      <span>›</span>
      <span>Invoices</span>
      <span>›</span>
      <span>Create</span>
    </div>

    <div class="page-header-main">
      <div class="page-title-wrap">
        <div class="page-icon"><i class="fa-regular fa-file-lines"></i></div>
        <div>
          <h2>Create Invoice</h2>
          <p class="invoices-subtitle">Generate invoices from completed work orders.</p>
        </div>
      </div>

      <span class="page-chip">
        <i class="fa-regular fa-circle-check"></i>
        Completed jobs only
      </span>
    </div>
  </header>

  <section class="invoice-panel">
    <div class="panel-head">
      <div class="panel-title">
        <h3>Eligible Work Orders</h3>
        <p>Select a completed job and generate an invoice.</p>
      </div>

      <div class="panel-tools">
        <div class="filter-input-icon">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input id="searchInput" type="text" placeholder="Search customer / service..." />
        </div>
      </div>
    </div>

    <div class="table-wrap">
      <table id="woTable">
        <thead>
          <tr>
            <th>Customer</th>
            <th>Service</th>
            <th>Completed</th>
            <th class="th-right">Total</th>
            <th class="th-actions">Action</th>
          </tr>
        </thead>
        <tbody>
        <?php if (!empty($workOrders)): ?>
          <?php foreach ($workOrders as $wo): ?>
            <?php
              $cust = trim(($wo['first_name'] ?? '') . ' ' . ($wo['last_name'] ?? ''));
              $service = (string)($wo['service_name'] ?? '');
              $completed = !empty($wo['completed_at']) ? date('M d, Y', strtotime($wo['completed_at'])) : '—';
              $total = (float)($wo['total_cost'] ?? 0);
            ?>
            <tr
              data-customer="<?= htmlspecialchars(strtolower($cust)) ?>"
              data-service="<?= htmlspecialchars(strtolower($service)) ?>"
            >
              <td>
                <div class="cell-strong"><?= htmlspecialchars($cust ?: '—') ?></div>
              </td>
              <td><?= htmlspecialchars($service ?: '—') ?></td>
              <td><?= htmlspecialchars($completed) ?></td>
              <td class="td-right">Rs.<?= number_format($total, 2) ?></td>
              <td class="td-actions">
                <button
                  class="chip-btn chip-btn--dark"
                  type="button"
                  onclick="openForm(<?= (int)$wo['work_order_id'] ?>, <?= (float)$total ?>)"
                >
                  <i class="fa-solid fa-receipt"></i>
                  <span>Create</span>
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="5" class="empty-row">
              <i class="fa-regular fa-folder-open"></i>
              No completed work orders available.
            </td>
          </tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>

  <section class="invoice-form-wrap hidden" id="invoiceFormWrap">
    <form method="post"
          action="<?= $B ?>/admin/admin-viewinvoices/store"
          id="invoiceForm"
          class="invoice-form-card">

      <div class="invoice-form-head">
        <div>
          <h3>Invoice Details</h3>
          <p>Review totals and apply optional discount.</p>
        </div>
        <button type="button" class="icon-close" onclick="closeForm()" aria-label="Close">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>

      <input type="hidden" name="work_order_id" id="workOrderId">

      <div class="form-grid">
        <div class="form-field">
          <label>Total Amount</label>
          <input type="number" name="total_amount" id="totalAmount" readonly>
          <small>Calculated from the selected work order.</small>
        </div>

        <div class="form-field">
          <label>Discount</label>
          <input type="number" name="discount" id="discount" value="0" min="0" step="0.01">
          <small>Optional (Rs.)</small>
        </div>

        <div class="form-field form-field--span2">
          <label>Grand Total</label>
          <input type="number" name="grand_total" id="grandTotal" readonly>
          <small>Total minus discount.</small>
        </div>
      </div>

      <div class="form-actions">
        <button class="btn-primary" type="submit">
          <i class="fa-solid fa-file-invoice"></i>
          <span>Generate Invoice</span>
        </button>

        <button class="btn-secondary" type="button" onclick="closeForm()">
          Cancel
        </button>
      </div>
    </form>
  </section>

</main>

<script>
function openForm(id, total) {
  document.getElementById('invoiceFormWrap').classList.remove('hidden');
  document.getElementById('workOrderId').value = id;

  document.getElementById('totalAmount').value = total.toFixed(2);
  document.getElementById('discount').value = "0";
  document.getElementById('grandTotal').value = total.toFixed(2);

  document.getElementById('discount').focus({preventScroll:true});
}

function closeForm() {
  document.getElementById('invoiceFormWrap').classList.add('hidden');
}

document.getElementById('discount').addEventListener('input', e => {
  const total = parseFloat(document.getElementById('totalAmount').value || 0);
  let discount = parseFloat(e.target.value || 0);

  if (discount < 0) discount = 0;
  if (discount > total) discount = total;

  document.getElementById('grandTotal').value = (total - discount).toFixed(2);
});

document.getElementById('searchInput')?.addEventListener('input', (e) => {
  const term = (e.target.value || '').toLowerCase().trim();
  const rows = document.querySelectorAll('#woTable tbody tr');

  rows.forEach(r => {
    if (r.querySelector('.empty-row')) return;

    const c = r.dataset.customer || '';
    const s = r.dataset.service || '';
    const show = !term || c.includes(term) || s.includes(term);
    r.style.display = show ? '' : 'none';
  });
});
</script>

</body>
</html>
