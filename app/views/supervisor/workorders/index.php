<?php $base = rtrim(BASE_URL,'/'); ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Work Orders</title>
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/supervisor/style-workorders.css">
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/supervisor/forms.css">
</head>
<body>

<?php include __DIR__ . '/../partials/sidebar.php'; ?>

<div class="container">
  <div class="page-header">
    <div class="header">
      <h1>Work Orders</h1>
      <p class="subtitle">All work orders with their appointment's service.</p>
    </div>
    <a class="btn primary" href="<?= $base ?>/supervisor/workorders/create">Add Work Order</a>
    

  </div>

  <?php if (!empty($message)): ?>
    <div class="toast <?= htmlspecialchars($message['type']) ?>">
      <?= htmlspecialchars($message['text']) ?>
    </div>
  <?php endif; ?>

  <div class="table-filters">
  <input
    type="text"
    id="idFilter"
    placeholder="Search by Work Order ID, Service"
    class="filter-input"
  >

  <select id="serviceFilter">
    <option value="">All Services</option>
    <?php
      $services = array_unique(array_column($workOrders, 'service_name'));
      foreach ($services as $service):
    ?>
      <option value="<?= strtolower($service) ?>">
        <?= htmlspecialchars($service) ?>
      </option>
    <?php endforeach; ?>
  </select>

  <select id="mechanicFilter">
    <option value="">All Mechanics</option>
    <?php
      $mechanics = array_unique(array_column($workOrders, 'mechanic_code'));
      foreach ($mechanics as $mech):
    ?>
      <option value="<?= strtolower($mech ?: 'unassigned') ?>">
        <?= htmlspecialchars($mech ?: 'Unassigned') ?>
      </option>
    <?php endforeach; ?>
  </select>

  <select id="statusFilter">
    <option value="">All Statuses</option>
    <option value="open">Open</option>
    <option value="in_progress">In Progress</option>
    <option value="completed">Completed</option>
  </select>
</div>
  <table class="workorders">
    <thead>
      <tr>
        <th>ID</th>
        <th>Appointment</th>
        <th>Service</th>
        <th>Mechanic</th>
        <th>Expected Completion</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($workOrders as $w): ?>
        <?php
          // Calculate expected completion using DateTime + DateInterval
          $expectedEnd = '-';
          if (!empty($w['started_at']) && !empty($w['base_duration_minutes'])) {
              $dt = new \DateTime($w['started_at']); // DB value as-is
              $dt->add(new \DateInterval('PT' . (int)$w['base_duration_minutes'] . 'M'));
              $expectedEnd = $dt->format('Y-m-d H:i:s'); // full timestamp
          }
        ?>
        <tr
  data-id="<?= $w['work_order_id'] ?>"
  data-service="<?= strtolower($w['service_name'] ?? '') ?>"
  data-mechanic="<?= strtolower($w['mechanic_code'] ?? 'unassigned') ?>"
  data-status="<?= strtolower($w['status']) ?>"
>

          <td><?= htmlspecialchars($w['work_order_id']) ?></td>
          <td><?= htmlspecialchars(($w['appointment_date'] ?? '') . ' ' . ($w['appointment_time'] ?? '')) ?></td>
          <td><?= htmlspecialchars($w['service_name'] ?? '') ?></td>
          <td><?= htmlspecialchars($w['mechanic_code'] ?? 'Unassigned') ?></td>
          <td class="countdown" data-end="<?= htmlspecialchars($expectedEnd) ?>">
    <?= $expectedEnd === '-' ? '-' : 'Loading...' ?>
</td>

          <td><span class="status <?= htmlspecialchars($w['status']) ?>"><?= htmlspecialchars($w['status']) ?></span></td>
          <td>
            <a class="btn small" href="<?= $base ?>/supervisor/workorders/<?= $w['work_order_id'] ?>">View</a>
            <a class="btn small edit" href="<?= $base ?>/supervisor/workorders/<?= $w['work_order_id'] ?>/edit">Edit</a>
            <form method="post" action="<?= $base ?>/supervisor/workorders/<?= $w['work_order_id'] ?>/delete" style="display:inline" onsubmit="return confirm('Delete this work order?')">
              <button type="submit" class="btn small danger">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

</div>
<script>
// Countdown timer for each row
function updateTimers() {
    const timers = document.querySelectorAll('.countdown');
    const now = new Date();

    timers.forEach(td => {
        const endTimeStr = td.dataset.end;
        if (!endTimeStr || endTimeStr === '-') {
            td.textContent = '-';
            return;
        }

        const endTime = new Date(endTimeStr);
        let diff = Math.floor((endTime - now) / 1000); // in seconds

        if (diff <= 0) {
            td.textContent = "00:00";
        } else {
            const hours = Math.floor(diff / 3600);
            diff %= 3600;
            const minutes = Math.floor(diff / 60);
            const seconds = diff % 60;

            td.textContent = 
                (hours > 0 ? hours + 'h ' : '') + 
                (minutes > 0 ? minutes + 'm ' : '') + 
                seconds + 's';
        }
    });
}

// Update timers every second
setInterval(updateTimers, 1000);
updateTimers(); // initial call
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("idFilter"); // now used for ID or Service
    const serviceFilter = document.getElementById("serviceFilter");
    const mechanicFilter = document.getElementById("mechanicFilter");
    const statusFilter = document.getElementById("statusFilter");
    const rows = document.querySelectorAll(".workorders tbody tr");

    function applyFilters() {
        const searchVal = searchInput.value.trim().toLowerCase(); // search by ID OR Service
        const serviceVal = serviceFilter.value;
        const mechanicVal = mechanicFilter.value;
        const statusVal = statusFilter.value;

        rows.forEach(row => {
            const rowId = (row.dataset.id || "").toLowerCase();
            const rowService = (row.dataset.service || "").toLowerCase();
            const rowMechanic = (row.dataset.mechanic || "").toLowerCase();
            const rowStatus = (row.dataset.status || "").toLowerCase();

            // ✅ Match search input with ID OR Service name
            const matchSearch = !searchVal || rowId.includes(searchVal) || rowService.includes(searchVal);

            // Match dropdown filters
            const matchService = !serviceVal || rowService === serviceVal;
            const matchMechanic = !mechanicVal || rowMechanic === mechanicVal;
            const matchStatus = !statusVal || rowStatus === statusVal;

            row.style.display = (matchSearch && matchService && matchMechanic && matchStatus) ? "" : "none";
        });
    }

    // Event listeners
    searchInput.addEventListener("keyup", applyFilters);
    serviceFilter.addEventListener("change", applyFilters);
    mechanicFilter.addEventListener("change", applyFilters);
    statusFilter.addEventListener("change", applyFilters);
});

</script>

</body>
</html>
