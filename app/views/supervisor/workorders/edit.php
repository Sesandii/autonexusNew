<?php
  $base = rtrim(BASE_URL,'/');
  $wo = $wo ?? [];
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Edit Work Order #<?= htmlspecialchars($wo['work_order_id']) ?></title>
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/supervisor/style-workorders.css">
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/supervisor/forms.css">
</head>
<body>
<div class="sidebar">
  <div class="logo-container">
    <img src="/autonexus/public/assets/img/Auto.png" alt="Logo" class="logo">
  </div>
  <h2>AUTONEXUS</h2>
  <a href="/autonexus/supervisor/dashboard"><img src="/autonexus/public/assets/img/dashboard.png"/>Dashboard</a>
  <a href="/autonexus/supervisor/workorders" class="nav active">
    <img src="/autonexus/public/assets/img/jobs.png"/>Work Orders
  </a>
  <a href="/autonexus/supervisor/assignedjobs"><img src="/autonexus/public/assets/img/assigned.png"/>Assigned Jobs</a>
  <a href="/autonexus/supervisor/history"><img src="/autonexus/public/assets/img/history.png"/>Vehicle History</a>
  <a href="/autonexus/supervisor/complaints"><img src="/autonexus/public/assets/img/Complaints.png"/>Complaints</a>
  <a href="/autonexus/supervisor/feedbacks"><img src="/autonexus/public/assets/img/Feedbacks.png"/>Feedbacks</a>
  <a href="/autonexus/supervisor/reports"><img src="/autonexus/public/assets/img/Inspection.png"/>Report</a>
</div>

<main class="container">
  <div class="page-header">
    <div>
      <h1>Edit Work Order #<?= htmlspecialchars($wo['work_order_id']) ?></h1>
      <p class="subtitle">View appointment and update work order details.</p>
    </div>
  </div>

  <div class="form-card">
    <form method="post" action="<?= $base ?>/supervisor/workorders/<?= (int)$wo['work_order_id'] ?>">
      <div class="form-grid">

        <!-- Appointment (read-only) -->
        <div class="form-group">
          <label class="required">Appointment</label>
          <select id="appointment_id" disabled>
            <option value="<?= (int)$wo['appointment_id'] ?>" selected>
              #<?= (int)$wo['appointment_id'] ?> — <?= htmlspecialchars($wo['appointment_date'] ?? '') ?> <?= htmlspecialchars($wo['appointment_time'] ?? '') ?> — <?= htmlspecialchars($wo['service_name'] ?? '') ?>
            </option>
          </select>
          <!-- Hidden input to submit the value -->
          <input type="hidden" name="appointment_id" value="<?= (int)$wo['appointment_id'] ?>">
          <div class="help">The appointment cannot be changed for this work order.</div>
        </div>

        <!-- Mechanic -->
        <div class="form-group">
          <label>Mechanic</label>
          <select name="mechanic_id">
            <option value="">-- unassigned --</option>
            <?php foreach ($activeMechanics as $m): ?>
              <option value="<?= (int)$m['mechanic_id'] ?>" <?= ((int)($wo['mechanic_id'] ?? 0) === (int)$m['mechanic_id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($m['mechanic_code']) ?> — <?= htmlspecialchars($m['specialization']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Service (from appointment) -->
        <div class="form-group">
          <label>Service (from appointment)</label>
          <input type="text" id="service_display" value="<?= htmlspecialchars($wo['service_name'] ?? '') ?>" readonly>
        </div>

        <!-- Default Price -->
        <div class="form-group">
          <label>Default Price</label>
          <div class="input-addon">
            <span>LKR</span>
            <input type="number" id="service_price" value="<?= htmlspecialchars($wo['default_price'] ?? '0') ?>" readonly>
          </div>
        </div>

        <!-- Service Summary -->
        <div class="form-group" style="grid-column:1/-1">
          <label>Service Summary</label>
          <textarea name="service_summary"><?= htmlspecialchars($wo['service_summary'] ?? '') ?></textarea>
        </div>

        <!-- Total Cost -->
        <div class="form-group">
          <label>Total Cost</label>
          <div class="input-addon">
            <span>LKR</span>
            <input type="number" step="0.01" name="total_cost" id="total_cost" value="<?= htmlspecialchars($wo['total_cost'] ?? '0') ?>">
          </div>
        </div>

        <!-- Status -->
        <div class="form-group">
          <label>Status</label>
          <select name="status">
            <?php foreach (['open','in_progress','completed'] as $st): ?>
              <option value="<?= $st ?>" <?= (($wo['status'] ?? 'open') === $st) ? 'selected' : '' ?>><?= $st ?></option>
            <?php endforeach; ?>
          </select>
        </div>

      </div>

      <div class="form-actions">
        <a class="btn" href="<?= $base ?>/supervisor/workorders">Cancel</a>
        <button class="btn primary" type="submit">Save changes</button>
      </div>
    </form>
  </div>
</main>

<script>
  // Initialize service & price fields (though appointment can't change)
  (function(){
    const svc  = document.getElementById('service_display');
    const prc  = document.getElementById('service_price');
    const tot  = document.getElementById('total_cost');

    // Populate total cost if empty
    if (!tot.value || +tot.value === 0) {
      tot.value = prc.value || '0';
    }
  })();
</script>
</body>
</html>
