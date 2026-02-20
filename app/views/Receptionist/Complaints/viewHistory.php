<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Vehicle History</title>

  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/sidebar.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/viewHistory.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>

<body>
  
<?php include APP_ROOT . '/views/layouts/receptionist-sidebar.php'; ?>

  <div class="main">
    <div class="service-history">
          <h4>Service History</h4>
          <div class="history-item">
            <div class="service-used">
                <p><b>30,000 Mile Service</b> <br><span><small>2023-06-15</small></span></p>
            </div>
            <div class="history-content">
                <div class="label">Technician:</div>
                <div class="value">John Smith</div>

                <div class="label">Cost:</div>
                <div class="value">Rs. 30,000</div>

                <div class="label">Notes:</div>
                <div class="value">Completed full service package including oil change, Filter Replacement, Multi-point Inspection.</div>
            </div>
           </div>

           <div class="history-item">
            <div class="service-used">
                <p><b>Brake Pad Replacement</b> <br><span><small>2023-10-05</small></span></p>
            </div>
            <div class="history-content">
                <div class="label">Technician:</b></div>
                <div class="value">Bill Hawkins</div>

                <div class="label"></b>Cost:</div>
                <div class="value">Rs. 22,000</div>

                <div class="label">Notes:</div>
                <div class="value">Replaced front brake pads and resurfaced rotors.</div>
            </div>
           </div>

           <div class="history-item">
            <div class="service-used">
                <p><b>Oil Change</b> <br><span><small>2024-01-15</small></p>
            </div>
            <div class="history-content">
                <div class="label">Technician:</div>
                <div class="value">Bill Hawkins</div>

                <div class="label">Cost:</div>
                <div class="value">Rs. 10,000</div>

                <div class="label">Notes:</div>
                <div class="value">Full synthetic oil change and filter replacement.</div>
            </div>
           </div>

    </div>

    <div class="complaint-history">
          <h4>Complaint History</h4>
          <div class="complaint-item">
            <div class="complaint">
                <p><b>Tire pressure warning</b> <br><span><small>2023-06-24</small></span></p>
            </div>
            <div class="complaint-content">
                <div><b>Status:</b></div>
                <div>Resolved</div>

                <div><b>Solution:</b></div>
                <div>Found slow leak in right front tire. Patched and reinflated all tires to proper PSI.</div>
            </div>
    </div>
</div>

           <script src="<?= BASE_URL ?>/public/assets/js/receptionist/viewHistory.js"></script>


        </body>
        </html>

    
