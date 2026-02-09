<?php $base = rtrim(BASE_URL, '/'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Auto Shop Dashboard</title>
<link rel="stylesheet" href="<?= $base ?>/public/assets/css/mechanic/style-jobs.css"/>
</head>
<body>
<?php include __DIR__ . '/../partials/sidebar.php'; ?>
<main class="main-content">
<header class="job-header">
  <h1>Ongoing Jobs</h1>

  <div class="filters-row">
    <input
      type="text"
      id="searchInput"
      placeholder="Search by service, customer, vehicle..."
      class="search"
    />

    <select id="statusFilter">
      <option value="">All Status</option>
      <option value="open">Open</option>
      <option value="in_progress">In-Progress</option>
      <option value="completed">Completed</option>
    </select>
  </div>
</header>
<section class="job-section">
  <p>Overview of all ongoing jobs</p>
  <div class="job-grid">
  <?php foreach ($allJobs as $job): ?>
    <div class="job-card"
     data-created="<?= $job['started_at'] ?>"
     data-duration="<?= $job['base_duration_minutes'] ?>"
     data-status="<?= strtolower($job['status']) ?>"
     data-service="<?= strtolower($job['name']) ?>"
     data-customer="<?= strtolower($job['first_name'] . ' ' . $job['last_name']) ?>"
     data-vehicle="<?= strtolower($job['make'] . ' ' . $job['model']) ?>"
     data-workorder="<?= $job['work_order_id'] ?>">

      <h3 class="job-title">Work Order <?= htmlspecialchars($job['work_order_id']) ?></h3>

      <div class="job-info">
        <span>Service</span>
        <?= htmlspecialchars($job['name']) ?>
      </div>
      <div class="job-info">
        <span>Customer</span>
        <?= htmlspecialchars($job['first_name'] . ' ' . $job['last_name']) ?>
      </div>

      <div class="job-info">
        <span>Vehicle</span>
        <?= htmlspecialchars($job['make'] . ' ' . $job['model']) ?>
      </div>

      <div class="job-info timer">
    <span>Time Remaining</span>
    <strong class="job-timer">--:--:--</strong>
  </div>

      <div class="job-info status">
        <span>Status</span>
        <?= htmlspecialchars($job['status']) ?>
      </div>

      <div class="progress-wrapper">
  <div class="progress-label">
    Progress: <?= $job['progress'] ?>%
  </div>
  <div class="progress-bar">
    <div 
      class="progress-fill"
      style="width: <?= $job['progress'] ?>%">
    </div>
  </div>
</div>
      <div class="job-actions">
        <a href="<?= $base ?>/mechanic/jobs/view/<?= $job['work_order_id'] ?>" class="view-btn">
          View
        </a>
      </div>

    </div>
  <?php endforeach; ?>
</div>


</section>
</main>
<script>
const searchInput = document.getElementById("searchInput");
const statusFilter = document.getElementById("statusFilter");
const jobCards = document.querySelectorAll(".job-card");

function applyJobFilters() {
  const searchVal = searchInput.value.toLowerCase();
  const statusVal = statusFilter.value;

  jobCards.forEach(card => {
    const service = card.dataset.service;
    const customer = card.dataset.customer;
    const vehicle = card.dataset.vehicle;
    const workorder = card.dataset.workorder;
    const status = card.dataset.status;

    const matchSearch =
      !searchVal ||
      service.includes(searchVal) ||
      customer.includes(searchVal) ||
      vehicle.includes(searchVal) ||
      workorder.includes(searchVal);

    const matchStatus =
      !statusVal || status === statusVal;

    card.style.display = matchSearch && matchStatus ? "" : "none";
  });
}

searchInput.addEventListener("keyup", applyJobFilters);
statusFilter.addEventListener("change", applyJobFilters);

function updateTimers() {
  document.querySelectorAll('.job-card').forEach(card => {
    const createdAt = card.dataset.created;
    const durationMin = parseInt(card.dataset.duration);

    if (!createdAt || !durationMin) return;

    const startTime = new Date(createdAt);
    const endTime = new Date(startTime.getTime() + durationMin * 60000);
    const now = new Date();

    const diff = endTime - now;
    const timerEl = card.querySelector('.job-timer');

    if (diff <= 0) {
      timerEl.textContent = "Overdue";
      timerEl.style.color = "red";
      return;
    }

    const hours = Math.floor(diff / (1000 * 60 * 60));
    const minutes = Math.floor((diff / (1000 * 60)) % 60);
    const seconds = Math.floor((diff / 1000) % 60);

    timerEl.textContent =
      `${String(hours).padStart(2, '0')}:` +
      `${String(minutes).padStart(2, '0')}:` +
      `${String(seconds).padStart(2, '0')}`;
  });
}

// run immediately
updateTimers();
// update every second
setInterval(updateTimers, 1000);
</script>

</body>
</html>
