<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Logged-in mechanic user ID
$currentUserId = $_SESSION['user']['user_id'] ?? null;
$base = rtrim(BASE_URL, '/'); 
?>

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
      <option value="on_hold">On-Hold</option>
      <option value="completed">Completed</option>
    </select>

    <select id="myWorkordersFilter">
      <option value="">All Work Orders</option>
      <option value="mine">My Jobs</option>
      <option value="others">Other Mechanics</option>
    </select>
    <button id="resetFilters" type="button">Reset</button>
  </div>
</header>

<section class="job-section">
  <p>Overview of all ongoing jobs</p>
  <div class="job-grid">
  <?php foreach ($allJobs as $job): ?>
    <?php
        // Owner check using user_id
        $owner = ($job['mechanic_user_id'] ?? 0) == $currentUserId ? 'mine' : 'others';
    ?>
    <div class="job-card"
     data-created="<?= $job['started_at'] ?>"
     data-duration="<?= $job['base_duration_minutes'] ?>"
     data-status="<?= strtolower($job['status']) ?>"
     data-service="<?= strtolower($job['name']) ?>"
     data-customer="<?= strtolower($job['first_name'] . ' ' . $job['last_name']) ?>"
     data-vehicle="<?= strtolower($job['make'] . ' ' . $job['model']) ?>"
     data-workorder="<?= $job['work_order_id'] ?>"
     data-owner="<?= $owner ?>">

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
        <strong class="job-timer">--</strong>
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
          <div style="width: <?= $job['progress'] ?>%" class="progress-fill"></div>
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
const myWorkordersFilter = document.getElementById("myWorkordersFilter");
const resetBtn = document.getElementById('resetFilters');
const jobCards = document.querySelectorAll(".job-card");

// Filter function
function applyJobFilters() {
  const searchVal = searchInput.value.toLowerCase().trim();
  const statusVal = statusFilter.value.trim();
  const ownerVal = myWorkordersFilter.value.trim(); // "mine", "others", or ""

  jobCards.forEach(card => {
    const service = (card.dataset.service || "").toLowerCase();
    const customer = (card.dataset.customer || "").toLowerCase();
    const vehicle = (card.dataset.vehicle || "").toLowerCase();
    const workorder = (card.dataset.workorder || "").toLowerCase();
    const status = (card.dataset.status || "").toLowerCase();
    const owner = (card.dataset.owner || "").toLowerCase();

    const matchSearch =
      !searchVal ||
      service.includes(searchVal) ||
      customer.includes(searchVal) ||
      vehicle.includes(searchVal) ||
      workorder.includes(searchVal);

    const matchStatus = !statusVal || status === statusVal;
    const matchOwner = !ownerVal || owner === ownerVal;

    card.style.display = (matchSearch && matchStatus && matchOwner) ? "" : "none";
  });
}

// Event listeners
searchInput.addEventListener("keyup", applyJobFilters);
statusFilter.addEventListener("change", applyJobFilters);
myWorkordersFilter.addEventListener("change", applyJobFilters);

// Reset button
resetBtn.addEventListener('click', () => {
  searchInput.value = '';
  statusFilter.value = '';
  myWorkordersFilter.value = '';
  jobCards.forEach(card => card.style.display = '');
});

// Timer function (only for in_progress jobs)
function updateTimers() {
  const now = new Date();

  document.querySelectorAll('.job-card').forEach(card => {
    const status = card.dataset.status;
    const timerEl = card.querySelector('.job-timer');

    if (!timerEl) return;

    if (status !== "in_progress") {
      timerEl.textContent = "-";
      timerEl.style.color = "";
      return;
    }

    const createdAt = card.dataset.created;
    const durationMin = parseInt(card.dataset.duration);
    if (!createdAt || !durationMin) return;

    const startTime = new Date(createdAt);
    const endTime = new Date(startTime.getTime() + durationMin * 60000);
    let diff = Math.floor((endTime - now) / 1000);

    if (diff <= 0) {
      timerEl.textContent = "Overdue";
      timerEl.style.color = "red";
      return;
    }

    const hours = Math.floor(diff / 3600);
    diff %= 3600;
    const minutes = Math.floor(diff / 60);
    const seconds = diff % 60;

    timerEl.style.color = "";
    timerEl.textContent =
      (hours > 0 ? hours + 'h ' : '') +
      (minutes > 0 ? minutes + 'm ' : '') +
      seconds + 's';
  });
}

updateTimers();
setInterval(updateTimers, 1000);
</script>
</body>
</html>
