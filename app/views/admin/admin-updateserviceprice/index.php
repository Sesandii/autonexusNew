<?php $current = 'pricing'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - Service Pricing Management</title>

  <!-- Shared neutral styles -->
 <link rel="stylesheet" href="<?= rtrim(BASE_URL,'/') ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="<?= rtrim(BASE_URL,'/') ?>/app/views/layouts/admin-sidebar/styles.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

  
  <style>
    .sidebar { position: fixed; top: 0; left: 0; width: 260px; height: 100vh; overflow-y: auto; }
    .main-content { margin-left: 260px; padding: 30px; background: #fff; min-height: 100vh; }
  </style>
</head>
<body>
  <?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

  <main class="main-content">

  <div class="management-header">
  <h2>Service Pricing Management</h2>

  <div class="tools">
  
  <input type="text" class="search-input" id="searchInput" placeholder="Search by service id/name..." />

     <select class="status-filter">
          <option value="all">All Service Types</option>
          
        </select>   

   
  </div>
</div>

      <table>
        <thead>
          <tr>
           
           <th>Service ID</th>
            <th>Service Name</th>
            <th>Service Type</th>
            <th>Current Price</th>
             <th>Update Price</th>
             <th>Actions</th>
                         
          </tr>
        </thead>
        <tbody>
          
   <tr>
    <td>SVC001</td>
    <td>Oil Change</td>
    <td>Maintenance</td>
    <td>2500 LKR</td>
    <td><input type="text" placeholder="Enter new price"></td>
    <td>
        <button class="btn-save" title="Save"><i class="fa-solid fa-floppy-disk"></i> Save</button>
    </td>
</tr>
<tr>
    <td>SVC002</td>
    <td>Engine Tune-Up</td>
    <td>Repair</td>
    <td>7500 LKR</td>
    <td><input type="text" placeholder="Enter new price"></td>
    <td>
        <button class="btn-save" title="Save"><i class="fa-solid fa-floppy-disk"></i> Save</button>
    </td>
</tr>
<tr>
    <td>SVC003</td>
    <td>Brake Pad Replacement</td>
    <td>Repair</td>
    <td>6800 LKR</td>
    <td><input type="text" placeholder="Enter new price"></td>
    <td>
        <button class="btn-save" title="Save"><i class="fa-solid fa-floppy-disk"></i> Save</button>
    </td>
</tr>
<tr>
    <td>SVC004</td>
    <td>Car Wash</td>
    <td>Cleaning</td>
    <td>1500 LKR</td>
    <td><input type="text" placeholder="Enter new price"></td>
    <td>
        <button class="btn-save" title="Save"><i class="fa-solid fa-floppy-disk"></i> Save</button>
    </td>
</tr>
<tr>
    <td>SVC005</td>
    <td>Wheel Alignment</td>
    <td>Maintenance</td>
    <td>4000 LKR</td>
    <td><input type="text" placeholder="Enter new price"></td>
    <td>
        <button class="btn-save" title="Save"><i class="fa-solid fa-floppy-disk"></i> Save</button>
    </td>
</tr>


          <!-- more rows -->
        </tbody>
      </table>
    </div>
  </main>
</body>
</html>
