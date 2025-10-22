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
      <img src="/autonexus/public/assets/img/assigned.png"/>Assigned Jobs
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
        <div class="user-profile">
          <img src="/autonexus/public/assets/img/bell.png" alt="Notifications" class="icon" />
          <img src="/autonexus/public/assets/img/user.png" alt="User" class="avatar-img" />
          <span>John Doe</span>
        </div>
      </header>
      <section class="job-section">
       <p>Overview of all ongoing jobs</p>
        <h2>Ongoing Jobs</h2>
        <table>
          <thead>
            <tr>
              <th>Customer</th>
              <th>Vehicle</th>
              <th>Service Type</th>
              <th>ETA</th>
              <th>Mechanic</th>
              <th>Supervisor</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="job-table-body">
            <!-- Populated by JavaScript -->
          </tbody>
        </table>
      </section>
    </main>
  <script src="/autonexus/public/assets/js/supervisor/script-assignedjobs.js"></script>
</body>
</html>
