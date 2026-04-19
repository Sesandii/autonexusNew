<?php /* Admin view: renders admin-viewstaff/index page. */ ?>
<?php $B = rtrim(BASE_URL, '/'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AutoNexus - Staff Management</title>

  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= $B ?>/public/assets/css/admin-admin-viewstaffindex.css?v=1">
</head>

<body>
  <?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

  <main class="main-content">
    <section class="management">
      <div class="management-header">
        <div>
          <h2>Staff Management</h2>
          <p class="page-note">One unified staff control center for managers, supervisors, mechanics, and receptionists.
          </p>
        </div>
      </div>

      <div class="summary-grid">
        <div class="summary-card">
          <p>Total Staff</p>
          <h3><?= (int) $summary['total'] ?></h3>
        </div>
        <div class="summary-card">
          <p>Active</p>
          <h3><?= (int) $summary['active'] ?></h3>
        </div>
        <div class="summary-card">
          <p>Inactive</p>
          <h3><?= (int) $summary['inactive'] ?></h3>
        </div>
        <div class="summary-card">
          <p>Available Mechanics</p>
          <h3><?= (int) $summary['available'] ?></h3>
        </div>
        <div class="summary-card">
          <p>Busy Mechanics</p>
          <h3><?= (int) $summary['busy'] ?></h3>
        </div>
      </div>

      <div class="chip-group">
        <a class="chip-btn" href="<?= $B ?>/admin/admin-viewstaff"><i class="fa-solid fa-users"></i> All Staff</a>
        <a class="chip-btn" href="<?= $B ?>/admin/service-managers"><i class="fa-solid fa-user-tie"></i> Managers:
          <?= (int) $counts['manager'] ?></a>
        <a class="chip-btn" href="<?= $B ?>/admin/supervisors"><i class="fa-solid fa-clipboard-check"></i> Supervisors:
          <?= (int) $counts['supervisor'] ?></a>
        <a class="chip-btn" href="<?= $B ?>/admin/mechanics"><i class="fa-solid fa-screwdriver-wrench"></i> Mechanics:
          <?= (int) $counts['mechanic'] ?></a>
        <a class="chip-btn" href="<?= $B ?>/admin/viewreceptionist"><i class="fa-solid fa-headset"></i> Receptionists:
          <?= (int) $counts['receptionist'] ?></a>
      </div>

      <div class="filters-row">
        <form method="GET" action="<?= $B ?>/admin/admin-viewstaff">
          <input class="search-input" type="text" name="q" value="<?= htmlspecialchars($filters['q'] ?? '') ?>"
            placeholder="Search by staff code, name, email, phone, branch...">

          <select name="branch_id">
            <option value="">All Branches</option>
            <?php foreach ($branches as $branch): ?>
              <option value="<?= (int) $branch['branch_id'] ?>" <?= (($filters['branch_id'] ?? '') == $branch['branch_id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($branch['name']) ?> (<?= htmlspecialchars($branch['branch_code']) ?>)
              </option>
            <?php endforeach; ?>
          </select>

          <select name="role">
            <option value="">All Roles</option>
            <option value="manager" <?= (($filters['role'] ?? '') === 'manager') ? 'selected' : '' ?>>Managers</option>
            <option value="supervisor" <?= (($filters['role'] ?? '') === 'supervisor') ? 'selected' : '' ?>>Supervisors
            </option>
            <option value="mechanic" <?= (($filters['role'] ?? '') === 'mechanic') ? 'selected' : '' ?>>Mechanics</option>
            <option value="receptionist" <?= (($filters['role'] ?? '') === 'receptionist') ? 'selected' : '' ?>>
              Receptionists</option>
          </select>

          <select name="staff_status">
            <option value="">All Status</option>
            <option value="active" <?= (($filters['staff_status'] ?? '') === 'active') ? 'selected' : '' ?>>Active</option>
            <option value="inactive" <?= (($filters['staff_status'] ?? '') === 'inactive') ? 'selected' : '' ?>>Inactive
            </option>
            <option value="pending" <?= (($filters['staff_status'] ?? '') === 'pending') ? 'selected' : '' ?>>Pending
            </option>
          </select>

          <button class="add-btn" type="submit"><i class="fa-solid fa-filter"></i> Apply Filters</button>
        </form>
      </div>

      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Staff</th>
              <th>Role</th>
              <th>Branch</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($records)): ?>
              <tr>
                <td colspan="4" class="empty-row"><i class="fa-regular fa-face-frown"></i> No staff records found.</td>
              </tr>
            <?php endif; ?>

            <?php foreach ($records as $row): ?>
              <?php $statusClass = 'status--' . strtolower((string) $row['staff_status']); ?>
              <tr>
                <td>
                  <strong><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></strong><br>
                  <small><?= htmlspecialchars($row['staff_code'] ?: '-') ?> ·
                    <?= htmlspecialchars($row['email'] ?: '-') ?></small><br>
                  <small><?= htmlspecialchars($row['phone'] ?: '-') ?></small>
                </td>
                <td>
                  <span class="role-badge"><?= htmlspecialchars($row['role_label']) ?></span>
                </td>
                <td>
                  <?= htmlspecialchars($row['branch_name']) ?><br>
                  <small><?= htmlspecialchars($row['branch_code']) ?></small>
                </td>
                <td>
                  <span class="status-pill <?= $statusClass ?>">
                    <span class="dot"></span>
                    <?= ucfirst(htmlspecialchars((string) $row['staff_status'])) ?>
                  </span>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>
</body>

</html>