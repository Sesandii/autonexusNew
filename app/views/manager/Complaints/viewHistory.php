<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Vehicle History</title>

  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/sidebar.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/viewHistory.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>

<body>

<?php include APP_ROOT . '/views/layouts/manager-sidebar.php'; ?>

<div class="main">

    <h2>Service History</h2>

    <!-- ========================= -->
    <!-- 1️⃣ APPOINTMENTS SECTION -->
    <!-- ========================= -->
    <h3>Appointments & Work Orders</h3>

    <?php if (!empty($customer['appointments'])): ?>
        <div class="appointment-grid">

        <?php foreach ($customer['appointments'] as $appt): ?>
            <div class="appointment-card">

                <h4>
                    Appointment #<?= htmlspecialchars($appt['appointment_id']) ?>
                    - <?= date('F j, Y', strtotime($appt['appointment_date'])) ?>
                </h4>

                <span class="badge-status <?= htmlspecialchars(strtolower($appt['status'])) ?>">
                    <?= htmlspecialchars($appt['status']) ?>
                </span>

                <p><b>Vehicle:</b>
                    <?= htmlspecialchars($appt['year'] . ' ' . $appt['make'] . ' ' . $appt['model']) ?>
                </p>

                <p><b>Service:</b>
                    <?= htmlspecialchars($appt['service_name']) ?>
                </p>

                <!-- Work Orders -->
                <?php if (!empty($appt['work_orders'])): ?>
                    <h5>Work Orders:</h5>
                    <ul>
                        <?php foreach ($appt['work_orders'] as $wo): ?>
                            <li>
                                <b>Mechanic:</b>
                                <?= htmlspecialchars($wo['mechanic_first'] . ' ' . $wo['mechanic_last']) ?><br>

                                <b>Supervisor:</b>
                                <?= htmlspecialchars($wo['supervisor_first'] . ' ' . $wo['supervisor_last']) ?><br>

                                <b>Summary:</b>
                                <?= htmlspecialchars($wo['service_summary'] ?? 'N/A') ?><br>

                                <b>Cost:</b>
                                $<?= htmlspecialchars($wo['total_cost']) ?><br>

                                <b>Status:</b>
                                <?= htmlspecialchars($wo['status']) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

            </div>
        <?php endforeach; ?>

        </div>
    <?php else: ?>
        <p>No appointments found.</p>
    <?php endif; ?>


    <!-- ========================= -->
    <!-- 2️⃣ COMPLAINT HISTORY -->
    <!-- ========================= -->
    <h3 style="margin-top: 40px;">Complaint History</h3>

    <?php
    // 🔹 Collect all complaints from appointments
    $allComplaints = [];

    if (!empty($customer['appointments'])) {
        foreach ($customer['appointments'] as $appt) {
            if (!empty($appt['complaints'])) {
                foreach ($appt['complaints'] as $c) {
                    $c['appointment_date'] = $appt['appointment_date']; // attach date
                    $allComplaints[] = $c;
                }
            }
        }
    }
    ?>
<?php if (!empty($customer['complaints'])): ?>
    <div class="appointment-grid">

    <?php foreach ($customer['complaints'] as $c): ?>
        <div class="appointment-card">

            <h4><?= htmlspecialchars($c['subject']) ?></h4>

            <span class="badge-status <?= strtolower($c['status']) ?>">
                <?= htmlspecialchars($c['status']) ?>
            </span>

            <p><b>Priority:</b> <?= htmlspecialchars($c['priority']) ?></p>
            <p><?= htmlspecialchars($c['description']) ?></p>

        </div>
    <?php endforeach; ?>

    </div>
<?php else: ?>
    <p>No complaints found.</p>
<?php endif; ?>

</div>

</body>
</html>