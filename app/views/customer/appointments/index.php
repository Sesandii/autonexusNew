<?php
$base  = rtrim(BASE_URL, '/');
$items = $appointments ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($title ?? 'Appointments') ?> - AutoNexus</title>

  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/appointments.css">
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/sidebar.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
 
  

<link rel="stylesheet" href="<?= rtrim(BASE_URL,'/') ?>/public/assets/css/normalize-ui.css">

</head>
<body>

  <?php include APP_ROOT . '/views/layouts/customer-sidebar.php'; ?>

  <main class="main-content">
    <h1>Your Appointments</h1>
    <p class="subtitle">Here’s a summary of all your upcoming and past service bookings.</p>

    <section class="appointments-grid">
      <?php if (empty($items)): ?>
        <p style="color:#6B7280">No appointments yet.</p>
      <?php else: ?>
        <?php foreach ($items as $a): ?>
          <div class="appointment-card <?= htmlspecialchars($a['status_class']) ?>">
            <div class="card-header">
              <h3>
                <?php
                  $icon = 'fa-screwdriver-wrench';
                  if ($a['status_class'] === 'completed') $icon = 'fa-car-side';
                  if ($a['status_class'] === 'cancelled') $icon = 'fa-ban';
                ?>
                <i class="fa-solid <?= $icon ?>"></i>
                <?= htmlspecialchars($a['service']) ?>
              </h3>
              <span class="status"><?= htmlspecialchars($a['status']) ?></span>
            </div>
            <div class="card-body">
              <p><strong>Date:</strong> <?= htmlspecialchars($a['date']) ?></p>
              <p><strong>Time:</strong> <?= htmlspecialchars($a['time']) ?></p>
              <p><strong>Branch:</strong> <?= htmlspecialchars($a['branch']) ?></p>
              <?php if (!empty($a['est_completion'])): ?>
                <p><strong>Est. Completion:</strong> <?= htmlspecialchars($a['est_completion']) ?></p>
              <?php endif; ?>
            </div>
            <div class="card-footer">
              <?php if ($a['status_class'] === 'upcoming'): ?>
                <form method="post" action="<?= $base ?>/customer/appointments/cancel" onsubmit="return confirm('Cancel this appointment?')">
                  <input type="hidden" name="appointment_id" value="<?= (int)$a['appointment_id'] ?>">
                  <button class="cancel" type="submit">Cancel</button>
                </form>
                <a class="reschedule" href="<?= $base ?>/customer/booking?reschedule=<?= (int)$a['appointment_id'] ?>">View</a>
              <?php elseif ($a['status_class'] === 'completed'): ?>
                <a class="review" href="<?= $base ?>/customer/rate-service?appointment=<?= (int)$a['appointment_id'] ?>">Leave Review</a>
              <?php else: ?>
                <a class="rebook" href="<?= $base ?>/customer/booking?rebook=<?= (int)$a['appointment_id'] ?>">Rebook</a>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </section>
  </main>

  <!-- Optional: small script if you later want AJAX cancel -->
  <!-- <script src="<?= $base ?>/assets/js/customer/appointments.js"></script> -->

</body>
</html>
