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
<div class="breadcrumb-text">
    Supervisor <span class="sep">&gt;</span> 
    Reports <span class="sep">&gt;</span> 
    Vehicle Report <span class="sep"></span> 
  </div>

  <div class="page-header">
    <div>
      <h1>Vehicle Reports</h1>
      <p class="subtitle">Reports created for completed work orders.</p>
    </div>
    <div class="header-actions">
    <button onclick="history.back()" class="btn secondary">Back</button>
      <a class="btn primary" href="<?= $base ?>/supervisor/reports/create">
  Create Report
</a>
</div>
  </div>

  <?php if (!empty($message)): ?>
    <div class="toast <?= htmlspecialchars($message['type']) ?>">
      <?= htmlspecialchars($message['text']) ?>
    </div>
  <?php endif; ?>
  <div class="filters-row">
  <input type="text" id="searchInput" placeholder="Search Service, Vehicle, Customer..." />

  <select id="statusFilter">
    <option value="">All Status</option>
    <option value="draft">Draft</option>
    <option value="submitted">Submitted</option>
  </select>

  <select id="ownerFilter">
    <option value="">All Reports</option>
    <option value="mine">My Reports</option>
    <option value="others">Other Supervisors</option>
  </select>

  <select id="mechanicFilter">
    <option value="">All Mechanics</option>
    <?php foreach (array_unique(array_column($reports, 'mechanic_code')) as $m): ?>
      <?php if ($m): ?>
        <option value="<?= htmlspecialchars($m) ?>"><?= htmlspecialchars($m) ?></option>
      <?php endif; ?>
    <?php endforeach; ?>
  </select>

  <button id="resetFilters" class="btn small">Reset</button>
</div>

  <table class="workorders">
    <thead>
      <tr>
        <th>Service</th>
        <th>Vehicle</th>
        <th>Customer</th>
        <th>Mechanic</th>
        <th>Status</th>
        <th>Created At</th>
        <th>Supervisor</th>
        <th>Actions</th>
      </tr>
    </thead>

    <tbody>
  <?php if (!empty($reports)): ?>
    <?php 
    $currentSupervisorId = $currentSupervisorId ?? 0;
    ?>
    
    <?php foreach ($reports as $r): ?>
  <?php 
    $reportOwnerId = $r['supervisor_id'] ?? null;

    $isOwner = ($reportOwnerId !== null && (int)$reportOwnerId === (int)$currentSupervisorId);
  ?>
  <tr data-status="<?= strtolower($r['status']) ?>"
    data-mechanic="<?= strtolower($r['mechanic_code'] ?? '') ?>"
    data-owner="<?= $isOwner ? 'mine' : 'others' ?>" 
    data-service="<?= strtolower($r['name']) ?>"
    data-vehicle="<?= strtolower($r['vehicle'] ?? '') ?>"
    data-customer="<?= strtolower($r['customer_name'] ?? '') ?>">

    <td><?= htmlspecialchars($r['name']) ?></td>
    <td><?= htmlspecialchars($r['vehicle'] ?? '-') ?></td>
    <td><?= htmlspecialchars($r['customer_name'] ?? '') ?></td>
    <td><?= htmlspecialchars($r['mechanic_code'] ?? 'N/A') ?></td>
    <td><span class="status <?= htmlspecialchars($r['status']) ?>"><?= ucfirst($r['status']) ?></span></td>
    <td><?= htmlspecialchars($r['created_at']) ?></td>
    <td><?= htmlspecialchars($r['supervisor_code']) ?></td>

    <td>
    <a class="btn small" href="<?= $base ?>/supervisor/reports/view/<?= $r['report_id'] ?>">View</a>

    <?php if ($isOwner): ?>
        <a class="btn small edit" href="<?= $base ?>/supervisor/reports/edit/<?= $r['report_id'] ?>">Edit</a>
        <form method="post" action="<?= $base ?>/supervisor/reports/delete/<?= $r['report_id'] ?>" class="delete-form" style="display:inline">
            <button type="submit" class="btn small danger">Delete</button>
        </form>
        <?php if ($r['status'] === 'submitted'): ?>
          <a class="btn small download" 
   href="<?= $base ?>/supervisor/reports/download/<?= $r['report_id'] ?>?format=pdf" 
   title="Download PDF">
   PDF
</a>
    <?php endif; ?>
    <?php else: ?>
        <span style="color:#888; font-size:13px;" title="ID: <?= $reportOwnerId ?? 'Missing' ?>">Restricted</span>
    <?php endif; ?>
</td>
  </tr>
<?php endforeach; ?>
  <?php else: ?>
    <?php endif; ?>
</tbody>
  </table>

</div>

<div id="deleteModal" class="modal-overlay">
  <div class="modal-box">
    <h3>Confirm Deletion</h3>
    <p>Are you sure you want to delete this report?</p>
    <div class="modal-actions">
      <button id="cancelDelete" class="btn small">Cancel</button>
      <button id="confirmDelete" class="btn small danger">Delete</button>
    </div>
  </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchInput");
    const statusFilter = document.getElementById("statusFilter");
    const mechanicFilter = document.getElementById("mechanicFilter");
    const ownerFilter = document.getElementById("ownerFilter");
    const resetBtn = document.getElementById("resetFilters");

    const rows = document.querySelectorAll(".workorders tbody tr");

    function applyFilters() {
        const searchVal = searchInput.value.toLowerCase().trim();
        const statusVal = statusFilter.value;
        const mechanicVal = mechanicFilter.value.toLowerCase();
        const ownerVal = ownerFilter.value;

        rows.forEach(row => {
            const service = row.dataset.service || "";
            const vehicle = row.dataset.vehicle || "";
            const customer = row.dataset.customer || "";
            
            const rowStatus = row.dataset.status;
            const rowMechanic = row.dataset.mechanic;
            const rowOwner = row.dataset.owner;

            const matchSearch = !searchVal || 
                                service.includes(searchVal) || 
                                vehicle.includes(searchVal) || 
                                customer.includes(searchVal);

            const matchStatus = !statusVal || rowStatus === statusVal;
            const matchMechanic = !mechanicVal || rowMechanic === mechanicVal;
            const matchOwner = !ownerVal || rowOwner === ownerVal;

            row.style.display = (matchSearch && matchStatus && matchMechanic && matchOwner) ? "" : "none";
        });
    }

    searchInput.addEventListener("keyup", applyFilters);
    statusFilter.addEventListener("change", applyFilters);
    mechanicFilter.addEventListener("change", applyFilters);
    ownerFilter.addEventListener("change", applyFilters);

    resetBtn.addEventListener("click", function () {
        searchInput.value = "";
        statusFilter.value = "";
        mechanicFilter.value = "";
        ownerFilter.value = "";
        applyFilters();
    });
});

let formToDelete = null;

document.querySelectorAll('.delete-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        formToDelete = this;
        document.getElementById('deleteModal').style.display = 'flex';
    });
});

document.getElementById('cancelDelete').addEventListener('click', function() {
    document.getElementById('deleteModal').style.display = 'none';
    formToDelete = null;
});

document.getElementById('confirmDelete').addEventListener('click', function() {
    if (formToDelete) {
        formToDelete.submit();
    }
});
</script>

</body>
</html>
