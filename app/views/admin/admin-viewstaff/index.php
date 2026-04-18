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

  <style>
    .summary-grid {
      display:grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap:14px;
      margin: 0 0 20px;
    }
    .summary-card {
      background:#fff;
      border-radius:12px;
      padding:16px;
      box-shadow:0 2px 8px rgba(0,0,0,.05);
    }
    .summary-card p { color:#6b7280; font-size:13px; margin-bottom:6px; }
    .summary-card h3 { font-size:24px; color:#111827; }
    .filters-row {
      display:flex;
      gap:10px;
      flex-wrap:wrap;
      margin-bottom:18px;
      align-items:center;
    }
    .filters-row form {
      display:flex;
      gap:10px;
      flex-wrap:wrap;
      width:100%;
    }
    .filters-row input,
    .filters-row select {
      padding:9px 12px;
      border:1px solid #d1d5db;
      border-radius:8px;
      background:#fff;
      font-size:14px;
      min-width:160px;
    }
    .filters-row .search-input { min-width:260px; }
    .chip-group {
      display:flex;
      gap:8px;
      flex-wrap:wrap;
      margin-bottom:18px;
    }
    .chip-btn {
      display:inline-flex;
      align-items:center;
      gap:6px;
      padding:8px 12px;
      border-radius:999px;
      background:#fff;
      border:1px solid #e5e7eb;
      font-size:13px;
      color:#374151;
      font-weight:600;
    }
    .mini-form {
      display:flex;
      gap:8px;
      align-items:center;
      flex-wrap:wrap;
    }
    .mini-form select {
      padding:7px 10px;
      border-radius:8px;
      border:1px solid #d1d5db;
      font-size:13px;
      background:#fff;
      min-width:120px;
    }
    .mini-form button {
      border:none;
      background:#cf3202;
      color:#fff;
      border-radius:999px;
      padding:7px 12px;
      font-size:12px;
      cursor:pointer;
      font-weight:600;
    }
    .role-badge {
      display:inline-block;
      padding:4px 10px;
      border-radius:999px;
      background:#f3f4f6;
      color:#374151;
      font-size:12px;
      font-weight:600;
    }
    .status--available { background:#dbeafe; border-color:#bfdbfe; color:#1d4ed8; }
    .status--available .dot { background:#2563eb; }
    .status--busy { background:#fef3c7; border-color:#fde68a; color:#92400e; }
    .status--busy .dot { background:#d97706; }
    .workload-pill {
      display:inline-flex;
      align-items:center;
      gap:6px;
      padding:4px 10px;
      border-radius:999px;
      background:#f9fafb;
      border:1px solid #e5e7eb;
      font-size:12px;
      font-weight:600;
      color:#374151;
    }
    .page-note {
      color:#6b7280;
      font-size:14px;
      margin-top:4px;
      margin-bottom:18px;
    }
  </style>
</head>
<body>
<?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

<main class="main-content">
  <section class="management">
    <div class="management-header">
      <div>
        <h2>Staff Management</h2>
        <p class="page-note">One unified staff control center for managers, supervisors, mechanics, and receptionists.</p>
      </div>
    </div>

    <div class="summary-grid">
      <div class="summary-card">
        <p>Total Staff</p>
        <h3><?= (int)$summary['total'] ?></h3>
      </div>
      <div class="summary-card">
        <p>Active</p>
        <h3><?= (int)$summary['active'] ?></h3>
      </div>
      <div class="summary-card">
        <p>Inactive</p>
        <h3><?= (int)$summary['inactive'] ?></h3>
      </div>
      <div class="summary-card">
        <p>Available Mechanics</p>
        <h3><?= (int)$summary['available'] ?></h3>
      </div>
      <div class="summary-card">
        <p>Busy Mechanics</p>
        <h3><?= (int)$summary['busy'] ?></h3>
      </div>
    </div>

    <div class="chip-group">
      <span class="chip-btn"><i class="fa-solid fa-user-tie"></i> Managers: <?= (int)$counts['manager'] ?></span>
      <span class="chip-btn"><i class="fa-solid fa-clipboard-check"></i> Supervisors: <?= (int)$counts['supervisor'] ?></span>
      <span class="chip-btn"><i class="fa-solid fa-screwdriver-wrench"></i> Mechanics: <?= (int)$counts['mechanic'] ?></span>
      <span class="chip-btn"><i class="fa-solid fa-headset"></i> Receptionists: <?= (int)$counts['receptionist'] ?></span>
    </div>

    <div class="filters-row">
      <form method="GET" action="<?= $B ?>/admin/admin-viewstaff">
        <input class="search-input" type="text" name="q" value="<?= htmlspecialchars($filters['q'] ?? '') ?>" placeholder="Search by staff code, name, email, phone, branch...">

        <select name="branch_id">
          <option value="">All Branches</option>
          <?php foreach ($branches as $branch): ?>
            <option value="<?= (int)$branch['branch_id'] ?>" <?= (($filters['branch_id'] ?? '') == $branch['branch_id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($branch['name']) ?> (<?= htmlspecialchars($branch['branch_code']) ?>)
            </option>
          <?php endforeach; ?>
        </select>

        <select name="role">
          <option value="">All Roles</option>
          <option value="manager" <?= (($filters['role'] ?? '') === 'manager') ? 'selected' : '' ?>>Managers</option>
          <option value="supervisor" <?= (($filters['role'] ?? '') === 'supervisor') ? 'selected' : '' ?>>Supervisors</option>
          <option value="mechanic" <?= (($filters['role'] ?? '') === 'mechanic') ? 'selected' : '' ?>>Mechanics</option>
          <option value="receptionist" <?= (($filters['role'] ?? '') === 'receptionist') ? 'selected' : '' ?>>Receptionists</option>
        </select>

        <select name="staff_status">
          <option value="">All Status</option>
          <option value="active" <?= (($filters['staff_status'] ?? '') === 'active') ? 'selected' : '' ?>>Active</option>
          <option value="inactive" <?= (($filters['staff_status'] ?? '') === 'inactive') ? 'selected' : '' ?>>Inactive</option>
          <option value="available" <?= (($filters['staff_status'] ?? '') === 'available') ? 'selected' : '' ?>>Available</option>
          <option value="busy" <?= (($filters['staff_status'] ?? '') === 'busy') ? 'selected' : '' ?>>Busy</option>
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
            <th>Workload</th>
            <th>Quick Transfer</th>
            <th>Quick Status Update</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($records)): ?>
            <tr>
              <td colspan="7" class="empty-row"><i class="fa-regular fa-face-frown"></i> No staff records found.</td>
            </tr>
          <?php endif; ?>

          <?php foreach ($records as $row): ?>
            <?php $statusClass = 'status--' . strtolower((string)$row['staff_status']); ?>
            <tr>
              <td>
                <strong><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></strong><br>
                <small><?= htmlspecialchars($row['staff_code'] ?: '-') ?> · <?= htmlspecialchars($row['email'] ?: '-') ?></small><br>
                <small><?= htmlspecialchars($row['phone'] ?: '-') ?></small>
              </td>
              <td>
                <span class="role-badge"><?= htmlspecialchars($row['role_label']) ?></span><br>
                <?php if (!empty($row['extra_info'])): ?>
                  <small><?= htmlspecialchars($row['extra_info']) ?></small>
                <?php endif; ?>
              </td>
              <td>
                <?= htmlspecialchars($row['branch_name']) ?><br>
                <small><?= htmlspecialchars($row['branch_code']) ?></small>
              </td>
              <td>
                <span class="status-pill <?= $statusClass ?>">
                  <span class="dot"></span>
                  <?= ucfirst(htmlspecialchars((string)$row['staff_status'])) ?>
                </span>
              </td>
              <td>
                <span class="workload-pill">
                  <i class="fa-solid fa-briefcase"></i>
                  <?= (int)$row['workload_count'] ?> active items
                </span>
              </td>
              <td>
                <form class="mini-form" method="POST" action="<?= $B ?>/admin/admin-viewstaff/transfer">
                  <input type="hidden" name="role" value="<?= htmlspecialchars($row['role']) ?>">
                  <input type="hidden" name="staff_id" value="<?= (int)$row['staff_id'] ?>">
                  <select name="branch_id" required>
                    <?php foreach ($branches as $branch): ?>
                      <option value="<?= (int)$branch['branch_id'] ?>" <?= ((int)$row['branch_id'] === (int)$branch['branch_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($branch['name']) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <button type="submit">Transfer</button>
                </form>
              </td>
              <td>
                <form class="mini-form" method="POST" action="<?= $B ?>/admin/admin-viewstaff/update-status">
                  <input type="hidden" name="role" value="<?= htmlspecialchars($row['role']) ?>">
                  <input type="hidden" name="staff_id" value="<?= (int)$row['staff_id'] ?>">
                  <input type="hidden" name="user_id" value="<?= (int)$row['user_id'] ?>">
                  <select name="status" required>
                    <?php if ($row['role'] === 'mechanic'): ?>
                      <option value="active" <?= ($row['staff_status'] === 'active') ? 'selected' : '' ?>>Active</option>
                      <option value="inactive" <?= ($row['staff_status'] === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                      <option value="available" <?= ($row['staff_status'] === 'available') ? 'selected' : '' ?>>Available</option>
                      <option value="busy" <?= ($row['staff_status'] === 'busy') ? 'selected' : '' ?>>Busy</option>
                    <?php else: ?>
                      <option value="active" <?= ($row['staff_status'] === 'active') ? 'selected' : '' ?>>Active</option>
                      <option value="inactive" <?= ($row['staff_status'] === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                    <?php endif; ?>
                  </select>
                  <button type="submit">Update</button>
                </form>
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