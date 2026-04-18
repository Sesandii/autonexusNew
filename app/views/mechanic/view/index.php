<?php $base = rtrim(BASE_URL, '/'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Service Dashboard</title>
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/mechanic/style-view.css">
</head>
<body>
  <div class="sidebar">
    <div class="logo-container">
    <img src="/autonexus/public/assets/img/Auto.png" alt="Logo" class="logo">
    </div>
    <h2>AUTONEXUS</h2>
    <a href="/autonexus/mechanic/dashboard">
      <img src="/autonexus/public/assets/img/dashboard.png"/>Dashboard
    </a>
    <a href="/autonexus/mechanic/jobs" class="active">
      <img src="/autonexus/public/assets/img/jobs.png"/>Jobs
    </a>
    <a href="/autonexus/mechanic/assignedjobs">
      <img src="/autonexus/public/assets/img/assigned.png"/>Assigned Jobs
    </a>
    <a href="/autonexus/mechanic/history">
      <img src="/autonexus/public/assets/img/history.png"/>Vehicle History
    </a>
  </div>

  <div class="main">
  <header>
        <input type="text" placeholder="Search..." class="search" />
</header>
    <div class="section-title job-header">Job Details - JOB-2023-1234</div>
  <div class="card">
  <div class="section-title">
    <img src="images/jobinfo.png" class="section-icon" alt="Job Info Icon" />
    Job Information
  </div>

  <div class="info-row">
    <span class="label">Job Title:</span>
    <span class="job-title">Regular Maintenance</span>
  </div>

  <div class="info-row">
    <span class="label">Status:</span>
    <span><span class="status">Completed</span></span>
  </div>

  <div class="info-row">
    <span class="label">Date:</span>
    <span class="job-date">2023-11-15</span>
  </div>

  <div class="info-row">
    <span class="label">Technician:</span>
    <span>Mike Johnson</span>
  </div>

  <div class="info-row">
    <span class="label">Notes:</span>
    <span>Customer reported unusual noise when braking. Fixed and tested.</span>
  </div>
</div>

<div class="card">
  <div class="section-title">
    <img src="images/car.png" class="section-icon" alt="Vehicle Icon" />
    Vehicle Information
  </div>

  <div class="info-row">
    <span class="label">Make:</span>
    <span class="vehicle-make">Toyota</span>
  </div>

  <div class="info-row">
    <span class="label">Model:</span>
    <span class="vehicle-model">Corolla</span>
  </div>

  <div class="info-row">
    <span class="label">Year:</span>
    <span class="vehicle-year">2019</span>
  </div>

  <div class="info-row">
    <span class="label">License:</span>
    <span class="vehicle-reg">ABC-1234</span>
  </div>
<div class="info-row">
    <span class="label">Mileage:</span>
    <span class="vehicle-mileage">45,000 km</span>
  </div>

  <div class="info-row">
    <span class="label">Color:</span>
    <span class="vehicle-color">Silver</span>
  </div>

  <div class="info-row">
    <span class="label">VIN:</span>
    <span class="vehicle-vin">1HGCM82633A123456</span>
  </div>
</div>


   <div class="card checklist-card">
  <div class="section-title">
    <img src="images/service.png" class="section-icon" alt="Checklist Icon" />
    Service Checklist
  </div>

  <table class="checklist-table">
    <thead>
      <tr>
        <th>Service Item</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Oil Change</td>
        <td><span class="status-icon"></span><span class="status-text">Completed</span></td>
      </tr>
      <tr>
        <td>Brake Inspection</td>
        <td><span class="status-icon"></span><span class="status-text">Completed</span></td>
      </tr>
      <tr>
        <td>Tire Rotation</td>
        <td><span class="status-icon"></span><span class="status-text">Completed</span></td>
      </tr>
      <tr>
        <td>Air Filter Replacement</td>
        <td><span class="status-icon"></span><span class="status-text">Completed</span></td>
      </tr>
      <tr>
        <td>Fluid Levels Check</td>
        <td><span class="status-icon"></span><span class="status-text">Completed</span></td>
      </tr>
      <tr>
        <td>Battery Test</td>
        <td><span class="status-icon"></span><span class="status-text">Completed</span></td>
      </tr>
    </tbody>
  </table>

    <div class="card">
      <div class="section-title">📸 Service Photos</div>
      <div class="photos">
        <img src="https://source.unsplash.com/400x300/?car,service" alt="Service Photo 1">
        <img src="https://source.unsplash.com/400x300/?car,back" alt="Service Photo 2">
        <img src="https://source.unsplash.com/400x300/?engine,belt" alt="Service Photo 3">
        <img src="https://source.unsplash.com/400x300/?garage,car" alt="Service Photo 4">
      </div>
    </div>
  </div>
</body>
</html>
