<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Auto Shop Dashboard</title>
  <link rel="stylesheet" href="/autonexus/public/assets/css/mechanic/style-assignedjobs.css"/>
</head>
<body>
<?php include __DIR__ . '/../partials/sidebar.php'; ?>
    <main class="main">
      <header>
        <input type="text" placeholder="Search..." class="search" />
      </header>
      <section class="job-section">
  <h2>Ongoing Jobs</h2>
  <p>Overview of all ongoing jobs</p>
  <section class="focus-section">
  <h2>Focus Job</h2>
  <div id="focus-dropzone" class="focus-dropzone">
    <p>Drag a job here to focus</p>
  </div>
</section>
  <div class="job-grid" id="job-grid">
    <?php if (!empty($workOrders)) : ?>
        <?php foreach ($workOrders as $job) : ?>
          <div class="job-card"
          draggable="true"
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
      <button class="view-btn" onclick="location.href='/autonexus/mechanic/jobs/view/<?= $job['work_order_id'] ?>'">Edit</button>
      </div>
      </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No assigned jobs yet.</p>
    <?php endif; ?>
</div>
</section>
    </main>
  <script> 
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

const focusDropzone = document.getElementById('focus-dropzone');
let draggedJob = null;

// Drag start
document.querySelectorAll('.job-card').forEach(card => {
  card.addEventListener('dragstart', e => {
    draggedJob = card;
    e.dataTransfer.effectAllowed = 'move';
    card.style.opacity = '0.5';
  });
  card.addEventListener('dragend', e => {
    draggedJob = null;
    card.style.opacity = '1';
  });
});

// Drag over dropzone
focusDropzone.addEventListener('dragover', e => {
  e.preventDefault();
  focusDropzone.style.backgroundColor = '#f0f0f0';
});

// Drag leave
focusDropzone.addEventListener('dragleave', e => {
  focusDropzone.style.backgroundColor = '';
});

// Drop
focusDropzone.addEventListener('drop', e => {
  e.preventDefault();
  focusDropzone.style.backgroundColor = '';
  if (draggedJob) {
    // Clear existing focus
    focusDropzone.innerHTML = '';
    // Clone dragged job to focus area
    const clone = draggedJob.cloneNode(true);
    clone.classList.add('focused-job');
    focusDropzone.appendChild(clone);
    // Optional: scroll into view
    clone.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }
});
</script>
</body>
</html>
