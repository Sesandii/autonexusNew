<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Appointments - AutoNexus</title>

  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/sidebar.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/dayAppointment.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>

<?php include APP_ROOT . '/views/layouts/receptionist-sidebar.php'; ?>

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
                    <th>Branch</th>
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
                        <td><?= htmlspecialchars($app['branch_name']) ?></td>

                        <td>
                        <span class="status <?= strtolower(str_replace(' ', '-', $app['status'])) ?>">
                            <?= htmlspecialchars($app['status']) ?>
                        </span>
                        </td>

                        <td><?= htmlspecialchars($app['notes'] ?? '-') ?></td>

                        <td>
                            <button class="update-btn"
                                data-id="<?= $app['appointment_id'] ?>"
                                data-time="<?= $app['appointment_time'] ?>"
                                data-customer="<?= htmlspecialchars($app['first_name'] . ' ' . $app['last_name']) ?>"
                                data-vehicle="<?= htmlspecialchars($app['make'] . ' ' . $app['model'] . ' (' . $app['license_plate'] . ')') ?>"
                                data-service="<?= htmlspecialchars($app['service_name']) ?>"
                                data-status="<?= htmlspecialchars($app['status']) ?>">
                                Update
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>

        </table>

    </div> <!-- .table-section -->

</div> <!-- .main -->

<script src="<?= BASE_URL ?>/public/assets/js/receptionist/dayAppointment.js"></script>

</body>
</html>
