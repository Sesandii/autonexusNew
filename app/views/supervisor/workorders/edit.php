<?php
  $base = rtrim(BASE_URL,'/');
  $wo = $wo ?? [];
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Edit Work Order <?= htmlspecialchars($wo['work_order_id']) ?></title>
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/supervisor/style-workorders.css">
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/supervisor/forms.css">
</head>
<body>

<?php include __DIR__ . '/../partials/sidebar.php'; ?>

<main class="container">
<div class="breadcrumb-text">
    Supervisor <span class="sep">&gt;</span> 
    Work Orders <span class="sep">&gt;</span> 
    #<?= htmlspecialchars($wo['work_order_id']) ?> <span class="sep">&gt;</span> 
    <span class="active-page">Edit</span>
  </div>
  <div class="page-header">
    <div class="header">
      <h1>Edit Work Order</h1>
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
              #<?= (int)$wo['appointment_id'] ?> —
              <?= htmlspecialchars($wo['appointment_date'] ?? '') ?>
              <?= htmlspecialchars($wo['appointment_time'] ?? '') ?> —
              <?= htmlspecialchars($wo['service_name'] ?? '') ?>
            </option>
          </select>
          <input type="hidden" name="appointment_id" value="<?= (int)$wo['appointment_id'] ?>">
          <div class="help">The appointment cannot be changed for this work order.</div>
        </div>

        <!-- Mechanic -->
        <div class="form-group">
    <label class="required">Mechanic</label>
    <select name="mechanic_id" id="mechanic_id" class="form-control">
    <option value="">-- Select Mechanic --</option>
    <?php foreach ($activeMechanics as $mech): 
        // Normalize status for reliable comparison
        $rawStatus = $mech['status'] ?? 'available';
        $status = strtolower($rawStatus); 
        $isSelected = ($selectedMechanicId == $mech['mechanic_id']);
        
        // Default UI states
        $style = "";
        $labelSuffix = "";
        $isDisabledAttr = "";

        // Logic for specialized statuses
        if ($status === 'on break') {
            $style = "filter: blur(1px); opacity: 0.5; color: #999;";
            $labelSuffix = " (On Break)";
            // Only disable if it's NOT the currently assigned mechanic
            if (!$isSelected) $isDisabledAttr = "disabled"; 
            
        } elseif ($status === 'busy') {
            $labelSuffix = " (Busy - 5+ Jobs)";
            // We keep them enabled so supervisors can still assign if urgent
            $style = "color: #d9534f; font-weight: bold;"; 
            
        } elseif ($status === 'off-duty') {
            // Hide off-duty mechanics entirely unless they are already assigned to this WO
            if (!$isSelected) continue; 
            $labelSuffix = " (Off-Duty)";
            $isDisabledAttr = "disabled";
        }
    ?>
        <option value="<?= $mech['mechanic_id'] ?>" 
                style="<?= $style ?>" 
                <?= $isDisabledAttr ?>
                <?php 
            // Compare the mechanic in the loop to the mechanic saved in the work order
            if ((int)$mech['mechanic_id'] === (int)($wo['mechanic_id'] ?? 0)) {
                echo 'selected';
            }
        ?>>
            <?= htmlspecialchars($mech['mechanic_code']) ?> - 
            <?= htmlspecialchars($mech['specialization']) ?> 
            <?= $labelSuffix ?>
        </option>
    <?php endforeach; ?>
</select>
</div>

        <!-- Service Name (readonly) -->
        <div class="form-group">
          <label>Service (from appointment)</label>
          <input type="text" id="service_display"
            value="<?= htmlspecialchars($wo['service_name'] ?? '') ?>" readonly>
        </div>
        <!-- Status -->
        <div class="form-group">
          <label class="required">Status</label>
          <select name="status">
            <?php foreach (['open','in_progress','on_hold','completed'] as $st): ?>
              <option value="<?= $st ?>"
                <?= (($wo['status'] ?? 'open') === $st) ? 'selected' : '' ?>>
                <?= $st ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

                <!-- Service Summary -->
                <div class="form-group">
          <label>Service Summary</label>
          <textarea name="service_summary"><?= htmlspecialchars($wo['service_summary'] ?? '') ?></textarea>
        </div>

        <div class="form-group checklist-box">
    <label class="checklist-title">Service Checklist</label>

    <div id="checklist-container">
        <?php if (!empty($checklist)): ?>
            <?php foreach ($checklist as $item): ?>
              <div class="checklist-item">
    <input type="text" name="checklist[]" value="<?= htmlspecialchars($item['item_name'], ENT_QUOTES) ?>" placeholder="Checklist item">
    <button type="button" class="btn-remove" onclick="this.parentElement.remove()">⛔</button>
</div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <button type="button" class="btn-add" onclick="addChecklistItem()">
        Add Item
    </button>
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
function addChecklistItem() {
    const container = document.getElementById('checklist-container');
    const div = document.createElement('div');
    div.classList.add('checklist-item');

    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'checklist[]';
    input.placeholder = 'Checklist item';
    input.required = true;

    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'btn-remove';
    btn.innerText = '⛔';
    btn.onclick = () => div.remove();

    div.appendChild(input);
    div.appendChild(btn);
    container.appendChild(div);
}
</script>

</body>
</html>
