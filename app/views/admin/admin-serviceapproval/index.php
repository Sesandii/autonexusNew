<?php $current = 'approval'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - Service Approval Queue</title>

  <!-- Shared neutral styles -->
  <link rel="stylesheet" href="../admin-shared/management.css">
  <link rel="stylesheet" href="../admin-sidebar/styles.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

  
  <style>
    .sidebar { position: fixed; top: 0; left: 0; width: 260px; height: 100vh; overflow-y: auto; }
    .main-content { margin-left: 260px; padding: 30px; background: #fff; min-height: 100vh; }
  </style>
</head>
<body>
  <?php include("../admin-sidebar/sidebar.php"); ?>

  <main class="main-content">

  <div class="management-header">
  <h2>Service Approval Queue</h2>

  <div class="tools">
  
    <select class="status-filter">
          <option value="all">All Branches</option>
          
        </select>

     <select class="status-filter">
          <option value="all">All Service Types</option>
          
        </select>   

          <!-- Date Range Picker -->
        <div class="date-range">
  <label class="date-label">
    From
    <input type="date" id="dateFrom" class="date-input">
  </label>
  <span class="dash">–</span>
  <label class="date-label">
    To
    <input type="date" id="dateTo" class="date-input">
  </label>
  <button type="button" id="applyDateRange" class="apply-btn">Apply</button>
</div>

   
  </div>
</div>

      <table>
        <thead>
          <tr>
           
            <th>Service Name</th>
            <th>Service Type</th>
            <th>Branch</th>
             <th>Submitted By</th>
             <th>Date</th>
             <th>Status</th>
            <th>Actions</th>            
          </tr>
        </thead>
        <tbody>
          
    <tr>
    <td>Oil Change</td>
    <td>Maintenance</td>
    <td>Colombo Main Branch</td>
    <td>John Perera</td>
    <td>2025-08-15</td>
    <td class="status--pending">Pending</td>
    <td>
        <!-- Approve -->
<button class="tick-btn" title="Approve">
  <i class="fa-solid fa-check"></i>
</button>

<!-- Reject -->
<button class="cross-btn" title="Reject">
  <i class="fa-solid fa-xmark"></i>
</button>


    </td>
</tr>
<tr>
    <td>Engine Tune-up</td>
    <td>Maintenance</td>
    <td>Kandy Branch</td>
    <td>Sara Fernando</td>
    <td>2025-08-18</td>
    <td class="status--pending">Pending</td>
    <td>
       <!-- Approve -->
<button class="tick-btn" title="Approve">
  <i class="fa-solid fa-check"></i>
</button>

<!-- Reject -->
<button class="cross-btn" title="Reject">
  <i class="fa-solid fa-xmark"></i>
</button>


    </td>
</tr>
<tr>
    <td>Brake Inspection</td>
    <td>Inspection</td>
    <td>Galle Branch</td>
    <td>Ruwan Silva</td>
    <td>2025-08-20</td>
    <td class="status--pending">Pending</td>
    <td>
       <!-- Approve -->
<button class="tick-btn" title="Approve">
  <i class="fa-solid fa-check"></i>
</button>

<!-- Reject -->
<button class="cross-btn" title="Reject">
  <i class="fa-solid fa-xmark"></i>
</button>

    </td>
</tr>
<tr>
    <td>Tire Replacement</td>
    <td>Replacement</td>
    <td>Negombo Branch</td>
    <td>Chathuri Jayasinghe</td>
    <td>2025-08-22</td>
    <td class="status--pending">Pending</td>
    <td>
       <!-- Approve -->
<button class="tick-btn" title="Approve">
  <i class="fa-solid fa-check"></i>
</button>

<!-- Reject -->
<button class="cross-btn" title="Reject">
  <i class="fa-solid fa-xmark"></i>
</button>


    </td>
</tr>
<tr>
    <td>Battery Check</td>
    <td>Repair</td>
    <td>Matara Branch</td>
    <td>Isuru Perera</td>
    <td>2025-08-25</td>
    <td class="status--pending">Pending</td>
    <td>
      <!-- Approve -->
<button class="tick-btn" title="Approve">
  <i class="fa-solid fa-check"></i>
</button>

<!-- Reject -->
<button class="cross-btn" title="Reject">
  <i class="fa-solid fa-xmark"></i>
</button>


    </td>
</tr>


          <!-- more rows -->
        </tbody>
      </table>
    </div>
  </main>
</body>
</html>
