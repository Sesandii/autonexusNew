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
    
<h4 id="appointments-header">Appointments</h4>
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
                    <th>Assigned to</th>
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
<td>
<?php if (empty($app['assigned_to'])): ?>
    <form method="POST" action="<?= BASE_URL ?>/receptionist/appointments/assignSupervisor">
        <!-- Send appointment ID -->
        <input type="hidden" name="appointment_id" value="<?= $app['appointment_id'] ?>">
        <!-- Keep the current date so we can redirect back to the same day -->
        <input type="hidden" name="date" value="<?= htmlspecialchars($date) ?>">

        <!-- Supervisor dropdown -->
        <select name="supervisor_id" required>
            <option value="">-- Select Supervisor --</option>
            <?php foreach ($app['supervisors'] as $sup): ?>
                <option value="<?= $sup['supervisor_id'] ?>">
                    <?= htmlspecialchars($sup['first_name'] . ' ' . $sup['last_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button class="assign-btn">Assign</button>
    </form>
<?php else: ?>
    <?php 
        // Show the assigned supervisor's name
        // If the assigned supervisor is not in the available list (maybe fully booked), just show "Assigned"
        $assigned = array_filter($app['supervisors'], fn($s) => $s['supervisor_id'] == $app['assigned_to']);
        if ($assigned) {
            $assignedName = reset($assigned)['first_name'] . ' ' . reset($assigned)['last_name'];
        } else {
            // If not found in supervisors list, fetch from DB or fallback
            $assignedName = "Assigned";
        }
    ?>
    <?= htmlspecialchars($assignedName) ?>
<?php endif; ?>
</td>
                        <td>
                            <button class="update-btn">
                                <a href="<?= BASE_URL ?>/receptionist/appointments/edit/<?= $app['appointment_id'] ?>" class="update-btn">
    Update
</a>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>

        </table>

    </div> <!-- .table-section -->

</div> <!-- .main -->

</body>
</html>
