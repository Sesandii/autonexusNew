<?php
/** @var array $appointment */
/** @var array $branches */
/** @var array $services */
/** @var string $pageTitle */
/** @var string $current */

$current = $current ?? 'appointments';
$B = rtrim(BASE_URL, '/');
$a = $appointment;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle ?? 'Edit Appointment') ?></title>
  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="<?= $B ?>/public/assets/css/admin/appointments/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

<main class="main-content appointments-main">
  <header class="page-header">
    <div class="page-breadcrumb">
      <a href="<?= $B ?>/admin/admin-appointments">Appointments</a>
      <span>›</span>
      <span>Edit #<?= htmlspecialchars((string)$a['appointment_id']) ?></span>
    </div>

    <div class="page-header-main">
      <div class="page-title-wrap">
        <div class="page-icon"><i class="fa-regular fa-pen-to-square"></i></div>
        <div>
          <h2>Edit Appointment</h2>
          <p>Adjust branch, service, date, or status for this booking.</p>
        </div>
      </div>
      <span class="page-chip">ID: <?= (int)$a['appointment_id'] ?></span>
    </div>
  </header>

  <section class="appt-form-wrap">
    <form class="appt-form-card" method="post" action="<?= $B ?>/admin/appointments/update">
      <input type="hidden" name="appointment_id" value="<?= (int)$a['appointment_id'] ?>"/>

      <div class="appt-form-row">
        <label>Customer</label>
        <input type="text" value="<?= htmlspecialchars($a['customer_name'] ?? '') ?>" disabled>
      </div>

      <div class="appt-form-row">
        <label for="branch_id">Branch</label>
        <select name="branch_id" id="branch_id" required>
          <?php foreach ($branches as $b): ?>
            <option value="<?= (int)$b['branch_id'] ?>"
              <?= ((int)$b['branch_id'] === (int)$a['branch_id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($b['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="appt-form-row">
        <label for="service_id">Service</label>
        <select name="service_id" id="service_id" required>
          <?php foreach ($services as $s): ?>
            <option value="<?= (int)$s['service_id'] ?>"
              <?= ((int)$s['service_id'] === (int)$a['service_id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($s['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="appt-form-row appt-form-row--inline">
        <div>
          <label for="appointment_date">Date</label>
          <input type="date" name="appointment_date" id="appointment_date"
                 value="<?= htmlspecialchars($a['appointment_date']) ?>" required>
        </div>
        <div>
          <label for="appointment_time">Time</label>
          <input type="time" name="appointment_time" id="appointment_time"
                 value="<?= htmlspecialchars(substr($a['appointment_time'], 0, 5)) ?>" required>
        </div>
      </div>

      <div class="appt-form-row">
        <label for="status">Status</label>
        <select name="status" id="status">
          <?php
          $statuses = [
              'requested'   => 'Requested',
              'confirmed'   => 'Confirmed',
              'in_progress' => 'In Progress',
              'completed'   => 'Completed',
              'cancelled'   => 'Cancelled',
          ];
          foreach ($statuses as $value => $label): ?>
            <option value="<?= $value ?>" <?= $a['status'] === $value ? 'selected' : '' ?>>
              <?= $label ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="appt-form-row">
        <label for="notes">Notes</label>
        <textarea name="notes" id="notes" rows="4"
                  placeholder="Any special instructions or remarks…"><?= htmlspecialchars($a['notes'] ?? '') ?></textarea>
      </div>

      <div class="appt-form-actions">
        <button type="submit" class="btn-primary">
          <i class="fa-regular fa-floppy-disk"></i>
          <span>Save Changes</span>
        </button>
        <a href="<?= $B ?>/admin/admin-appointments" class="btn-secondary">
          Cancel
        </a>
      </div>
    </form>
  </section>
</main>
</body>
</html>
