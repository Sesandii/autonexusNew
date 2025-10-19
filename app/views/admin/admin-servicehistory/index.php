<?php $current = 'history'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service History</title>
   <!-- Shared neutral styles -->

 <link rel="stylesheet" href="<?= rtrim(BASE_URL,'/') ?>/app/views/layouts/admin-shared/management.css">
  <!-- Sidebar styles -->
 <link rel="stylesheet" href="<?= rtrim(BASE_URL,'/') ?>/app/views/layouts/admin-sidebar/styles.css">
  <!-- Icons (optional) -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

  <style>
    .sidebar { position: fixed; top:0; left:0; width:260px; height:100vh; overflow-y:auto; }
    .main-content { margin-left:260px; padding:30px; background:#fff; min-height:100vh; }
    </style>
</head>
<body>

<?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

    <div class="main-content">
        <div class="management">
            <div class="management-header">
                <h2>Service History</h2>
                <div class="tools">
                    <input type="text" class="search-input" placeholder="Search by service ID/ branch/ customer">

                    <div class="date-range">
                        <label class="date-label">
                            <span>From:</span>
                            <input type="date" class="date-input">
                        </label>
                        <span class="dash">to</span>
                        <label class="date-label">
                            <span>To:</span>
                            <input type="date" class="date-input">
                        </label>
                    </div>

                    <select class="status-filter">
                        <option value="">Service Type</option>
                        <option value="repair">Repair</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="replacement">Replacement</option>
                        <option value="inspection">Inspection</option>
                    </select>

                   
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Service ID</th>
                        <th>Service Type</th>
                        <th>Service Date</th>
                        <th>Branch Name</th>
                        <th>Customer Name</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>SRV-001</td>
                        <td class="status--repair">Repair</td>
                        <td>Oct 15, 2023</td>
                        <td>Downtown Branch</td>
                        <td>John Smith</td>
                    </tr>
                    <tr>
                        <td>SRV-002</td>
                        <td class="status--maintenance">Maintenance</td>
                        <td>Oct 18, 2023</td>
                        <td>Westside Branch</td>
                        <td>Emily Davis</td>
                    </tr>
                    <tr>
                        <td>SRV-003</td>
                        <td class="status--replacement">Replacement</td>
                        <td>Oct 20, 2023</td>
                        <td>Eastside Branch</td>
                        <td>Michael Wilson</td>
                    </tr>
                    <tr>
                        <td>SRV-004</td>
                        <td class="status--inspection">Inspection</td>
                        <td>Oct 22, 2023</td>
                        <td>Downtown Branch</td>
                        <td>Jessica Thompson</td>
                    </tr>
                    <!-- Additional rows as necessary -->
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
