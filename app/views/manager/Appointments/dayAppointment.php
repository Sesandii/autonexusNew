<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Appointments - AutoNexus</title>

  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/sidebar.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/dayAppointment.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>

 <?php include APP_ROOT . '/views/layouts/manager-sidebar.php'; ?>

<div class="main">

    <!-- Page Title -->
    <h4>Appointments</h4>

    <!-- TABLE SECTION WRAPPER (Required for your CSS) -->
    <div class="table-section">

        <table>
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Customer</th>
                    <th>Vehicle</th>
                    <th>Service</th>
                    <th>Assigned to</th>
                    <th>Status</th>
                    <th>Notes</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($appointments as $app) : ?>
                    <tr>
                        <td><?= htmlspecialchars($app['appointment_time']) ?></td>
                        <td><?= htmlspecialchars($app['first_name'] . ' ' . $app['last_name']) ?></td>

                        <td>
                            <?= htmlspecialchars($app['make'] . ' ' . $app['model']) ?>
                            (<?= htmlspecialchars($app['license_plate']) ?>)
                        </td>

                        <td><?= htmlspecialchars($app['service_name']) ?></td>
                        <td><?= htmlspecialchars($app['assigned_person'] ?? 'Not Assigned') ?></td>
                        <td>
                        <span class="status <?= strtolower(str_replace(' ', '-', $app['status'])) ?>">
                            <?= htmlspecialchars($app['status']) ?>
                        </span>
                        </td>

                        <td><?= htmlspecialchars($app['notes'] ?? '-') ?></td>

                        <td>
    <a href="<?= BASE_URL ?>/manager/appointments/edit/<?= $app['appointment_id'] ?>" 
       class="update-btn">
        Update
    </a>
</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>

        </table>

    </div> <!-- .table-section -->

</div> <!-- .main -->

<script src="<?= BASE_URL ?>/public/assets/js/manager/dayAppointment.js"></script>

</body>
</html>
