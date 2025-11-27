<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Auto Shop Dashboard</title>
  <link rel="stylesheet" href="/autonexus/public/assets/css/supervisor/style-report.css"/>
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
      <a href="/autonexus/supervisor/feedbacks">
      <img src="/autonexus/public/assets/img/Feedbacks.png"/>Feedbacks
     </a>
      <a href="/autonexus/supervisor/reports" class="nav">
       <img src="/autonexus/public/assets/img/Inspection.png"/>Report
     </a>
    </div>
    <main class="main-content">
      <header>
        <input type="text" placeholder="Search..." class="search" />
      </header>

  <!-- Job Inspection & Reporting -->
  <div class="card">
    <div class="card-header">
      <h2>Job Inspection & Reporting</h2>
      <span class="status pending">Pending Inspection</span>
    </div>
    <div class="card-content">
      <div class="info-grid">
        <div>
          <p class="label">Job ID</p>
          <p class="value">JOB10456</p>
        </div>
        <div>
          <p class="label">Vehicle Number</p>
          <p class="value">ABC-1234</p>
        </div>
        <div>
          <p class="label">Customer</p>
          <p class="value">Nuwan Perera</p>
        </div>
        <div>
          <p class="label">Assigned Mechanic</p>
          <p class="value">John Doe</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Service Summary -->
  <div class="card">
    <div class="card-header">
      <h2>Service Summary</h2>
      <a href="#" class="job-log-link">View full job log</a>
    </div>
    <div class="card-content">
      <table>
        <thead>
          <tr>
            <th>Service Task</th>
            <th>Status</th>
            <th>Notes</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Oil Change</td>
            <td class="status completed">Completed</td>
            <td>Used Mobil 10W-30</td>
          </tr>
          <tr>
            <td>Tire Rotation</td>
            <td class="status completed">Completed</td>
            <td>Front-left tire uneven wear</td>
          </tr>
          <tr>
            <td>Brake Inspection</td>
            <td class="status completed">Completed</td>
            <td>No issues found</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

<!-- Final Inspection Form -->
  <div class="form-section">
    <h2>Final Inspection Form</h2>
    <label>Inspection Notes</label>
    <textarea placeholder="Enter your observations and conclusions..."></textarea>

    <label>Work Quality Rating</label>
    <div class="stars" id="rating-stars">
      <span class="star" data-rating="1">&#9733;</span>
      <span class="star" data-rating="2">&#9733;</span>
      <span class="star" data-rating="3">&#9733;</span>
      <span class="star" data-rating="4">&#9733;</span>
      <span class="star" data-rating="5">&#9733;</span>
    </div>

    <div class="checklist">
      <strong>Checklist Confirmation</strong>
      <label><input type="checkbox"> All service tasks verified</label>
      <label><input type="checkbox"> Vehicle test driven</label>
      <label><input type="checkbox"> Customer concerns addressed</label>
    </div>
  </div>

  <!-- Attach Work Photo -->
  <div class="form-section">
    <h2>Attach Work Photo</h2>
    <div class="upload-box">
      <label class="upload-label">
        <input type="file" accept=".png,.jpg,.jpeg,.gif" onchange="handleFileUpload(event)">
        Upload images or drag and drop
      </label>
      <p>PNG, JPG, GIF up to 10MB each</p>
      <div id="file-list"></div>
    </div>
  </div>

<!-- Final Report -->
  <div class="form-section">
    <h2>Final Report</h2>
    <label>Report Summary</label>
    <textarea placeholder="Provide a brief summary of the entire job and your final notes..."></textarea>

    <div class="next-service">
      <label>Next Service Recommendation</label>
      <input type="text" placeholder="e.g., Next oil change due in 5,000 km or 3 months">
    </div>
  </div>

  <!-- Action Buttons -->
  <div class="actions">
    <button class="btn secondary" onclick="saveDraft()">Save as Draft</button>
    <button class="btn primary" onclick="submitReport()">Submit Final Report</button>
  </div>  
    </main>

  <script src="/autonexus/public/assets/js/supervisor/script-report.js"></script>
</body>
</html>
     