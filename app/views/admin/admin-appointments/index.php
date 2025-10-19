<?php
/** @var array $appointments */
/** @var string $pageTitle */
/** @var string $current */
?>
<?php $current = $current ?? 'appointments'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($pageTitle ?? 'Appointments') ?></title>

  <link rel="stylesheet" href="<?= rtrim(BASE_URL,'/') ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="<?= rtrim(BASE_URL,'/') ?>/public/assets/css/admin/appointments/style.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <script defer src="<?= rtrim(BASE_URL,'/') ?>/assets/js/admin/appointments/script.js"></script>
</head>
<body>
  <?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

  <main class="main-content">
    <section class="appointments-section">
      <h2>Appointments Management</h2>

      <div class="filters">
        <input id="searchInput" type="text" placeholder="Search by customer or service..." />
        <select id="statusSelect">
          <option value="">All Status</option>
          <option>Scheduled</option>
          <option>In Progress</option>
          <option>Completed</option>
          <option>Cancelled</option>
        </select>
        <input id="dateInput" type="date" />
      </div>

      <div id="cardsContainer" class="cards-container">
        <?php foreach ($appointments as $a): 
          $dateISO = (new DateTime($a['datetime']))->format('Y-m-d');
          $timeFmt = (new DateTime($a['datetime']))->format('M j, g:i A');
          $status  = $a['status'];
          $badgeClass = [
            'Scheduled'   => 'scheduled',
            'In Progress' => 'in-progress',
            'Completed'   => 'completed',
            'Cancelled'   => 'cancelled',
          ][$status] ?? 'scheduled';
        ?>
        <div class="card"
             data-id="<?= (int)$a['id'] ?>"
             data-customer="<?= htmlspecialchars($a['customer']) ?>"
             data-service="<?= htmlspecialchars($a['service']) ?>"
             data-status="<?= htmlspecialchars($status) ?>"
             data-date="<?= $dateISO ?>">
          <div class="card-header">
            <strong><?= htmlspecialchars($a['customer']) ?></strong>
            <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($status) ?></span>
          </div>
          <p>Service: <?= htmlspecialchars($a['service']) ?></p>
          <p>Branch: <?= htmlspecialchars($a['branch']) ?></p>
          <p>Time: <?= htmlspecialchars($timeFmt) ?></p>
          <div class="card-actions">
            <button class="edit-btn" data-action="edit"><i class="fa-regular fa-pen-to-square"></i> Edit</button>
            <button class="cancel-btn" data-action="cancel"><i class="fa-regular fa-circle-xmark"></i> Cancel</button>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </section>
  </main>
</body>
</html>
