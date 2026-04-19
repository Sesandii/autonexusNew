<?php /* Admin view: renders admin-viewinvoices/index page. */ ?>
<?php
$current = 'invoices';
$B = rtrim(BASE_URL, '/');
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AutoNexus - Invoices</title>

  <!-- Shared & Sidebar CSS -->
  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="<?= $B ?>/public/assets/css/admin/invoices/style.css?v=2">

  <!-- Icons -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body>
  <?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

  <main class="main-content">
    <section class="dash-wrap invoices-page">
      <header class="topbar">
        <div>
          <h1 class="page-title">Invoices</h1>
          <p class="subtitle">Track billing totals, payment status, and invoice records.</p>
        </div>
        <a href="<?= $B ?>/admin/admin-viewinvoices/create" class="add-btn btn-create-invoice">
          <i class="fa-solid fa-plus"></i>
          <span>Create New Invoice</span>
        </a>
      </header>

      <div class="kpi-grid">
        <article class="kpi-card">
          <div class="kpi-icon"><i class="fa-solid fa-sack-dollar"></i></div>
          <div class="kpi-meta">
            <h3>Total Revenue</h3>
            <p class="kpi-value">Rs.<?= number_format((float) $summary['total'], 2) ?></p>
          </div>
        </article>

        <article class="kpi-card">
          <div class="kpi-icon"><i class="fa-solid fa-circle-check"></i></div>
          <div class="kpi-meta">
            <h3>Paid</h3>
            <p class="kpi-value">Rs.<?= number_format((float) $summary['paid'], 2) ?></p>
          </div>
        </article>

        <article class="kpi-card">
          <div class="kpi-icon"><i class="fa-solid fa-hourglass-half"></i></div>
          <div class="kpi-meta">
            <h3>Pending</h3>
            <p class="kpi-value">Rs.<?= number_format((float) $summary['pending'], 2) ?></p>
          </div>
        </article>

        <article class="kpi-card">
          <div class="kpi-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
          <div class="kpi-meta">
            <h3>Outstanding</h3>
            <p class="kpi-value">Rs.<?= number_format((float) max(($summary['pending'] ?? 0), 0), 2) ?></p>
          </div>
        </article>
      </div>

      <div class="filters-panel">
        <h3><i class="fa-solid fa-filter"></i> Search & Filters</h3>
        <form class="filters" onsubmit="return false;">
          <div>
            <label><i class="fa-solid fa-search"></i> Search</label>
            <input id="invoiceSearch" type="text" placeholder="Invoice number, customer, service...">
          </div>
          <div>
            <label><i class="fa-solid fa-circle"></i> Status</label>
            <select id="invoiceStatusFilter">
              <option value="">All Status</option>
              <option value="paid">Paid</option>
              <option value="pending">Pending</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>
          <div>
            <label><i class="fa-solid fa-calendar-days"></i> Date</label>
            <input id="invoiceDateFilter" type="date">
          </div>
        </form>
      </div>

      <div class="panel">
        <div class="panel-head">
          <h2><i class="fa-solid fa-file-invoice-dollar"></i> All Invoices</h2>
        </div>
        <div class="table-wrap">
          <table class="table">
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
              <?php if (empty($invoices)): ?>
                <tr>
                  <td colspan="7" class="empty-row"><i class="fa-regular fa-face-frown"></i> No invoices found.</td>
                </tr>
              <?php else: ?>
                <?php foreach ($invoices as $inv): ?>
                  <?php $status = strtolower((string) ($inv['status'] ?? 'pending')); ?>
                  <tr data-status="<?= htmlspecialchars($status) ?>"
                    data-date="<?= htmlspecialchars(substr((string) ($inv['issued_at'] ?? ''), 0, 10)) ?>">
                    <td>
                      <strong><?= htmlspecialchars((string) $inv['invoice_no']) ?></strong>
                    </td>
                    <td><?= htmlspecialchars(trim(($inv['first_name'] ?? '') . ' ' . ($inv['last_name'] ?? ''))) ?></td>
                    <td><?= htmlspecialchars((string) ($inv['service_name'] ?? '-')) ?></td>
                    <td>Rs.<?= number_format((float) ($inv['grand_total'] ?? 0), 2) ?></td>
                    <td><?= date('M d, Y', strtotime((string) $inv['issued_at'])) ?></td>
                    <td>
                      <span class="status-pill status--<?= htmlspecialchars($status) ?>">
                        <span class="dot"></span>
                        <?= ucfirst($status) ?>
                      </span>
                    </td>
                    <td>
                      <div class="table-actions">
                        <a class="chip-btn chip-dark"
                          href="<?= $B ?>/admin/admin-viewinvoices/show?id=<?= (int) $inv['invoice_id'] ?>">
                          <i class="fa-regular fa-eye"></i>
                          <span>View</span>
                        </a>
                        <a class="chip-btn chip-light"
                          href="<?= $B ?>/admin/admin-viewinvoices/download?id=<?= (int) $inv['invoice_id'] ?>">
                          <i class="fa-solid fa-file-arrow-down"></i>
                          <span>Download</span>
                        </a>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </section>
  </main>

  <script>
    const searchEl = document.getElementById('invoiceSearch');
    const statusEl = document.getElementById('invoiceStatusFilter');
    const dateEl = document.getElementById('invoiceDateFilter');
    const rows = Array.from(document.querySelectorAll('#invoiceTable tr[data-status]'));

    function applyFilters() {
      const q = (searchEl?.value || '').trim().toLowerCase();
      const wantedStatus = (statusEl?.value || '').trim().toLowerCase();
      const wantedDate = (dateEl?.value || '').trim();

      rows.forEach((row) => {
        const textMatch = q === '' || row.innerText.toLowerCase().includes(q);
        const statusMatch = wantedStatus === '' || (row.dataset.status || '') === wantedStatus;
        const dateMatch = wantedDate === '' || (row.dataset.date || '') === wantedDate;
        row.style.display = textMatch && statusMatch && dateMatch ? '' : 'none';
      });
    }

    searchEl?.addEventListener('input', applyFilters);
    statusEl?.addEventListener('change', applyFilters);
    dateEl?.addEventListener('change', applyFilters);
  </script>

</body>

</html>