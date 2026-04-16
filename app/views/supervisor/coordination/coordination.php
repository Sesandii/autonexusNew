<?php $base = rtrim(BASE_URL, '/'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Daily Report | AutoNexus</title>
  <link rel="stylesheet" href="/autonexus/public/assets/css/supervisor/style-coordination.css"/>
</head>
<body>
<?php include __DIR__ . '/../partials/sidebar.php'; ?>
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
        $statuses = ['Available', 'Busy', 'On Break', 'Off-Duty'];
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
     data-status="<?= htmlspecialchars($mech['status']) ?>">


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
  <option value="Available" <?= $mech['status'] == 'Available' ? 'selected' : '' ?>>Available</option>
  <option value="Busy" <?= $mech['status'] == 'Busy' ? 'selected' : '' ?>>Busy</option>
  <option value="On Break" <?= $mech['status'] == 'On Break' ? 'selected' : '' ?>>On Break</option>
  <option value="Off-Duty" <?= $mech['status'] == 'Off-Duty' ? 'selected' : '' ?>>Off-Duty</option>
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
const codeInput = document.getElementById('filter-code');
const specSelect = document.getElementById('filter-spec');
const statusSelect = document.getElementById('filter-status');
const resetBtn = document.getElementById('reset-filters');
const mechanicCards = document.querySelectorAll('.mechanic-card');

function filterMechanics() {
    const codeVal = codeInput.value.toLowerCase();
    const specVal = specSelect.value;
    const statusVal = statusSelect.value;

    mechanicCards.forEach(card => {
        const code = card.dataset.code.toLowerCase();
        const spec = card.dataset.spec;
        const status = card.dataset.status;

        const matchesCode = !codeVal || code === codeVal;
        const matchesSpec = !specVal || spec === specVal;
        const matchesStatus = !statusVal || status === statusVal;

        if (matchesCode && matchesSpec && matchesStatus) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

codeInput.addEventListener('change', filterMechanics);
specSelect.addEventListener('change', filterMechanics);
statusSelect.addEventListener('change', filterMechanics);

resetBtn.addEventListener('click', () => {
    codeInput.value = '';
    specSelect.value = '';
    statusSelect.value = '';
    filterMechanics();
});

filterMechanics();

</script>
</body>
</html>