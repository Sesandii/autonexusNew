<?php $base = rtrim(BASE_URL,'/'); ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Create Work Order</title>
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/supervisor/style-workorders.css">
  
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/supervisor/forms.css">
</head>
<body>
<?php include __DIR__ . '/../partials/sidebar.php'; ?>

<main class="container">
  <div class="page-header">
    <div>
      <h1>Create Work Order</h1>
      <p class="subtitle">Choose an appointment — its service will load automatically.</p>
    </div>
  </div>

  <div class="form-card">
    <form method="post" action="<?= $base ?>/supervisor/workorders">
      <div class="form-grid">

        <div class="form-group">
          <label class="required">Appointment</label>
          <select name="appointment_id" id="appointment_id" required>
            <option value="">-- choose appointment --</option>
            <?php foreach ($availableAppointments as $a): ?>
              <option
    value="<?= (int)$a['appointment_id'] ?>"
    data-service="<?= htmlspecialchars($a['service_name'] ?? '', ENT_QUOTES) ?>"
    data-service-id="<?= (int)($a['service_id'] ?? 0) ?>"
    data-datetime="<?= htmlspecialchars(($a['appointment_date'] ?? '') . ' ' . ($a['appointment_time'] ?? ''), ENT_QUOTES) ?>">
    <?= (int)$a['appointment_id'] ?> — <?= htmlspecialchars($a['appointment_date'] ?? '') ?> <?= htmlspecialchars($a['appointment_time'] ?? '') ?> — <?= htmlspecialchars($a['service_name'] ?? '') ?>
</option>

            <?php endforeach; ?>
          </select>
          <div class="help">Only “requested/confirmed” appointments are shown.</div>
        </div>

        <div class="form-group">
          <label>Mechanic</label>
          <select name="mechanic_id">
            <option value="">-- unassigned --</option>
            <?php foreach ($activeMechanics as $m): ?>
              <option value="<?= (int)$m['mechanic_id'] ?>">
              <?= htmlspecialchars($m['open_jobs']) ?>-<?= htmlspecialchars($m['in_progress_jobs']) ?>-<?= htmlspecialchars($m['completed_jobs']) ?> — <?= htmlspecialchars($m['mechanic_code']) ?> — <?= htmlspecialchars($m['specialization']) ?> — <?= htmlspecialchars($m['current_job']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label>Service (from appointment)</label>
          <input type="text" id="service_display" value="" readonly>
          <div class="help">This is derived from the selected appointment.</div>
            </div>

        <div class="form-group" style="grid-column:1/-1">
          <label>Service Summary</label>
          <textarea name="service_summary" placeholder="Notes, observations, extra work…"></textarea>
        </div>

        <div class="form-group checklist-box">
    <label class="checklist-title">Service Checklist</label>

    <ul id="checklist-display" class="checklist">
        <li class="placeholder">Select an appointment to see the checklist</li>
    </ul>
</div>


        <div class="form-group">
          <label>Status</label>
          <select name="status">
            <option value="open">open</option>
            <option value="in_progress">in_progress</option>
            <option value="completed">completed</option>
          </select>
        </div>

      </div>

      <div class="form-actions">
        <a class="btn" href="<?= $base ?>/supervisor/workorders">Cancel</a>
        <button class="btn primary" type="submit">Save</button>
      </div>
    </form>
  </div>
</main>

<script>
(function () {
    const appt = document.getElementById('appointment_id');
    const svc  = document.getElementById('service_display');
    const checklistDisplay = document.getElementById('checklist-display');

    // Templates passed from backend
    const templates = <?= json_encode($allTemplates ?? []) ?>;

    function applyFromOption(opt) {
        const name = opt?.dataset.service || '';
        svc.value = name;

        checklistDisplay.innerHTML = '';
        const serviceId = opt?.dataset.serviceId;

        if (serviceId && templates[serviceId]) {
            templates[serviceId].forEach(step => {
                const li = document.createElement('li');
                li.textContent = step.step_name;
                checklistDisplay.appendChild(li);
            });
        } else {
            const li = document.createElement('li');
            li.textContent = 'No checklist defined for this service';
            checklistDisplay.appendChild(li);
        }
    }

    appt.addEventListener('change', function () {
        const o = appt.options[appt.selectedIndex];
        applyFromOption(o);
    });

    // If browser restores selected value
    if (appt.value) {
        applyFromOption(appt.options[appt.selectedIndex]);
    }
})();

</script>

</body>
</html>
