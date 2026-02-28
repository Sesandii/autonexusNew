<?php
// app/views/manager/viewschedules/index.php
$base = rtrim(BASE_URL, '/');
$todayLabel = date('F j, Y'); // e.g., July 28, 2025
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Team Schedule - AutoNexus</title>

  <!-- Sidebar CSS (remembered) -->
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/manager/sidebar.css">

  <!-- Page CSS -->
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/manager/schedule.css">
  <!-- (Optional) calendar helpers if you have a mini calendar on top -->
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/manager/calender.css">
</head>
<body>

  <?php include APP_ROOT . '/views/layouts/managersidebar.php'; ?>

  <div class="main">
    <header class="sched-header">
      <div>
        <h1>Today’s Schedule</h1>
        <p class="muted">Branch: <strong><?= (int)($_SESSION['branch_id'] ?? 0) ?: 'Current' ?></strong> • <?= htmlspecialchars($todayLabel, ENT_QUOTES, 'UTF-8') ?></p>
      </div>

      <form class="filters" method="get" action="<?= $base ?>/manager/schedule">
        <div class="field">
          <label>Mechanic</label>
          <select name="mechanic">
            <option value="">All</option>
            <option>John Smith</option>
            <option>David Lee</option>
            <option>Robert Chen</option>
          </select>
        </div>
        <div class="field">
          <label>Status</label>
          <select name="status">
            <option value="">All</option>
            <option>Not Arrived</option>
            <option>Waiting</option>
            <option>In Service</option>
            <option>Completed</option>
            <option>Canceled</option>
          </select>
        </div>
        <button class="btn primary" type="submit">Apply</button>
      </form>
    </header>

    <section class="card">
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>Time</th>
              <th>Service</th>
              <th>Customer</th>
              <th>Vehicle</th>
              <th>Mechanic</th>
              <th>Supervisor</th>
              <th>Status</th>
              <th class="t-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <!-- Row 1 -->
            <tr>
              <td>07:30 – 08:15</td>
              <td>
                <strong>Oil Change</strong><br>
                <span class="muted">Appointment #A-10234</span>
              </td>
              <td>Jane Doe</td>
              <td>Toyota Camry • ABC-1234</td>
              <td>John Smith</td>
              <td>Maria Garcia</td>
              <td><span class="badge gray">Not Arrived</span></td>
              <td class="t-right">
                <a class="btn outline" href="<?= $base ?>/manager/appointments">View</a>
                <a class="btn" href="<?= $base ?>/manager/schedule">Reassign</a>
                <a class="btn success" href="<?= $base ?>/manager/schedule">Start</a>
              </td>
            </tr>

            <!-- Row 2 -->
            <tr>
              <td>09:00 – 10:00</td>
              <td>
                <strong>Brake Inspection</strong><br>
                <span class="muted">Appointment #A-10242</span>
              </td>
              <td>Tom Hawk</td>
              <td>Honda Civic • XYZ-5678</td>
              <td>David Lee</td>
              <td>Maria Garcia</td>
              <td><span class="badge warn">Waiting</span></td>
              <td class="t-right">
                <a class="btn outline" href="<?= $base ?>/manager/appointments">View</a>
                <a class="btn" href="<?= $base ?>/manager/schedule">Reassign</a>
                <a class="btn success" href="<?= $base ?>/manager/schedule">Start</a>
              </td>
            </tr>

            <!-- Row 3 -->
            <tr>
              <td>10:30 – 11:30</td>
              <td>
                <strong>Full Inspection</strong><br>
                <span class="muted">Appointment #A-10259</span>
              </td>
              <td>Emily Johnson</td>
              <td>Ford Focus • QRS-9012</td>
              <td>Robert Chen</td>
              <td>Maria Garcia</td>
              <td><span class="badge info">In Service</span></td>
              <td class="t-right">
                <a class="btn outline" href="<?= $base ?>/manager/appointments">View</a>
                <a class="btn" href="<?= $base ?>/manager/schedule">Reassign</a>
                <a class="btn primary" href="<?= $base ?>/manager/schedule">Complete</a>
              </td>
            </tr>

            <!-- Row 4 -->
            <tr>
              <td>12:00 – 12:45</td>
              <td>
                <strong>Tire Rotation</strong><br>
                <span class="muted">Appointment #A-10271</span>
              </td>
              <td>Michael Brown</td>
              <td>BMW 3 Series • TUV-3456</td>
              <td>John Smith</td>
              <td>Maria Garcia</td>
              <td><span class="badge success">Completed</span></td>
              <td class="t-right">
                <a class="btn outline" href="<?= $base ?>/manager/appointments">View</a>
                <a class="btn" href="<?= $base ?>/manager/service-history">History</a>
              </td>
            </tr>

            <!-- Row 5 -->
            <tr>
              <td>12:30 – 13:15</td>
              <td>
                <strong>AC Check</strong><br>
                <span class="muted">Appointment #A-10277</span>
              </td>
              <td>Sarah Davis</td>
              <td>Audi A4 • WXY-7890</td>
              <td>David Lee</td>
              <td>Maria Garcia</td>
              <td><span class="badge danger">Canceled</span></td>
              <td class="t-right">
                <a class="btn outline" href="<?= $base ?>/manager/appointments">View</a>
                <a class="btn" href="<?= $base ?>/manager/schedule">Rebook</a>
              </td>
            </tr>

          </tbody>
        </table>
      </div>
    </section>
  </div>

  <!-- (Optional) page JS if needed later -->
  <!-- <script src="<?= $base ?>/public/assets/js/manager/daySchedule.js"></script> -->
</body>
</html>
