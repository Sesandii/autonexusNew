<?php $current = 'appointments'; // highlights “Service Progress” in sidebar ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Appointments - AutoNexus</title>

  <link rel="stylesheet" href="../admin-sidebar/styles.css">   <!-- fixed sidebar styles -->
  <link rel="stylesheet" href="style.css">                    <!-- this page’s styles -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <script src="script.js"></script>

</head>
<body>
    <?php include(__DIR__ . '/../admin-sidebar/sidebar.php'); ?>
  

    <main class="main-content">
    

      <section class="appointments-section">
        <h2>Appointments Management</h2>

        <div class="filters">
          <input type="text" placeholder="Search by customer or service..." />
          <select>
            <option>All Status</option>
            <option>Scheduled</option>
            <option>In Progress</option>
            <option>Completed</option>
            <option>Cancelled</option>
          </select>
          <input type="date" />
        </div>

        <div class="cards-container">
          <!-- Card Example -->
          <div class="card">
            <div class="card-header">
              <strong>John Smith</strong>
              <span class="badge scheduled">Scheduled</span>
            </div>
            <p>Service: Oil Change</p>
            <p>Branch: Downtown Branch</p>
            <p>Time: Nov 10, 9:00 AM</p>
            <div class="card-actions">
              <button class="edit-btn">Edit</button>
              <button class="cancel-btn">Cancel</button>
            </div>
          </div>

          <!-- Duplicate cards below for other customers -->
          <div class="card">
            <div class="card-header">
              <strong>Sarah Williams</strong>
              <span class="badge in-progress">In Progress</span>
            </div>
            <p>Service: Brake Inspection</p>
            <p>Branch: Uptown Branch</p>
            <p>Time: Nov 10, 10:30 AM</p>
            <div class="card-actions">
              <button class="edit-btn">Edit</button>
              <button class="cancel-btn">Cancel</button>
            </div>
          </div>

          <div class="card">
            <div class="card-header">
              <strong>Robert Brown</strong>
              <span class="badge completed">Completed</span>
            </div>
            <p>Service: Tire Rotation</p>
            <p>Branch: Midtown Branch</p>
            <p>Time: Nov 10, 1:15 PM</p>
            <div class="card-actions">
              <button class="edit-btn">Edit</button>
              <button class="cancel-btn">Cancel</button>
            </div>
          </div>

        </div>
      </section>
    </main>

</body>
</html>
