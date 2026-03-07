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

        <!-- Appointment Dropdown -->
        <div class="form-group">
          <label class="required">Appointment</label>
          <select name="appointment_id" id="appointment_id" required>
            <option value="">-- choose appointment --</option>
            <?php foreach ($availableAppointments as $a): ?>
              <option
                value="<?= (int)$a['appointment_id'] ?>"
                data-service="<?= htmlspecialchars($a['service_name'] ?? '', ENT_QUOTES) ?>"
                data-service-id="<?= (int)($a['service_id'] ?? 0) ?>"
                data-datetime="<?= htmlspecialchars(($a['appointment_date'] ?? '') . ' ' . ($a['appointment_time'] ?? ''), ENT_QUOTES) ?>"
                <?= isset($selectedAppointmentId) && $selectedAppointmentId == $a['appointment_id'] ? 'selected' : '' ?>
              >
                <?= (int)$a['appointment_id'] ?> — <?= htmlspecialchars($a['appointment_date'] ?? '') ?> <?= htmlspecialchars($a['appointment_time'] ?? '') ?> — <?= htmlspecialchars($a['service_name'] ?? '') ?>
              </option>
            <?php endforeach; ?>
          </select>
          <div class="help">Only “requested/confirmed” appointments are shown.</div>
        </div>

        <!-- Mechanic Dropdown -->
        <div class="form-group">
          <label>Mechanic</label>
              
          <select name="mechanic_id" id="mechanicSelect">
    <option value="">Select Mechanic</option>
    <?php foreach ($activeMechanics as $mech):
        $code = $mech['mechanic_code'] ?? '';
        $spec = $mech['specialization'] ?? '';
        $activeCount = $mechanicLimits[$code] ?? 0;
        $disabled = $activeCount >= 5 ? 'disabled' : '';
        $style = $activeCount >= 5 ? 'opacity:0.5; filter: blur(1px);' : '';
    ?>
        <option value="<?= $mech['mechanic_id'] ?>" <?= $disabled ?> style="<?= $style ?>">
            <?= htmlspecialchars($code . ' (' . $spec . ')') ?>
            <?= $activeCount >= 5 ? '⚠ Full' : '' ?>
        </option>
    <?php endforeach; ?>
</select>

        </div>

        <!-- Service Display -->
        <div class="form-group">
          <label>Service (from appointment)</label>
          <input type="text" id="service_display" value="" readonly>
          <div class="help">This is derived from the selected appointment.</div>
        </div>

        <!-- Service Summary -->
        <div class="form-group" style="grid-column:1/-1">
          <label>Service Summary</label>
          <textarea name="service_summary" placeholder="Notes, observations, extra work…"></textarea>
        </div>

        <!-- Checklist -->
        <div class="form-group checklist-box">
          <label class="checklist-title">Service Checklist</label>
          <ul id="checklist-display" class="checklist">
            <li class="placeholder">Select an appointment to see the checklist</li>
          </ul>
        </div>

        <!-- Status -->
        <div class="form-group">
          <label>Status</label>
          <select name="status">
            <option value="open">open</option>
            <option value="in_progress">in_progress</option>
            <option value="on_hold">on_hold</option>
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

    if (appt) {
        appt.addEventListener('change', function () {
            applyFromOption(appt.options[appt.selectedIndex]);
        });

        // Apply service & checklist if a value is pre-selected
        if (appt.value) {
            applyFromOption(appt.options[appt.selectedIndex]);
        }
    }
})();
document.addEventListener("DOMContentLoaded", function () {
    const toast = document.querySelector('.toast');
    if (toast) {
        setTimeout(() => {
            toast.classList.add('hide');
        }, 3000); // hide after 5 seconds
    }
});

</script>

</body>
</html>
