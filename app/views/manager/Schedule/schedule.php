<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Team Schedule - AutoNexus</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/sidebar.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/Schedule.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/calender.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
  
<?php include APP_ROOT . '/views/layouts/manager-sidebar.php'; ?>

  <div class="main">
    <div class="header">
      <h2>Team Schedule</h2>
    </div>

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
          <h4>Schedule Today -July 28, 2025</h4>
          <div class="task">
            <p><b>John Smith</b> <br><span>Senior Mechanic</span><br>07:30 - 16:00</p>
            <span class="edit"><a href="updateTask.html">✎</a></span>
          </div>
          <div class="task">
            <p><b>Maria Garcia</b> <br><span>Service Advisor</span><br>09:00 - 18:00</p>
            <span class="edit">✎</span>
          </div>
          <div class="task">
            <p><b>David Lee</b> <br><span>Technician</span><br>08:00 - 16:30</p>
            <span class="edit">✎</span>
          </div>
          <div class="task">
            <p><b>Sarah Johnson</b> <br><span>Parts Specialist</span><br>10:00 - 19:00</p>
            <span class="edit">✎</span>
          </div>
        </div>
      </div>

      <div class="right-section">

      <!-- Team box -->
      <div class="team-box">
        <h4>
          Team Members
          <span class="count">6</span>
        </h4>

        <div class="all-members">All Team Members</div>

        <ul class="members">
    <?php if (!empty($users)) : ?>
        <?php foreach ($users as $user) : ?>
            
               <a href="<?= BASE_URL ?>/manager/schedule/member?id=<?= $user['user_id'] ?>">

               <li> 
               <span class="circle purple"><?= strtoupper(substr($user['first_name'], 0, 1)) ?></span>
                <div class="info">
                    <span class="name"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></span>
                    <span class="role"><?= htmlspecialchars($user['role']) ?></span>
                </div>
                </li>
        </a>
            
        <?php endforeach; ?>
    <?php else: ?>
        <li>No team members found.</li>
    <?php endif; ?>
</ul>

      
  </div>

<script>
  const BASE_URL = "<?= BASE_URL ?>";
</script>
<script src="<?= BASE_URL ?>/public/assets/js/manager/calenderTeamSchedule.js"></script>
</body>
</html>
