<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Auto Shop Dashboard</title>
  <link rel="stylesheet" href="/autonexus/public/assets/css/mechanic/style-assignedjobs.css"/>
  <style>
    .job-card.disabled {
        opacity: 0.5;
        cursor: grab;
        filter: blur(1px);
    }
  </style>
</head>
<body>

<?php include __DIR__ . '/../partials/sidebar.php'; ?>

<main class="main">
  <div class="breadcrumb-text">
    Mechanic <span class="sep">&gt;</span> 
    Assigned Jobs <span class="sep"></span>
  </div>

<section class="job-section">
  <h2>Current Job</h2>

  <section class="focus-section">
    <div id="focus-dropzone" class="focus-dropzone">

    <?php $hasCurrentJob = !empty($currentJobs); ?>

    <?php if ($hasCurrentJob) : 
        $current = $currentJobs[0]; ?>
        
        <div class="job-card focused-job"
          draggable="true"
          data-job-id="<?= $current['work_order_id'] ?>"
          data-status="<?= $current['status'] ?>"
          data-duration="<?= $current['base_duration_minutes'] ?>"
          data-remaining="<?= $current['seconds_left'] ?>" 
          data-created="<?= $current['job_start_time'] ?? '' ?>">

          <h3 class="job-title">
            <?= htmlspecialchars($current['name']) ?> - <?= htmlspecialchars($current['make'] . ' ' . $current['model']) ?>
          </h3>

          <div class="job-info">
            <span>Customer</span>
            <?= htmlspecialchars($current['first_name'] . ' ' . $current['last_name']) ?>
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
              <div class="progress-fill" style="width: <?= $current['progress'] ?>%"></div>
            </div>
          </div>

          <div class="job-actions">
            <button class="view-btn" onclick="location.href='/autonexus/mechanic/jobs/view/<?= $current['work_order_id'] ?>'">
              Edit
            </button>
          </div>
        </div>

    <?php else: ?>
        <p>No current job in progress. Drag a job here to start.</p>
    <?php endif; ?>

    </div>
  </section>

  <h2>Open Jobs</h2>
  <p>Jobs waiting to be started</p>
  <div class="job-filters">
        <label style="font-size: 0.9rem; color: #666; cursor: pointer;">
            <input type="checkbox" id="showCompleted" onchange="toggleCompleted()"> Show Completed Jobs
        </label>
    </div>

  <div class="job-grid" id="job-grid">
    <?php if (!empty($openJobs)) : ?>
        <?php foreach ($openJobs as $job) : ?>
          <div class="job-card <?= ($hasCurrentJob && $job['status'] === 'open') ? 'disabled' : '' ?> <?= $job['status'] === 'on_hold' ? 'on-hold' : '' ?>"
            draggable="true"
            data-job-id="<?= $job['work_order_id'] ?>"
            data-status="<?= $job['status'] ?>"
            data-duration="<?= $job['base_duration_minutes'] ?>"
            data-remaining="<?= $job['seconds_left'] ?>"
            data-created="<?= $job['job_start_time'] ?? '' ?>">
            
            <h3 class="job-title">
              <?= htmlspecialchars($job['name']) ?>
              <?php if($job['status'] === 'on_hold'): ?>
                <span style="color:orange;">(Paused)</span>
              <?php endif; ?>
            </h3>
            <div class="job-info"><span>Customer</span> <?= htmlspecialchars($job['first_name'] . ' ' . $job['last_name']) ?></div>
            <div class="job-info"><span>Vehicle</span> <?= htmlspecialchars($job['make'] . ' ' . $job['model']) ?></div>

            <div class="job-actions" style="margin-top: 15px;">
    <a href="<?= $base ?>/mechanic/jobs/view/<?= $job['work_order_id'] ?>" class="btn-black-tile">
        View
    </a>
</div>

            <div class="progress-wrapper">
              <div class="progress-label">Progress: <?= $job['progress'] ?>%</div>
              <div class="progress-bar">
                <div class="progress-fill" style="width: <?= $job['progress'] ?>%"></div>
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
    document.querySelectorAll('.job-card').forEach(card => {
        const status = card.dataset.status;
        const timerEl = card.querySelector('.job-timer');
        if (!timerEl) return;

        let remaining = parseInt(card.dataset.remaining);

        if (status === 'in_progress') {
            if (remaining > 0) {
                remaining--; 
                card.dataset.remaining = remaining; 

                const h = Math.floor(remaining / 3600);
                const m = Math.floor((remaining % 3600) / 60);
                const s = remaining % 60;

                timerEl.textContent = `${h > 0 ? h + 'h ' : ''}${m}m ${s}s`;
                timerEl.style.color = remaining < 300 ? "red" : ""; 
            } else {
                timerEl.textContent = "Overdue";
                timerEl.style.color = "red";
            }
        } else if (status === 'on_hold') {
            const h = Math.floor(remaining / 3600);
            const m = Math.floor((remaining % 3600) / 60);
            timerEl.textContent = `${h > 0 ? h + 'h ' : ''}${m}m (Paused)`;
            timerEl.style.color = "orange";
        } else {
            timerEl.textContent = "--:--:--";
        }
    });
}

updateTimers();
setInterval(updateTimers, 1000);

/* DRAG & DROP */
const focusDropzone = document.getElementById('focus-dropzone');
let draggedJob = null;

document.querySelectorAll('.job-card').forEach(card => {
  card.addEventListener('dragstart', e => {
    if (card.dataset.status === 'completed') {
        e.preventDefault();
        return false;
    }
    draggedJob = card;
    card.style.opacity = '0.5';
});
    card.addEventListener('dragend', () => {
        draggedJob = null;
        card.style.opacity = '1';
    });
});

focusDropzone.addEventListener('dragover', e => e.preventDefault());

focusDropzone.addEventListener('drop', async (e) => {
    e.preventDefault();
    if (!draggedJob) return;

    const jobId = draggedJob.dataset.jobId;
    const currentCard = focusDropzone.querySelector('.job-card.focused-job');

    // 1. Check if we are dragging the same job that's already focused
    if (currentCard && currentCard.dataset.jobId === jobId) return;

    try {
        // 2. Use the correct URL. Based on your Controller, it's likely update-status
        // If you don't have a specific JSON API, we use FormData to talk to your existing method
        const formData = new FormData();
        formData.append('work_order_id', jobId);
        formData.append('status', 'in_progress');

        const response = await fetch('/autonexus/mechanic/jobs/update-status', {
            method: 'POST',
            body: formData
        });

        if (response.ok) {
            window.location.reload();
        } else {
            // Handle the error (like the "Already has an active job" error we wrote)
            alert("Could not start job. Check if you have another job in progress.");
        }
    } catch (error) {
        console.error("Error:", error);
    }
});

function toggleCompleted() {
    const showCompleted = document.getElementById('showCompleted').checked;
    const cards = document.querySelectorAll('.job-card');

    cards.forEach(card => {
        // We only care about cards in the grid (not the focused one)
        if (!card.classList.contains('focused-job')) {
            if (card.dataset.status === 'completed') {
                card.style.display = showCompleted ? 'block' : 'none';
            }
        }
    });
}

// Ensure the filters are applied correctly if the page refreshes
document.addEventListener('DOMContentLoaded', () => {
    toggleCompleted();
});
</script>
</body>
</html>