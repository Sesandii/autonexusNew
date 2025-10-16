<?php $current = 'progress'; // highlights “Service Progress” in sidebar ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Ongoing Services</title>



  <link rel="stylesheet" href="../admin-sidebar/styles.css">   <!-- fixed sidebar styles -->
  <link rel="stylesheet" href="styles.css">                    <!-- this page’s styles -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>

  <?php include(__DIR__ . '/../admin-sidebar/sidebar.php'); ?>

  <main class="main-content">
    <h2>Ongoing Services</h2>

    <div class="filters">
      <label>
        Branch:
        <select>
          <option>All</option>
        </select>
      </label>
      <label>
        Status:
        <select>
          <option>All</option>
        </select>
      </label>
    </div>

    <div class="cards">
      <!-- Card 1 -->
      <div class="card">
        <div class="card-header">
          <h2>Oil Change</h2>
          <div class="duration">30 min</div>
        </div>
        <p><strong>Customer:</strong> John Smith</p>
        <p><strong>Assigned to:</strong> Mike Johnson</p>
        <p><strong>Time Remaining:</strong> 15 min</p>
        <div class="progress-bar">
          <span class="step active">Received</span>
          <span class="step active">In Service</span>
          <span class="step">Completed</span>
          <div class="bar"><div class="progress in-service"></div></div>
        </div>
      </div>

      <!-- Card 2 -->
      <div class="card">
        <div class="card-header">
          <h2>Brake Inspection</h2>
          <div class="duration">45 min</div>
        </div>
        <p><strong>Customer:</strong> Sarah Davis</p>
        <p><strong>Assigned to:</strong> Lisa Brown</p>
        <p><strong>Time Remaining:</strong> 30 min</p>
        <div class="progress-bar">
          <span class="step active">Received</span>
          <span class="step active">In Service</span>
          <span class="step">Completed</span>
          <div class="bar"><div class="progress in-service"></div></div>
        </div>
      </div>

      <!-- Card 3 -->
      <div class="card">
        <div class="card-header">
          <h2>Tire Rotation</h2>
          <div class="duration">60 min</div>
        </div>
        <p><strong>Customer:</strong> Robert Wilson</p>
        <p><strong>Assigned to:</strong> David Miller</p>
        <p><strong>Time Remaining:</strong> 45 min</p>
        <div class="progress-bar">
          <span class="step active">Received</span>
          <span class="step">In Service</span>
          <span class="step">Completed</span>
          <div class="bar"><div class="progress received"></div></div>
        </div>
      </div>

      <!-- Card 4 -->
      <div class="card">
        <div class="card-header">
          <h2>Engine Diagnostic</h2>
          <div class="duration">90 min</div>
        </div>
        <p><strong>Customer:</strong> Emily Clark</p>
        <p><strong>Assigned to:</strong> Mike Johnson</p>
        <p><strong>Time Remaining:</strong> 20 min</p>
        <div class="progress-bar">
          <span class="step active">Received</span>
          <span class="step active">In Service</span>
          <span class="step">Completed</span>
          <div class="bar"><div class="progress in-service"></div></div>
        </div>
      </div>
    </div>
  </main>

</body>
</html>
