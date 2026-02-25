<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Auto Shop Dashboard</title>
  <link rel="stylesheet" href="/autonexus/public/assets/css/mechanic/style-assignedjobs.css"/>
  <style>
    /* Blur / disable open jobs when current job exists */
    .job-card.disabled {
        opacity: 0.5;
        pointer-events: none;
        filter: blur(1px);
    }
  </style>
</head>
<body>

<?php include __DIR__ . '/../partials/sidebar.php'; ?>

<main class="main">

<section class="job-section">
<h2>Current Job</h2>

<section class="focus-section">
<div id="focus-dropzone" class="focus-dropzone">

<?php $hasCurrentJob = !empty($currentJobs); ?>

<?php if ($hasCurrentJob) : 
    $current = $currentJobs[0]; ?>
    
    <div class="job-card focused-job"
     data-job-id="<?= $current['work_order_id'] ?>"
     data-status="<?= $current['status'] ?>"
     data-created="<?= $current['job_start_time'] ?? '' ?>"
     data-duration="<?= $current['base_duration_minutes'] ?>"
     data-remaining="<?= $current['paused_remaining_seconds'] ?? '' ?>">

      <h3 class="job-title">
        Work Order <?= htmlspecialchars($current['work_order_id']) ?>
      </h3>

      <div class="job-info">
        <span>Service</span>
        <?= htmlspecialchars($current['name']) ?>
      </div>

      <div class="job-info">
        <span>Customer</span>
        <?= htmlspecialchars($current['first_name'] . ' ' . $current['last_name']) ?>
      </div>

      <div class="job-info">
        <span>Vehicle</span>
        <?= htmlspecialchars($current['make'] . ' ' . $current['model']) ?>
      </div>

      <div class="job-info timer">
        <span>Time Remaining</span>
        <strong class="job-timer">--:--:--</strong>
      </div>

      <div class="progress-wrapper">
        <div class="progress-label">
          Progress: <?= $current['progress'] ?>%
        </div>
        <div class="progress-bar">
          <div class="progress-fill"
               style="width: <?= $current['progress'] ?>%">
          </div>
        </div>
      </div>

      <div class="job-actions">
        <button class="view-btn"
          onclick="location.href='/autonexus/mechanic/jobs/view/<?= $current['work_order_id'] ?>'">
          Edit
        </button>
      </div>

    </div>

<?php else: ?>
    <p>No current job in progress.</p>
<?php endif; ?>

</div>
</section>

<h2>Open Jobs</h2>
<p>Jobs waiting to be started</p>

<div class="job-grid" id="job-grid">

<?php if (!empty($openJobs)) : ?>
    <?php foreach ($openJobs as $job) : ?>
      <div class="job-card <?= ($hasCurrentJob && $job['status'] === 'open') ? 'disabled' : '' ?> <?= $job['status'] === 'on_hold' ? 'on-hold' : '' ?>"
     draggable="true"
     data-job-id="<?= $job['work_order_id'] ?>"
     data-status="<?= $job['status'] ?>"
     data-duration="<?= $job['base_duration_minutes'] ?>"
>
<h3 class="job-title">
Work Order <?= htmlspecialchars($job['work_order_id']) ?>
    <?php if($job['status'] === 'on_hold'): ?>
      <span style="color:orange;">(Paused)</span>
    <?php endif; ?>
      </h3>

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

      <div class="progress-wrapper">
        <div class="progress-label">
          Progress: <?= $job['progress'] ?>%
        </div>
        <div class="progress-bar">
          <div class="progress-fill"
               style="width: <?= $job['progress'] ?>%"></div>
        </div>
      </div>

    </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>No open jobs available.</p>
<?php endif; ?>

</div>
</section>
</main>

<script>
function updateTimers() {
  const now = new Date();

  document.querySelectorAll('.job-card').forEach(card => {
    const status = card.dataset.status;
    const durationMin = parseInt(card.dataset.duration) || 0;
    const jobStartStr = card.dataset.created;
    let remainingSec;

    // Only count down if the job is in_progress
    if (status === 'in_progress') {
      if (card.dataset.remaining && card.dataset.remaining !== '') {
        remainingSec = parseInt(card.dataset.remaining);
      } else if (jobStartStr) {
        const jobStart = new Date(jobStartStr);
        remainingSec = Math.floor(durationMin * 60 - (now - jobStart) / 1000);
      } else {
        remainingSec = durationMin * 60;
      }

      const timerEl = card.querySelector('.job-timer');
      if (!timerEl) return;

      if (remainingSec <= 0) {
        timerEl.textContent = "Overdue";
        timerEl.style.color = "red";
        card.dataset.remaining = 0;
        return;
      }

      const hours = Math.floor(remainingSec / 3600);
      const minutes = Math.floor((remainingSec % 3600) / 60);
      const seconds = remainingSec % 60;

      timerEl.textContent =
        `${hours > 0 ? hours + 'h ' : ''}` +
        `${minutes > 0 ? minutes + 'm ' : ''}` +
        `${seconds}s`;

      card.dataset.remaining = remainingSec - 1;
      timerEl.style.color = "";

    } else if (status === 'on_hold') {
      // Show paused remaining
      remainingSec = card.dataset.remaining ? parseInt(card.dataset.remaining) : durationMin * 60;
      const timerEl = card.querySelector('.job-timer');
      if (!timerEl) return;
      const hours = Math.floor(remainingSec / 3600);
      const minutes = Math.floor((remainingSec % 3600) / 60);
      const seconds = remainingSec % 60;

      timerEl.textContent =
        `${hours > 0 ? hours + 'h ' : ''}` +
        `${minutes > 0 ? minutes + 'm ' : ''}` +
        `${seconds}s (paused)`;
      timerEl.style.color = "orange";

    } else {
      // open or completed
      const timerEl = card.querySelector('.job-timer');
      if (!timerEl) return;
      timerEl.textContent = "--:--:--";
      card.dataset.remaining = '';
    }
  });
}

updateTimers();
setInterval(updateTimers, 1000);

/* ================= DRAG TO START JOB ================= */
const focusDropzone = document.getElementById('focus-dropzone');
let draggedJob = null;

/* ================= DRAG START ================= */
document.querySelectorAll('#job-grid .job-card').forEach(card => {

  card.addEventListener('dragstart', e => {
    draggedJob = card;
    card.style.opacity = '0.5';
  });

  card.addEventListener('dragend', e => {
    draggedJob = null;
    card.style.opacity = '1';
  });

});

/* ================= DROP ZONE ================= */
focusDropzone.addEventListener('dragover', e => {
  e.preventDefault();
  focusDropzone.style.backgroundColor = '#f0f0f0';
});

focusDropzone.addEventListener('dragleave', e => {
  focusDropzone.style.backgroundColor = '';
});

focusDropzone.addEventListener('drop', e => {
  e.preventDefault();
  focusDropzone.style.backgroundColor = '';

  if (!draggedJob) return;

  const currentCard = focusDropzone.querySelector('.job-card.focused-job');

  /* ================= 1️⃣ PAUSE CURRENT JOB ================= */
  if (currentCard) {

    const now = new Date();
    const durationMin = parseInt(currentCard.dataset.duration) || 0;
    const startTime = new Date(currentCard.dataset.created);

    const remainingSec = Math.floor(
      durationMin * 60 - (now - startTime) / 1000
    );

    currentCard.dataset.status = 'on_hold';
    currentCard.dataset.remaining = remainingSec > 0 ? remainingSec : 0;

    currentCard.classList.remove('focused-job');

    // Move back to grid (keep visible as paused)
    document.getElementById('job-grid').appendChild(currentCard);

    fetch(`/autonexus/mechanic/jobs/set-status/${currentCard.dataset.jobId}`, {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({
        status:'on_hold',
        paused_remaining_seconds: currentCard.dataset.remaining
      })
    });
  }

  /* ================= 2️⃣ START OR RESUME DROPPED JOB ================= */

  const clone = draggedJob.cloneNode(true);
  clone.classList.add('focused-job');

  const previousStatus = clone.dataset.status;

  clone.dataset.status = 'in_progress';

  // 🔹 If job was paused → resume from remaining time
  if (previousStatus === 'on_hold' && clone.dataset.remaining) {

    const remainingSec = parseInt(clone.dataset.remaining);
    const newStart = new Date(Date.now() - 
                    ((parseInt(clone.dataset.duration) * 60 - remainingSec) * 1000));

    clone.dataset.created = newStart.toISOString();

  } 
  // 🔹 If job was open → start fresh
  else {
    clone.dataset.created = new Date().toISOString();
    clone.dataset.remaining = '';
  }

  focusDropzone.innerHTML = '';
  focusDropzone.appendChild(clone);

  draggedJob.remove();

  fetch(`/autonexus/mechanic/jobs/set-status/${clone.dataset.jobId}`, {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify({status:'in_progress'})
  });

});


</script>
</body>
</html>
