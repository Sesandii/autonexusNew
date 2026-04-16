<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

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
<div class="breadcrumb-text">
    Mechanic <span class="sep">&gt;</span> 
    Jobs <span class="sep"></span> 
  </div>
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
    <button id="resetFilters" type="button" class="view-btn">Reset</button>
  </div>
</header>

<section class="job-section">
  
  <p>Overview of all ongoing jobs</p>
  <div class="job-grid">
  <?php foreach ($allJobs as $job): ?>
    <?php
        $owner = ($job['mechanic_user_id'] ?? 0) == $currentUserId ? 'mine' : 'others';
        $status = strtolower($job['status']);
        $initialDisplay = ($status === 'completed') ? 'display: none;' : '';
  
    ?>
    <div class="job-card"
    style="<?= $initialDisplay ?>"
     data-created="<?= $job['started_at'] ?>"
     data-duration="<?= $job['base_duration_minutes'] ?>"
     data-status="<?= strtolower($job['status']) ?>"
     data-service="<?= strtolower($job['name']) ?>"
     data-remaining="<?= $job['seconds_left'] ?? ($job['base_duration_minutes'] * 60) ?>"
     data-customer="<?= strtolower($job['first_name'] . ' ' . $job['last_name']) ?>"
     data-vehicle="<?= strtolower($job['make'] . ' ' . $job['model']) ?>"
     data-workorder="<?= $job['work_order_id'] ?>"
     data-owner="<?= $owner ?>">

      <h3 class="job-title"><?= htmlspecialchars($job['name']) ?> - <?= htmlspecialchars($job['make'] . ' ' . $job['model']) ?></h3>
      <div class="job-info">
        <span>Customer</span>
        <?= htmlspecialchars($job['first_name'] . ' ' . $job['last_name']) ?>
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

function applyJobFilters() {
  const searchVal = searchInput.value.toLowerCase().trim();
  const statusVal = statusFilter.value.trim();
  const ownerVal = myWorkordersFilter.value.trim();

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

      let matchStatus = false;
    if (statusVal === "") {
      matchStatus = (status !== "completed");
    } else {
      matchStatus = (status === statusVal);
    }
    const matchOwner = !ownerVal || owner === ownerVal;

    card.style.display = (matchSearch && matchStatus && matchOwner) ? "" : "none";
  });
}

searchInput.addEventListener("keyup", applyJobFilters);
statusFilter.addEventListener("change", applyJobFilters);
myWorkordersFilter.addEventListener("change", applyJobFilters);

resetBtn.addEventListener('click', () => {
  searchInput.value = '';
  statusFilter.value = '';
  myWorkordersFilter.value = '';
  applyJobFilters();
});

let jobTimers = {};

function initTimers() {
  console.log("Initializing timers..."); 
  document.querySelectorAll('.job-card').forEach(card => {
    const id = card.dataset.workorder;
    const remaining = parseInt(card.dataset.remaining);
    jobTimers[id] = isNaN(remaining) ? 0 : remaining;
  });
}

function formatTime(seconds) {
  if (seconds <= 0) return "0s";
  const h = Math.floor(seconds / 3600);
  const m = Math.floor((seconds % 3600) / 60);
  const s = seconds % 60;
  
  let parts = [];
  if (h > 0) parts.push(h + 'h');
  if (m > 0 || h > 0) parts.push(m + 'm');
  parts.push(s + 's');
  
  return parts.join(' ');
}

function updateTimers() {
  document.querySelectorAll('.job-card').forEach(card => {
    const id = card.dataset.workorder;
    const status = (card.dataset.status || "").toLowerCase();
    const timerEl = card.querySelector('.job-timer');

    if (!timerEl) return;

    if (status === "on_hold") {
      timerEl.textContent = "Paused: " + formatTime(jobTimers[id]);
      timerEl.style.color = "#f39c12"; 
      return;
    }

    if (status === "open") {
      timerEl.textContent = "Not Started";
      timerEl.style.color = "#95a5a6";
      return;
    }

    if (status === "completed") {
      timerEl.textContent = "Finished";
      timerEl.style.color = "#2ecc71";
      return;
    }

    if (status === "in_progress") {
      if (jobTimers[id] > 0) {
        jobTimers[id]--; 
        timerEl.textContent = formatTime(jobTimers[id]);
        
        if (jobTimers[id] < 300) {
          timerEl.style.color = "#e74c3c";
          timerEl.style.fontWeight = "bold";
        } else {
          timerEl.style.color = ""; 
          timerEl.style.fontWeight = "normal";
        }
      } else {
        timerEl.textContent = "Overdue";
        timerEl.style.color = "#e74c3c";
        timerEl.style.fontWeight = "bold";
      }
    }
  });
}

document.addEventListener('DOMContentLoaded', () => {
    initTimers();
    updateTimers(); 
    setInterval(updateTimers, 1000);
});

</script>
</body>
</html>
