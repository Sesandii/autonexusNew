<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Customer Complaints - AutoNexus</title>
  <link rel="stylesheet" href="/autonexus/public/assets/css/supervisor/style-complaints.css"/>

  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f8f9fa;
      color: #333;
      margin: 0;
      display: flex;
    }

    .sidebar {
      width: 240px;
      background: #000000ff;
      box-shadow: 2px 0 6px rgba(0,0,0,0.1);
      padding: 15px;
    }

    main {
      flex: 1;
      display: flex;
      flex-direction: column;
      align-items: center; /* centers horizontally */
      justify-content: center; /* centers vertically */
      min-height: 100vh;
      padding: 40px;
    }

    h2 {
      color: #b71c1c;
      margin-bottom: 25px;
      text-align: center;
    }

    table {
      width: 85%;
      border-collapse: collapse;
      background: #fff;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      border-radius: 10px;
      overflow: hidden;
    }

    th, td {
      padding: 14px 18px;
      border-bottom: 1px solid #eee;
      text-align: left;
    }

    th {
      background: #f4f4f4;
      font-weight: bold;
    }

    tr:hover {
      background-color: #fafafa;
    }

    .status {
      font-weight: bold;
      padding: 5px 10px;
      border-radius: 5px;
      text-transform: capitalize;
    }
    .status.Open {
      background: #ffebee;
      color: #b71c1c;
    }
    .status.InProgress {
      background: #fff3e0;
      color: #e65100;
    }
    .status.Resolved {
      background: #e8f5e9;
      color: #1b5e20;
    }
  </style>
</head>
<body>

<div class="sidebar">
  <div class="logo-container">
    <img src="/autonexus/public/assets/img/Auto.png" alt="Logo" class="logo" style="width:100%; margin-bottom:10px;">
  </div>
  <h2>AUTONEXUS</h2>
  <a href="/autonexus/supervisor/dashboard"><img src="/autonexus/public/assets/img/dashboard.png"/>Dashboard</a>
  <a href="/autonexus/supervisor/workorders"><img src="/autonexus/public/assets/img/jobs.png"/>Work Orders</a>
  <a href="/autonexus/supervisor/assignedjobs"><img src="/autonexus/public/assets/img/assigned.png"/>Assigned</a>
  <a href="/autonexus/supervisor/history"><img src="/autonexus/public/assets/img/history.png"/>Vehicle History</a>
  <a href="/autonexus/supervisor/complaints" class="nav"><img src="/autonexus/public/assets/img/Complaints.png"/>Complaints</a>
  <a href="/autonexus/supervisor/feedbacks"><img src="/autonexus/public/assets/img/Feedbacks.png"/>Feedbacks</a>
  <a href="/autonexus/supervisor/reports"><img src="/autonexus/public/assets/img/Inspection.png"/>Report</a>
  <a href="<?= rtrim(BASE_URL, '/') ?>/logout">
    <img src="/autonexus/public/assets/img/user.png" alt="User" class="avatar-img" /> Sign Out
  </a>
</div>

<main>
  <h2>Customer Complaints</h2>

  <?php
    $complaints = [
      [
        "title" => "Engine noise after service",
        "desc" => "Customer reports unusual engine noise after the 30,000 mile service was completed yesterday.",
        "customer" => "James Wilson",
        "vehicle" => "2019 Honda Civic",
        "date" => "Jul 28, 2025",
        "assigned" => "Mike Johnson",
        "status" => "Open"
      ],
      [
        "title" => "AC not cooling properly",
        "desc" => "Customer complains that the air conditioning is not cooling effectively after the recent AC service.",
        "customer" => "Tom Hawk",
        "vehicle" => "2018 Ford F-150",
        "date" => "Jul 26, 2025",
        "assigned" => "Mike Johnson",
        "status" => "In Progress"
      ],
      [
        "title" => "Brake squeaking",
        "desc" => "Customer reports loud squeaking noise when braking. Brakes were replaced at our shop 2 weeks ago.",
        "customer" => "Lisa Chen",
        "vehicle" => "2020 Toyota Premio",
        "date" => "Jul 25, 2025",
        "assigned" => "Mike Johnson",
        "status" => "Resolved"
      ],
      [
        "title" => "Tire pressure warning",
        "desc" => "Customer reports tire pressure warning light comes on intermittently since last service.",
        "customer" => "Lisa Chen",
        "vehicle" => "2022 Nissan Leaf",
        "date" => "Jul 25, 2025",
        "assigned" => "Mike Johnson",
        "status" => "Resolved"
      ]
    ];
  ?>

  <table>
    <thead>
      <tr>
        <th>Title</th>
        <th>Description</th>
        <th>Customer</th>
        <th>Vehicle</th>
        <th>Date</th>
        <th>Assigned To</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($complaints as $c): ?>
        <tr>
          <td><?= htmlspecialchars($c['title']) ?></td>
          <td><?= htmlspecialchars($c['desc']) ?></td>
          <td><?= htmlspecialchars($c['customer']) ?></td>
          <td><?= htmlspecialchars($c['vehicle']) ?></td>
          <td><?= htmlspecialchars($c['date']) ?></td>
          <td><?= htmlspecialchars($c['assigned']) ?></td>
          <td><span class="status <?= str_replace(' ', '', $c['status']) ?>"><?= htmlspecialchars($c['status']) ?></span></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</main>

</body>
</html>
