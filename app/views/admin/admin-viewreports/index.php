<!-- admin/admin-viewreports -->
<?php $current = 'reports'; // highlights “Service Progress” in sidebar ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>AutoNexus Dashboard</title>

  <!-- in <head> -->
<link rel="stylesheet" href="<?= rtrim(BASE_URL,'/') ?>/app/views/layouts/admin-sidebar/styles.css">
<link rel="stylesheet" href="<?= rtrim(BASE_URL,'/') ?>/public/assets/css/admin/reports/style.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>



  

</head>
<body>
<?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>
  <div class="main">
    
    <div class="reports-container">
      <h2>Reports</h2>
      <div class="filter-bar">
        <select>
          <option>Service Report</option>
          <option>Revenue Report</option>
          <option>Service Distribution</option>
        </select>
        <select>
          <option>Last 6 Months</option>
          <option>Last 12 Months</option>
        </select>
        <button class="grey-btn">Refresh</button>
        <button class="red-btn">Export</button>
      </div>

      <div class="tab-buttons">
        <button class="tab-btn active" data-tab="service">Service Report</button>
        <button class="tab-btn" data-tab="revenue">Revenue Report</button>
        <button class="tab-btn" data-tab="distribution">Service Distribution</button>
      </div>

      <!-- Service Report Tab -->
      <div id="service" class="tab-content active">
  <h4>Service Trends</h4>
  <canvas id="serviceChart" width="700" height="300"></canvas>
        
        <h4>Service Summary</h4>
        <table>
          <thead>
            <tr><th>Service Type</th><th>Total</th><th>Avg. per Month</th><th>Growth</th></tr>
          </thead>
          <tbody>
            <tr><td>Oil Changes</td><td>345</td><td>57.5</td><td class="green">+12%</td></tr>
            <tr><td>Brake Service</td><td>172</td><td>28.7</td><td class="green">+8%</td></tr>
            <tr><td>Tire Service</td><td>188</td><td>31.3</td><td class="red">-2%</td></tr>
            <tr><td>Diagnostics</td><td>128</td><td>21.3</td><td class="green">+15%</td></tr>
          </tbody>
        </table>
      </div>

      <!-- Revenue Report Tab -->
      <div id="revenue" class="tab-content">
  <h4>Revenue Trends</h4>
  <canvas id="revenueChart" width="700" height="300"></canvas>
        
        <h4>Revenue Summary</h4>
        <div class="summary-cards">
          <div class="card">
            <p>Total Revenue</p>
            <h3>$140,000</h3>
            <span class="green">+15% from last period</span>
          </div>
          <div class="card">
            <p>Average Monthly</p>
            <h3>$23,333</h3>
            <span class="green">+8% from last period</span>
          </div>
          <div class="card">
            <p>Projected Annual</p>
            <h3>$280,000</h3>
            <span class="green">+12% from last year</span>
          </div>
        </div>
      </div>

      <!-- Service Distribution Tab -->
      <div id="distribution" class="tab-content">
  <h4>Service Distribution</h4>
  <canvas id="distributionChart" width="400" height="300"></canvas>

        
        <h4>Service Breakdown</h4>
        <table>
          <thead>
            <tr><th>Service Type</th><th>Percentage</th><th>Count</th><th>Revenue</th></tr>
          </thead>
          <tbody>
            <tr><td>Oil Changes</td><td>45%</td><td>345</td><td>$34,500</td></tr>
            <tr><td>Brake Service</td><td>25%</td><td>172</td><td>$43,000</td></tr>
            <tr><td>Tire Service</td><td>20%</td><td>188</td><td>$37,600</td></tr>
            <tr><td>Diagnostics</td><td>10%</td><td>128</td><td>$25,600</td></tr>
          </tbody>
        </table>
      </div>

      <div class="bottom-button">
        <button class="red-btn full">📄 Generate Monthly Report</button>
      </div>
    </div>
  </div>
<!-- at end of <body>, BEFORE closing body -->
<script src="<?= rtrim(BASE_URL,'/') ?>/public/assets/js/admin/reports/script.js"></script>
</body>
</html>
