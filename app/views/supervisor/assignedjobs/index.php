<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Auto Shop Dashboard</title>
  <link rel="stylesheet" href="/autonexus/public/assets/css/supervisor/style-assignedjobs.css"/>
</head>
<body>
  <div class="sidebar">
     <div class="logo-container">
     <img src="/autonexus/public/assets/img/Auto.png" alt="Logo" class="logo">
     </div>
      <h2>AUTONEXUS</h2>
    <a href="/autonexus/supervisor/dashboard">
      <img src="/autonexus/public/assets/img/dashboard.png"/>Dashboard
    </a>
    <a href="/autonexus/supervisor/workorders">
      <img src="/autonexus/public/assets/img/jobs.png"/>Work Orders
    </a>
    <a href="/autonexus/supervisor/assignedjobs" class="nav">
      <img src="/autonexus/public/assets/img/assigned.png"/>Assigned
    </a>
    <a href="/autonexus/supervisor/history">
      <img src="/autonexus/public/assets/img/history.png"/>Vehicle History
    </a>
    <a href="/autonexus/supervisor/complaints">
      <img src="/autonexus/public/assets/img/Complaints.png"/>Complaints
     </a>
      <a href="/autonexus/supervisor/feedbacks">
      <img src="/autonexus/public/assets/img/Feedbacks.png"/>Feedbacks
     </a>
      <a href="/autonexus/supervisor/reports">
       <img src="/autonexus/public/assets/img/Inspection.png"/>Report
     </a>
    </div>
    <main class="main">
      <header>
        <input type="text" placeholder="Search..." class="search" />
</header>
  <section class="job-section">
  <p>Overview of all ongoing jobs</p>
  <h2>Ongoing Jobs</h2>

  <div class="job-grid" id="job-grid">
    <!-- Tiles will be dynamically inserted here by JS -->
  </div>
</section>
    </main>
  <script src="/autonexus/public/assets/js/supervisor/script-assignedjobs.js"></script>
</body>
</html>
