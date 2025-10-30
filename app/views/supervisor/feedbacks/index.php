<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Auto Shop Dashboard</title>
  <link rel="stylesheet" href="/autonexus/public/assets/css/supervisor/style-feedbacks.css"/>
</head>
<body>
  <div class="sidebar">
     <div class="logo-container">
     <img src="/autonexus/public/assets/img/Auto.png" alt="Logo" class="logo">
     </div>
      <h2>AUTONEXUS</h2>
      <a href="/autonexus/supervisor/dashboard" >
      <img src="/autonexus/public/assets/img/dashboard.png"/>Dashboard
    </a>
    <a href="/autonexus/supervisor/workorders" >
      <img src="/autonexus/public/assets/img/jobs.png"/>Work Orders
    </a>
    <a href="/autonexus/supervisor/assignedjobs">
      <img src="/autonexus/public/assets/img/assigned.png"/>Assigned
    </a>
    <a href="/autonexus/supervisor/history">
      <img src="/autonexus/public/assets/img/history.png"/>Vehicle History
    </a>
    <a href="/autonexus/supervisor/complaints">
      <img src="/autonexus/public/assets/img/Complaints.png"/>Complaints
     </a>
      <a href="/autonexus/supervisor/feedbacks" class="nav">
      <img src="/autonexus/public/assets/img/Feedbacks.png"/>Feedbacks
     </a>
      <a href="/autonexus/supervisor/reports">
       <img src="/autonexus/public/assets/img/Inspection.png"/>Report
     </a>

     <a href="<?= rtrim(BASE_URL, '/') ?>/logout"><img src="/autonexus/public/assets/img/user.png" alt="User" class="avatar-img" /> Sign Out</a>
    </div>
    <main class="main-content">
      <header>
        <input type="text" placeholder="Search..." class="search" />
      </header>

<section class="feedback-section">
        <h2>Customer Feedback</h2>

        <!-- Filter Row -->
        <div class="filters">
          <input type="text" placeholder="Search feedback..." class="filter-search">
          <select>
            <option>All Ratings</option>
            <option>5 Stars</option>
            <option>4 Stars</option>
            <option>3 Stars</option>
            <option>2 Stars</option>
            <option>1 Star</option>
          </select>
          <select>
            <option>All</option>
            <option>Replied</option>
            <option>Not Replied</option>
          </select>
          <input type="date">
        </div>

        <!-- Feedback Cards -->
        <div class="feedback-cards">
          <div class="card">
            <h3>John Smith <span class="rating good">5/5 ★★★★★</span></h3>
            <p><strong>Service:</strong> Oil Change</p>
            <p><strong>Date:</strong> Nov 5, 2023</p>
            <p>Great service! The staff was friendly and the work was done quickly and efficiently.</p>
            <span class="reply replied">Replied</span>
          </div>

          <div class="card">
            <h3>Sarah Williams <span class="rating bad">2/5 ★★☆☆☆</span></h3>
            <p><strong>Service:</strong> Brake Inspection</p>
            <p><strong>Date:</strong> Nov 4, 2023</p>
            <p>The service took much longer than expected. I had to wait for over 2 hours.</p>
            <span class="reply not-replied">Not replied yet</span>
          </div>

          <div class="card">
            <h3>Michael Johnson <span class="rating good">4/5 ★★★★☆</span></h3>
            <p><strong>Service:</strong> Tire Rotation</p>
            <p><strong>Date:</strong> Nov 3, 2023</p>
            <p>Good service overall. The mechanic explained everything clearly, but the waiting area needs improvement.</p>
            <span class="reply replied">Replied</span>
          </div>

          <div class="card">
            <h3>Emily Davis <span class="rating bad">1/5 ★☆☆☆☆</span></h3>
            <p><strong>Service:</strong> Full Service</p>
            <p><strong>Date:</strong> Nov 2, 2023</p>
            <p>Very disappointed with the service. My car still has the same issue after repair.</p>
            <span class="reply not-replied">Not replied yet</span>
          </div>

          <div class="card">
            <h3>Robert Brown <span class="rating avg">3/5 ★★★☆☆</span></h3>
            <p><strong>Service:</strong> Engine Diagnostic</p>
            <p><strong>Date:</strong> Nov 1, 2023</p>
            <p>Average service. Accurate diagnostic but the price was higher than quoted.</p>
            <span class="reply not-replied">Not replied yet</span>
          </div>
        </div>
      </section>
    </main>
  </div>

  <script src="/autonexus/public/assets/js/supervisor/script-feedbacks.js"></script>
</body>
</html>
     