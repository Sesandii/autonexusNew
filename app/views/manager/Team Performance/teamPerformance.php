<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Team Performance</title>
   <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/sidebar.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/teamPerformance.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

  <?php include APP_ROOT . '/views/layouts/manager-sidebar.php'; ?>

  <div class="main">

    <h1>Team Performance</h1>
      <table class="performance-table">
        <thead>
          <tr>
            <th></th>
            <th>Team Member</th>
            <th>Role</th>
            <th>Completed Jobs</th>
            <th>Avg. Service Time</th>
            <th>Return Rate</th>
            <th>Revenue</th>
          </tr>
        </thead>
        <tbody id="table-body">
          <!-- JS will inject rows -->
        </tbody>
      </table>
    </main>
  </div>
<script src="<?= BASE_URL ?>/public/assets/js/manager/teamPerformance.js"></script>

</body>
</html>