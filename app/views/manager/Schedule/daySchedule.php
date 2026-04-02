<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Team Schedule - AutoNexus</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/sidebar.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/daySchedule.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/calender.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
   
<?php include APP_ROOT . '/views/layouts/manager-sidebar.php'; ?>

  <div class="main">
    <div class="schedule">
          <h4>Schedule Today -July 28, 2025</h4>
          <div class="schedule-item">
            <div class="schedule-header">
                <p><b>John Smith</b> <br><span><small>Senior Mechanic</small></span><br>07:30 - 16:00</p>
            </div>
            <div class="schedule-content">
                <p>Vehicl Info    : 2018 Toyota Camry (ABC-1234)</p><br/>
                <p>Description    : Oil Change and Filter Replacement </p><br/>
                <p>Time allocated : 45 minutes</p><br/>
                <p>Price          : Rs.15000/- </p><br/>
            </div>
           </div>

           <div class="schedule-item">
            <div class="schedule-header">
                <p><b>Maria Garcia</b> <br><span><small>Service Advisor</small></span><br>09:00 - 18:00</p>
            </div>
            <div class="schedule-content">
                <p>Additional info goes here. You can add notes, tasks, or buttons.</p>
            </div>
           </div>

           <div class="schedule-item">
            <div class="schedule-header">
                <p><b>David Lee</b> <br><span><small>Technician</small></span><br>08:00 - 16:30</p>
            </div>
            <div class="schedule-content">
                <p>Additional info goes here. You can add notes, tasks, or buttons.</p>
            </div>
           </div>

           <div class="schedule-item">
            <div class="schedule-header">
                <p><b>David Lee</b> <br><span><small>Technician</small></span><br>08:00 - 16:30</p>
            </div>
            <div class="schedule-content">
                <p>Additional info goes here. You can add notes, tasks, or buttons.</p>
            </div>
           </div>

    <script>
    const BASE_URL = "<?= BASE_URL ?>";
    </script>
    <script src="<?= BASE_URL ?>/public/assets/js/manager/calenderTeamSchedule.js"></script>   
    <script src="<?= BASE_URL ?>/public/assets/js/manager/daySchedule.js"></script>


      </body>
      </html>

