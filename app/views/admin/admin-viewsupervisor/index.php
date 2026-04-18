<?php
$current = 'supervisors';
$B = rtrim($base ?? BASE_URL, '/');
$q = $q ?? '';
$status = $status ?? 'all';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Workshop Supervisors • AutoNexus Admin</title>

  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="<?= $B ?>/app/views/admin/admin-viewsupervisor/supervisors.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

</head>

<body>

  <?php include(__DIR__ . '/../../layouts/admin-sidebar/sidebar.php'); ?>

  <main class="main-content">

    <section class="management">

      <!-- Header -->
      <header class="management-header">
        <div>
          <h2>Workshop Supervisors</h2>
          <p class="management-subtitle">Monitor and manage workshop floor supervisors</p>
        </div>

        <div class="tools">
          <a class="add-btn back-btn" href="<?= $B ?>/admin/admin-viewstaff">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Back to Staff Management</span>
          </a>

          <form method="get" action="<?= $B ?>/admin/supervisors" class="tools">
            <input type="text" id="searchInput" class="search-input" name="q" placeholder="Search supervisor…"
              value="<?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?>">
            <select class="status-filter" name="status">
              <option value="all" <?= $status === 'all' ? 'selected' : '' ?>>All Status</option>
              <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
              <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>
            <button class="add-btn" type="submit" style="border:none; cursor:pointer;">
              <i class="fa-solid fa-filter"></i>
              <span>Filter</span>
            </button>
            <a class="add-btn" href="<?= $B ?>/admin/supervisors/create">
              <i class="fa-solid fa-user-plus"></i>
              <span>Add Supervisor</span>
            </a>
          </form>
        </div>
      </header>

      <!-- Supervisors Table -->
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Supervisor Code</th>
              <th>Full Name</th>
              <th>Email</th>
              <th>Contact</th>
              <th>Status</th>
              <th>Created</th>
              <th class="th-actions">Actions</th>
            </tr>
          </thead>

          <tbody id="supervisorRows">
            <?php foreach (($supervisors ?? []) as $row):
              $name = trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''));
              $status = strtolower($row['status'] ?? 'active');
              $pill = $status === 'inactive' ? 'status--inactive' : 'status--active';
              ?>
              <tr data-status="<?= htmlspecialchars($status) ?>">
                <td><?= htmlspecialchars($row['supervisor_code']) ?></td>
                <td><?= htmlspecialchars($name) ?></td>
                <td><?= htmlspecialchars($row['email'] ?? '-') ?></td>
                <td><?= htmlspecialchars($row['phone'] ?? '-') ?></td>

                <td>
                  <span class="status-pill <?= $pill ?>">
                    <span class="dot"></span>
                    <?= ucfirst($status) ?>
                  </span>
                </td>

                <td><?= htmlspecialchars(substr($row['created_at'], 0, 10)) ?></td>

                <td class="table-actions">

                  <a class="chip-btn chip-btn--light"
                    href="<?= $B ?>/admin/supervisors/<?= urlencode($row['supervisor_code']) ?>">
                    <i class="fa-solid fa-eye"></i><span>View</span>
                  </a>

                  <a class="chip-btn chip-btn--dark"
                    href="<?= $B ?>/admin/supervisors/<?= urlencode($row['supervisor_code']) ?>/edit">
                    <i class="fa-solid fa-pen"></i><span>Edit</span>
                  </a>

                  <form method="post"
                    action="<?= $B ?>/admin/supervisors/<?= urlencode($row['supervisor_code']) ?>/delete"
                    style="display:inline" onsubmit="return confirm('Delete this supervisor?')">
                    <button class="chip-btn chip-btn--danger" type="submit">
                      <i class="fa-solid fa-trash"></i><span>Delete</span>
                    </button>
                  </form>

                </td>
              </tr>

            <?php endforeach; ?>

            <?php if (empty($supervisors)): ?>
              <tr>
                <td colspan="7" class="empty-row">
                  <i class="fa-regular fa-circle-question"></i>
                  <span>No supervisors found.</span>
                </td>
              </tr>
            <?php endif; ?>

          </tbody>
        </table>
      </div>

    </section>

  </main>

</body>

</html>