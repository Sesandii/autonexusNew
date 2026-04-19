<?php $base = rtrim(BASE_URL, '/'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Coordinate | AutoNexus</title>
  <link rel="stylesheet" href="/autonexus/public/assets/css/supervisor/style-coordination.css"/>
</head>
<body>
<?php include __DIR__ . '/../partials/sidebar.php'; ?>

<?php 
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (isset($_SESSION['message'])): 
    $message = $_SESSION['message'];
?>
    <div class="toast-container" id="toast-notification">
        <div class="toast-message <?= $message['type'] ?>">
            <span><?= htmlspecialchars($message['text']) ?></span>
        </div>
    </div>
<?php 
    unset($_SESSION['message']); 
endif; 
?>
<main class="main-content">
<div class="breadcrumb-text">
    Supervisor <span class="sep">&gt;</span> 
    Coordinate <span class="sep"></span> 
  </div>
<h1>Mechanic Coordination</h1>
<form method="get" class="filter-form">
<div class="filter-form">
    <select id="filter-code">
        <option value="">All Mechanic Codes</option>
        <?php
        $codes = array_unique(array_column($mechanics, 'mechanic_code'));
        foreach ($codes as $code): ?>
            <option value="<?= htmlspecialchars($code) ?>"><?= htmlspecialchars($code) ?></option>
        <?php endforeach; ?>
    </select>
    
    <select id="filter-spec">
        <option value="">All Specializations</option>
        <?php 
        $specializations = array_unique(array_column($mechanics, 'specialization'));
        foreach ($specializations as $spec): ?>
            <option value="<?= htmlspecialchars($spec) ?>"><?= htmlspecialchars($spec) ?></option>
        <?php endforeach; ?>
    </select>
    <select id="filter-status">
        <option value="">All Statuses</option>
        <?php
        $statuses = ['Active', 'Busy', 'On Break', 'Off-Duty'];
        foreach ($statuses as $st): ?>
            <option value="<?= $st ?>"><?= $st ?></option>
        <?php endforeach; ?>
    </select>
    <button type="button" id="reset-filters" class="btn reset">Reset</button>
</div>
</form>
<div class="mechanic-board">

<?php foreach($mechanics as $mech): 
  $load = $this->workOrderModel->countWorkOrdersByMechanic($mech['mechanic_id']);
?>
<div class="mechanic-card <?= strtolower($mech['status']) ?>"
     data-mechanic="<?= $mech['mechanic_id'] ?>"
     data-code="<?= htmlspecialchars($mech['mechanic_code']) ?>"
     data-spec="<?= htmlspecialchars($mech['specialization']) ?>"
     data-status="<?= $mech['status'] ?>">


  <h3><?= $mech['mechanic_code'] ?></h3>
  <p>Name: <?= htmlspecialchars($mech['first_name'] . ' ' . $mech['last_name']) ?></p>
  <p>Skill: <?= $mech['specialization'] ?></p>
  <p>Status: <strong><?= $mech['status'] ?></strong></p>
  <p>Active Work Orders: <?= $load ?></p>

  <div class="workorder-list">
    <?php foreach($mech['scheduled_orders'] as $wo):
      if($wo['mechanic_id'] == $mech['mechanic_id']): ?>
<?php 
$start = new DateTime($wo['calculated_start']);
$end   = new DateTime($wo['calculated_end']);
?>

<a href="<?= BASE_URL ?>/supervisor/assignedjobs/<?= $wo['work_order_id'] ?>" class="workorder-card-link">
    <div class="workorder-card">
        <div class="wo-time">
            <span><?= $start->format('h:i A') ?> -</span>
            <span><?= $end->format('h:i A') ?></span>
        </div>

        <div class="wo-status <?= strtolower($wo['status']) ?>">
            <?= strtoupper(htmlspecialchars($wo['status'])) ?>
        </div>

        <div class="wo-service">
            <?= htmlspecialchars($wo['name']) ?>
        </div>
    </div>
</a>
    <?php endif; endforeach; ?>
  </div>

  <form method="post" action="<?= BASE_URL ?>/supervisor/coordination/updateMechanicStatus">
    <input type="hidden" name="mechanic_id" value="<?= $mech['mechanic_id'] ?>">
    <input type="hidden" name="mechanic_code" value="<?= htmlspecialchars($mech['mechanic_code']) ?>">
    <select name="status">
    <?php $currentStatus = trim($mech['status']); ?>
    <option value="Active" <?= strcasecmp($currentStatus, 'Active') == 0 ? 'selected' : '' ?>>Active</option>
    <option value="Busy" <?= strcasecmp($currentStatus, 'Busy') == 0 ? 'selected' : '' ?>>Busy</option>
    <option value="On Break" <?= strcasecmp($currentStatus, 'On Break') == 0 ? 'selected' : '' ?>>On Break</option>
    <option value="Off-Duty" <?= strcasecmp($currentStatus, 'Off-Duty') == 0 ? 'selected' : '' ?>>Off-Duty</option>
</select>
    <button class="update-btn">Update</button>
  </form>
<form method="get" action="<?= BASE_URL ?>/supervisor/workorders/create">
    <input type="hidden" name="mechanic_id" value="<?= $mech['mechanic_id'] ?>">
    <input type="hidden" name="mechanic_spec" value="<?= htmlspecialchars($mech['specialization']) ?>">
    <button type="submit" class="assign-btn">Assign Job</button>
</form>
</div>
<?php endforeach; ?>
</div>
</main>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const codeSelect = document.getElementById('filter-code');
    const specSelect = document.getElementById('filter-spec');
    const statusSelect = document.getElementById('filter-status');
    const resetBtn = document.getElementById('reset-filters');
    const mechanicCards = document.querySelectorAll('.mechanic-card');

    function filterMechanics() {
        const codeVal = codeSelect.value.trim().toLowerCase();
        const specVal = specSelect.value.trim().toLowerCase();
        const statusVal = statusSelect.value.trim().toLowerCase();

        mechanicCards.forEach(card => {
            const cardCode = (card.getAttribute('data-code') || "").trim().toLowerCase();
            const cardSpec = (card.getAttribute('data-spec') || "").trim().toLowerCase();
            const cardStatus = (card.getAttribute('data-status') || "").trim().toLowerCase();

            const matchesCode = !codeVal || cardCode.includes(codeVal);
            const matchesSpec = !specVal || cardSpec === specVal;
            const matchesStatus = !statusVal || cardStatus === statusVal;

            if (matchesCode && matchesSpec && matchesStatus) {
                card.classList.remove('hidden-card');
            } else {
                card.classList.add('hidden-card');
            }
        });
    }

    window.addEventListener('DOMContentLoaded', () => {
    const toast = document.querySelector('.toast');
    if (toast) {
        setTimeout(() => {
            toast.style.transition = 'opacity 0.5s';
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 500);
        }, 3000);
    }
});


    codeSelect.addEventListener('change', filterMechanics);
    specSelect.addEventListener('change', filterMechanics);
    statusSelect.addEventListener('change', filterMechanics);

    resetBtn.addEventListener('click', () => {
        codeSelect.value = '';
        specSelect.value = '';
        statusSelect.value = '';
        filterMechanics();
    });

    filterMechanics();
});
</script>
</body>
</html>