<?php $base = rtrim(BASE_URL,'/'); ?>
<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}


$message = $_SESSION['message'] ?? null;
unset($_SESSION['message']);

// Logged-in supervisor ID for owner filter
$currentSupervisorId = $_SESSION['user']['user_id'] ?? 0;
?>
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

<?php if (!empty($message)): ?>
    <div class="toast <?= htmlspecialchars($message['type']) ?>">
        <?= htmlspecialchars($message['text']) ?>
    </div>
<?php endif; ?>

<div class="container">
  <div class="page-header">
    <div class="header">
      <h1>Work Orders</h1>
      <p class="subtitle">All work orders with their appointment's service.</p>
    </div>
    <a class="btn primary" href="<?= $base ?>/supervisor/workorders/create">Add Work Order</a>
  </div>

  <!-- Filters -->
  <div class="table-filters">
    <input type="text" id="idFilter" placeholder="Search by Work Order ID, Service" class="filter-input">

    <select id="serviceFilter">
      <option value="">All Services</option>
      <?php
        $services = array_unique(array_column($workOrders, 'service_name'));
        foreach ($services as $service):
      ?>
        <option value="<?= strtolower($service) ?>"><?= htmlspecialchars($service) ?></option>
      <?php endforeach; ?>
    </select>

    <select id="mechanicFilter">
      <option value="">All Mechanics</option>
      <?php
        $mechanics = array_unique(array_column($workOrders, 'mechanic_code'));
        foreach ($mechanics as $mech):
      ?>
        <option value="<?= strtolower($mech ?: 'unassigned') ?>"><?= htmlspecialchars($mech ?: 'Unassigned') ?></option>
      <?php endforeach; ?>
    </select>

    <select id="statusFilter">
      <option value="">All Statuses</option>
      <option value="open">Open</option>
      <option value="in_progress">In Progress</option>
      <option value="on_hold">On Hold</option>
      <option value="completed">Completed</option>
    </select>

    <select id="ownerFilter">
      <option value="">All Work Orders</option>
      <option value="mine">My Work Orders</option>
      <option value="others">Other Supervisors</option>
    </select>

    <button id="resetFilters" class="btn small">Reset</button>
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
        <th>Supervisor</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($workOrders as $w): ?>
    <?php
        $isOwner = ($w['supervisor_id'] ?? 0) == $currentSupervisorId;

        // Only calculate expected completion if status is 'in_progress'
        $expectedEnd = '-';
        if (!empty($w['started_at']) && !empty($w['base_duration_minutes']) && strtolower($w['status']) === 'in_progress') {
            $dt = new \DateTime($w['started_at']);
            $dt->add(new \DateInterval('PT' . (int)$w['base_duration_minutes'] . 'M'));
            $expectedEnd = $dt->format('Y-m-d H:i:s');
        }

        $supervisorCode = htmlspecialchars($w['supervisor_code'] ?? '-');
        $status = strtolower($w['status'] ?? '');
    ?>
    <tr
        data-id="<?= $w['work_order_id'] ?>"
        data-service="<?= strtolower($w['service_name'] ?? '') ?>"
        data-mechanic="<?= strtolower($w['mechanic_code'] ?? 'unassigned') ?>"
        data-status="<?= $status ?>"
        data-owner="<?= $isOwner ? 'mine' : 'others' ?>"
    >
        <td><?= htmlspecialchars($w['work_order_id']) ?></td>
        <td><?= htmlspecialchars(($w['appointment_date'] ?? '') . ' ' . ($w['appointment_time'] ?? '')) ?></td>
        <td><?= htmlspecialchars($w['service_name'] ?? '') ?></td>
        <td><?= htmlspecialchars($w['mechanic_code'] ?? 'Unassigned') ?></td>
        <td class="countdown"
    data-start="<?= $w['job_start_time'] ?? '' ?>"
    data-duration="<?= $w['base_duration_minutes'] ?? 0 ?>"
    data-status="<?= strtolower($w['status'] ?? '') ?>"
    data-remaining="<?= $w['paused_remaining_seconds'] ?? '' ?>">
    -
</td>


        <td><span class="status <?= htmlspecialchars($w['status']) ?>"><?= htmlspecialchars($w['status']) ?></span></td>
        <td><?= $supervisorCode ?></td>
        <td>
            <a class="btn small" href="<?= $base ?>/supervisor/workorders/<?= $w['work_order_id'] ?>">View</a>
            <?php if ($isOwner): ?>
                <a class="btn small edit" href="<?= $base ?>/supervisor/workorders/<?= $w['work_order_id'] ?>/edit">Edit</a>
                <form method="post" action="<?= $base ?>/supervisor/workorders/<?= $w['work_order_id'] ?>/delete" style="display:inline" onsubmit="return confirm('Delete this work order?')">
                    <button type="submit" class="btn small danger">Delete</button>
                </form>
            <?php else: ?>
                <span style="color:#888; font-size:13px;">Restricted</span>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>

<script>
function updateTimers() {
    const now = new Date();

    document.querySelectorAll('.countdown').forEach(td => {
        const status = td.dataset.status;
        const durationMin = parseInt(td.dataset.duration) || 0;
        const startStr = td.dataset.start;
        let remainingSec;

        if (status === 'in_progress') {
            // Use paused_remaining if exists
            if (td.dataset.remaining && td.dataset.remaining !== '') {
                remainingSec = parseInt(td.dataset.remaining);
            } else if (startStr) {
                const startTime = new Date(startStr);
                remainingSec = Math.floor(durationMin * 60 - (now - startTime) / 1000);
            } else {
                remainingSec = durationMin * 60;
            }

            if (remainingSec <= 0) {
                td.textContent = "Overdue";
                td.style.color = "red";
                td.dataset.remaining = 0;
                return;
            }

            const hours = Math.floor(remainingSec / 3600);
            const minutes = Math.floor((remainingSec % 3600) / 60);
            const seconds = remainingSec % 60;

            td.textContent =
                (hours > 0 ? hours + 'h ' : '') +
                (minutes > 0 ? minutes + 'm ' : '') +
                seconds + 's';

            // Always decrement for in_progress
            td.dataset.remaining = remainingSec - 1;
            td.style.color = "";

        } else if (status === 'on_hold') {
            // paused time: do NOT decrement
            remainingSec = td.dataset.remaining ? parseInt(td.dataset.remaining) : durationMin * 60;

            const hours = Math.floor(remainingSec / 3600);
            const minutes = Math.floor((remainingSec % 3600) / 60);
            const seconds = remainingSec % 60;

            td.textContent =
                (hours > 0 ? hours + 'h ' : '') +
                (minutes > 0 ? minutes + 'm ' : '') +
                seconds + 's (paused)';

            td.style.color = "orange";
        } else {
            td.textContent = "-";
            td.dataset.remaining = '';
            td.style.color = "";
        }
    });
}

setInterval(updateTimers, 1000);
updateTimers();


// Filters
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("idFilter");
    const serviceFilter = document.getElementById("serviceFilter");
    const mechanicFilter = document.getElementById("mechanicFilter");
    const statusFilter = document.getElementById("statusFilter");
    const ownerFilter = document.getElementById("ownerFilter");
    const resetBtn = document.getElementById("resetFilters");
    const rows = document.querySelectorAll(".workorders tbody tr");

    function applyFilters() {
        const searchVal = searchInput.value.trim().toLowerCase();
        const serviceVal = serviceFilter.value;
        const mechanicVal = mechanicFilter.value;
        const statusVal = statusFilter.value;
        const ownerVal = ownerFilter.value;

        rows.forEach(row => {
            const rowId = (row.dataset.id || "").toLowerCase();
            const rowService = (row.dataset.service || "").toLowerCase();
            const rowMechanic = (row.dataset.mechanic || "").toLowerCase();
            const rowStatus = (row.dataset.status || "").toLowerCase();
            const rowOwner = (row.dataset.owner || "").toLowerCase();

            const matchSearch = !searchVal || rowId.includes(searchVal) || rowService.includes(searchVal);
            const matchService = !serviceVal || rowService === serviceVal;
            const matchMechanic = !mechanicVal || rowMechanic === mechanicVal;
            const matchStatus = !statusVal || rowStatus === statusVal;
            const matchOwner = !ownerVal || rowOwner === ownerVal;

            row.style.display = (matchSearch && matchService && matchMechanic && matchStatus && matchOwner) ? "" : "none";
        });
    }

    searchInput.addEventListener("keyup", applyFilters);
    serviceFilter.addEventListener("change", applyFilters);
    mechanicFilter.addEventListener("change", applyFilters);
    statusFilter.addEventListener("change", applyFilters);
    ownerFilter.addEventListener("change", applyFilters);

    resetBtn.addEventListener("click", function () {
        searchInput.value = "";
        serviceFilter.value = "";
        mechanicFilter.value = "";
        statusFilter.value = "";
        ownerFilter.value = "";
        applyFilters();
    });
});


window.addEventListener('DOMContentLoaded', () => {
    const toast = document.querySelector('.toast');
    if (toast) {
        setTimeout(() => {
            toast.style.transition = 'opacity 0.5s';
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 500);
        }, 3000);
    }
});
</script>
</body>
</html>
