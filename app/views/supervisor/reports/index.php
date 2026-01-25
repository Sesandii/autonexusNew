<?php $base = rtrim(BASE_URL, '/'); ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Reports</title>
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/supervisor/forms.css">
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/supervisor/style-workorders.css">
</head>
<body>

<?php include __DIR__ . '/../partials/sidebar.php'; ?>

<div class="container">

  <div class="page-header">
    <div>
      <h1>Job Reports</h1>
      <p class="subtitle">Reports created for completed work orders.</p>
    </div>
      <a class="btn primary" href="<?= $base ?>/supervisor/reports/create">
  Create Report
</a>


  </div>

  <?php if (!empty($message)): ?>
    <div class="toast <?= htmlspecialchars($message['type']) ?>">
      <?= htmlspecialchars($message['text']) ?>
    </div>
  <?php endif; ?>
  <div class="filters-row">
  <input type="text" id="searchInput" placeholder="Search report, customer, vehicle..." />

  <select id="statusFilter">
    <option value="">All Status</option>
    <option value="draft">Draft</option>
    <option value="submitted">Submitted</option>
  </select>

  <select id="mechanicFilter">
    <option value="">All Mechanics</option>
    <?php foreach (array_unique(array_column($reports, 'mechanic_code')) as $m): ?>
      <?php if ($m): ?>
        <option value="<?= htmlspecialchars($m) ?>">
          <?= htmlspecialchars($m) ?>
        </option>
      <?php endif; ?>
    <?php endforeach; ?>
  </select>

  <input type="date" id="dateFilter" />
</div>

  <table class="workorders">
    <thead>
      <tr>
        <th>Report ID</th>
        <th>Work Order ID</th>
        <th>Vehicle</th>
        <th>Customer</th>
        <th>Mechanic</th>
        <th>Status</th>
        <th>Created At</th>
        <th>Actions</th>
      </tr>
    </thead>

    <tbody>
      <?php if (!empty($reports)): ?>
        <?php foreach ($reports as $r): ?>
          <tr
  data-status="<?= strtolower($r['status']) ?>"
  data-mechanic="<?= strtolower($r['mechanic_code'] ?? '') ?>"
  data-date="<?= date('Y-m-d', strtotime($r['created_at'])) ?>"
>

            <td><?= htmlspecialchars($r['report_id']) ?></td>
            <td><?= htmlspecialchars($r['work_order_id']) ?></td>
            <td><?= htmlspecialchars($r['license_plate'] ?? '-') ?></td>
            <td><?= htmlspecialchars(
        ($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? '')
    ) ?></td>
            <td><?= htmlspecialchars($r['mechanic_code'] ?? 'N/A') ?></td>

            <td>
              <span class="status <?= htmlspecialchars($r['status']) ?>">
                <?= htmlspecialchars(ucfirst($r['status'])) ?>
              </span>
            </td>

            <td><?= htmlspecialchars($r['created_at']) ?></td>
            <td>
    <a class="btn small edit"
       href="<?= rtrim(BASE_URL, '/') ?>/supervisor/reports/view/<?= $r['report_id'] ?>">
        View
    </a>

    <a class="btn small edit"
       href="<?= rtrim(BASE_URL, '/') ?>/supervisor/reports/edit/<?= $r['report_id'] ?>">
        Edit
    </a>

    <form method="post"
          action="<?= rtrim(BASE_URL, '/') ?>/supervisor/reports/delete/<?= $r['report_id'] ?>"
          style="display:inline"
          onsubmit="return confirm('Delete this report?')">
        <button type="submit" class="btn small danger">
            Delete
        </button>
    </form>
</td>

          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="8" style="text-align:center;">
            No reports found.
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>

</div>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchInput");
    const statusFilter = document.getElementById("statusFilter");
    const mechanicFilter = document.getElementById("mechanicFilter");
    const dateFilter = document.getElementById("dateFilter");

    const rows = document.querySelectorAll(".workorders tbody tr");

    function applyFilters() {
        const searchVal = searchInput.value.toLowerCase();
        const statusVal = statusFilter.value;
        const mechanicVal = mechanicFilter.value.toLowerCase();
        const dateVal = dateFilter.value;

        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            const rowStatus = row.dataset.status;
            const rowMechanic = row.dataset.mechanic;
            const rowDate = row.dataset.date;

            const matchSearch = !searchVal || text.includes(searchVal);
            const matchStatus = !statusVal || rowStatus === statusVal;
            const matchMechanic = !mechanicVal || rowMechanic === mechanicVal;
            const matchDate = !dateVal || rowDate === dateVal;

            row.style.display =
                matchSearch && matchStatus && matchMechanic && matchDate
                    ? ""
                    : "none";
        });
    }

    searchInput.addEventListener("keyup", applyFilters);
    statusFilter.addEventListener("change", applyFilters);
    mechanicFilter.addEventListener("change", applyFilters);
    dateFilter.addEventListener("change", applyFilters);
});
</script>

</body>
</html>
