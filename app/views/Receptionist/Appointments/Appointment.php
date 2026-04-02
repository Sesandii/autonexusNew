
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Appointments - AutoNexus</title>

  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/sidebar.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/appointments.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/calender.css">
  
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>

  <!-- Include sidebar -->
  <?php include APP_ROOT . '/views/layouts/receptionist-sidebar.php'; ?>

  <div class="main">
    <div class="header">
  <h2>Appointments</h2>

  <div class="top-actions">
    <!-- Create New Appointment Button -->
 <button class="add-btn" 
        onclick="window.location.href='<?= BASE_URL ?>/public/receptionist/appointments/new'">
  + Create New Appointment
</button>


    <!-- Search Appointment Bar -->
    <div class="search-bar">
      <input type="text" id="searchInput" placeholder="Search appointment..." />
      <button id="searchBtn">🔍</button>
    </div>
  </div>
</div>

<div class="main-content">
    <div class="calendar">
    <div class="calendar-header">
      <button id="prev">◀</button>
      <h2 id="month-year"></h2>
      <button id="next">▶</button>
    </div>
    <div class="calendar-grid" id="calendar-grid">
      <div class="day-name">Sun</div>
      <div class="day-name">Mon</div>
      <div class="day-name">Tue</div>
      <div class="day-name">Wed</div>
      <div class="day-name">Thu</div>
      <div class="day-name">Fri</div>
      <div class="day-name">Sat</div>
    </div>
  </div>

 

        <div class="schedule">
  <h4>Today's Appointments</h4>

  <?php if (!empty($appointments)): ?>
      <?php foreach ($appointments as $app): ?>
          <div class="task">
            <p>
              <b><?= htmlspecialchars($app['first_name'] . ' ' . $app['last_name']) ?></b> <br>
              <span><?= htmlspecialchars($app['make'] . ' ' . $app['model'] . ' (' . $app['license_plate'] . ')') ?></span><br>
              <?= date('H:i', strtotime($app['appointment_time'])) ?>
            </p>
          </div>
      <?php endforeach; ?>
  <?php else: ?>
      <p>No appointments today.</p>
  <?php endif; ?>
</div>
        </div>
      </div>

  </div>   
    
    </div>

      
  </div>

<script>
  const BASE_URL = "<?= BASE_URL ?>";
</script>
<script src="<?= BASE_URL ?>/public/assets/js/receptionist/calender.js"></script>
</body>
</html>
