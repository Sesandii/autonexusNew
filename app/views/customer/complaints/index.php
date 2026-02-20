<?php
$base = rtrim(BASE_URL, '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($title ?? 'My Complaints') ?> - AutoNexus</title>

  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/sidebar.css">
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/complaints.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

  <?php include APP_ROOT . '/views/layouts/customer-sidebar.php'; ?>

  <div class="container">
    <main class="main-content">

      <header class="page-header">
        <h1 class="page-title">My Complaints</h1>
        <p class="page-subtitle">Submit a complaint about a service or vehicle issue.</p>
      </header>

      <?php if (!empty($flash)): ?>
        <div class="flash-message"><?= htmlspecialchars($flash) ?></div>
      <?php endif; ?>

      <!-- Submit new complaint -->
      <section class="card complaint-form-card">
        <h2>Submit a New Complaint</h2>

        <?php if (empty($vehicles)): ?>
          <p class="info-msg">
            You have no registered vehicles.
            <a href="<?= $base ?>/customer/profile/vehicle">Add a vehicle</a> first.
          </p>
        <?php else: ?>
        <form method="POST" action="<?= $base ?>/customer/complaints/store">
          <div class="form-row">
            <div class="form-group">
              <label for="vehicle_id">Vehicle</label>
              <select name="vehicle_id" id="vehicle_id" required>
                <option value="">-- Select vehicle --</option>
                <?php foreach ($vehicles as $v): ?>
                  <option value="<?= (int)$v['vehicle_id'] ?>">
                    <?= htmlspecialchars($v['make'] . ' ' . $v['model'] . ' (' . $v['license_plate'] . ')') ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group">
              <label for="priority">Priority</label>
              <select name="priority" id="priority">
                <option value="Low">Low</option>
                <option value="Medium" selected>Medium</option>
                <option value="High">High</option>
              </select>
            </div>
          </div>

          <div class="form-group full-width">
            <label for="description">Describe the issue</label>
            <textarea name="description" id="description" rows="4"
                      placeholder="Please describe the problem in detail..." required></textarea>
          </div>

          <div class="form-actions">
            <button type="submit" class="btn-primary">
              <i class="fa-regular fa-paper-plane"></i> Submit Complaint
            </button>
          </div>
        </form>
        <?php endif; ?>
      </section>

      <!-- Past complaints -->
      <section class="card complaint-list-card">
        <h2>Your Submitted Complaints</h2>

        <?php if (empty($complaints)): ?>
          <div class="empty-state">
            <i class="fa-solid fa-clipboard-check fa-3x" style="color:#aaa;margin-bottom:.75rem;"></i>
            <p>You haven't submitted any complaints yet.</p>
          </div>
        <?php else: ?>
        <div class="table-wrapper">
          <table class="complaints-table">
            <thead>
              <tr>
                <th>#</th>
                <th>Date</th>
                <th>Vehicle</th>
                <th>Description</th>
                <th>Priority</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($complaints as $c): ?>
                <tr>
                  <td><?= (int)$c['complaint_id'] ?></td>
                  <td><?= htmlspecialchars($c['complaint_date'] ?? '—') ?></td>
                  <td><?= htmlspecialchars(($c['make'] ?? '') . ' ' . ($c['model'] ?? '') . ' — ' . ($c['license_plate'] ?? '')) ?></td>
                  <td><?= htmlspecialchars(mb_strimwidth($c['description'] ?? '', 0, 80, '…')) ?></td>
                  <td>
                    <span class="badge priority-<?= htmlspecialchars(strtolower($c['priority'] ?? 'medium')) ?>">
                      <?= htmlspecialchars($c['priority'] ?? 'Medium') ?>
                    </span>
                  </td>
                  <td>
                    <span class="badge status-<?= htmlspecialchars(strtolower(str_replace(' ', '-', $c['status'] ?? 'open'))) ?>">
                      <?= htmlspecialchars($c['status'] ?? 'Open') ?>
                    </span>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php endif; ?>
      </section>

    </main>
  </div>

</body>
</html>
