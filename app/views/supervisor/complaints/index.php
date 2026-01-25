<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Customer Complaints</title>
  <link rel="stylesheet" href="/autonexus/public/assets/css/supervisor/style-complaints.css"/>
</head>
<body>
<?php include __DIR__ . '/../partials/sidebar.php'; ?>
  <main class="main-content">
  <header class="page-header">
  <h1>Customer Complaints</h1>

  <div class="filter-bar">
    <input
      type="text"
      placeholder="Search complaints..."
      class="search"
      id="searchInput"
    />

    <input type="date" id="dateFilter">

    <select id="statusFilter">
      <option value="">All Status</option>
      <option value="open">Open</option>
      <option value="in_progress">In Progress</option>
      <option value="resolved">Resolved</option>
    </select>

    <select id="priorityFilter">
      <option value="">All Priority</option>
      <option value="low">Low</option>
      <option value="medium">Medium</option>
      <option value="high">High</option>
    </select>
  </div>
</header>


    <section class="complaints-section">
      <div class="complaints-container">
  <?php if (!empty($complaints)): ?>
    <?php foreach ($complaints as $complaint): ?>
      <div class="complaint-row"
     data-date="<?= htmlspecialchars($complaint['complaint_date']); ?>"
     data-status="<?= strtolower($complaint['status']); ?>"
     data-priority="<?= strtolower($complaint['priority']); ?>">

        <div class="complaint-header">
          <div>
            <h3><?= htmlspecialchars($complaint['customer_name']); ?></h3>
            <p class="meta">
              <strong>Vehicle:</strong> <?= htmlspecialchars($complaint['vehicle']); ?> (<?= htmlspecialchars($complaint['vehicle_number']); ?>)
              &nbsp; | &nbsp;
              <strong>Date:</strong> <?= htmlspecialchars($complaint['complaint_date']); ?> <?= htmlspecialchars($complaint['complaint_time']); ?>
            </p>
          </div>
          <span class="priority <?= strtolower($complaint['priority']); ?>">
            <?= htmlspecialchars($complaint['priority']); ?>
          </span>
        </div>

        <p class="description"><?= htmlspecialchars($complaint['description']); ?></p>

        <div class="complaint-footer">
          <p><strong>Status:</strong> <span class="status"><?= htmlspecialchars($complaint['status']); ?></span></p>
          <p><strong>Assigned To:</strong> <?= htmlspecialchars($complaint['assigned_to']); ?></p>
        </div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p class="no-data">No complaints found.</p>
  <?php endif; ?>
</div>

    </section>
  </main>
  <script>
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchInput");
    const dateFilter = document.getElementById("dateFilter");
    const statusFilter = document.getElementById("statusFilter");
    const priorityFilter = document.getElementById("priorityFilter");

    const rows = document.querySelectorAll(".complaint-row");

    function applyFilters() {
        const searchVal = searchInput.value.toLowerCase();
        const dateVal = dateFilter.value;
        const statusVal = statusFilter.value;
        const priorityVal = priorityFilter.value;

        rows.forEach(row => {
            const rowText = row.innerText.toLowerCase();
            const rowDate = row.dataset.date;
            const rowStatus = row.dataset.status;
            const rowPriority = row.dataset.priority;

            const matchSearch = !searchVal || rowText.includes(searchVal);
            const matchDate = !dateVal || rowDate === dateVal;
            const matchStatus = !statusVal || rowStatus === statusVal;
            const matchPriority = !priorityVal || rowPriority === priorityVal;

            row.style.display =
                matchSearch && matchDate && matchStatus && matchPriority
                ? "block"
                : "none";
        });
    }

    searchInput.addEventListener("keyup", applyFilters);
    dateFilter.addEventListener("change", applyFilters);
    statusFilter.addEventListener("change", applyFilters);
    priorityFilter.addEventListener("change", applyFilters);
});
</script>

</body>
</html>
