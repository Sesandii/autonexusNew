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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .main-content { margin-left:260px;padding:30px;background:#fff;min-height:100vh; }
    .form-card { max-width:700px;border:1px solid #eee;border-radius:10px;padding:20px;background:#fafafa; }
    .form-row { margin-bottom:14px; }
    .form-row label { display:block;font-weight:600;margin-bottom:4px; }
    .form-row input,
    .form-row select,
    .form-row textarea { width:100%;padding:8px;border-radius:6px;border:1px solid #d1d5db;font-size:14px; }
    .form-actions { margin-top:20px;display:flex;gap:10px; }
    .btn { padding:8px 14px;border-radius:6px;border:none;cursor:pointer;font-size:14px; }
    .btn-primary { background:#2563eb;color:#fff; }
    .btn-secondary { background:#e5e7eb;color:#111827;text-decoration:none;display:inline-flex;align-items:center; }
  </style>
</head>
<body>
<?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

<main class="main-content">
  <header class="page-header">
    <div class="page-breadcrumb">
      <a href="<?= $B ?>/admin/appointments">Appointments</a>
      <span>›</span>
      <span>Edit Appointment #<?= htmlspecialchars((string)$a['appointment_id']) ?></span>
    </div>
  </header>

  <section style="margin-top:20px;">
    <form class="form-card" method="post" action="<?= $B ?>/admin/appointments/update">
      <input type="hidden" name="appointment_id" value="<?= (int)$a['appointment_id'] ?>"/>

      <div class="form-row">
        <label>Customer</label>
        <input type="text" value="<?= htmlspecialchars($a['customer_name'] ?? '') ?>" disabled>
      </div>

      <div class="form-row">
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

      <div class="form-row">
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

      <div class="form-row">
        <label for="appointment_date">Appointment Date</label>
        <input type="date" name="appointment_date" id="appointment_date"
               value="<?= htmlspecialchars($a['appointment_date']) ?>" required>
      </div>

      <div class="form-row">
        <label for="appointment_time">Appointment Time</label>
        <input type="time" name="appointment_time" id="appointment_time"
               value="<?= htmlspecialchars(substr($a['appointment_time'], 0, 5)) ?>" required>
      </div>

      <div class="form-row">
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

      <div class="form-row">
        <label for="notes">Notes</label>
        <textarea name="notes" id="notes" rows="4"><?= htmlspecialchars($a['notes'] ?? '') ?></textarea>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-primary">
          <i class="fa-regular fa-floppy-disk"></i>&nbsp;Save Changes
        </button>
        <a href="<?= $B ?>/admin/appointments" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </section>
</main>
</body>
</html>
