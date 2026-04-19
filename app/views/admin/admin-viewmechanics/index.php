<?php /* Admin view: renders admin-viewmechanics/index page. */ ?>
<?php
$current = $current ?? 'mechanics';
$B = rtrim($base ?? BASE_URL, '/');
$q = $q ?? '';
$status = $status ?? 'all';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mechanics • AutoNexus</title>

  <!-- Shared admin styles -->
  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

  <?php include __DIR__ . '/../../layouts/admin-sidebar/sidebar.php'; ?>

  <main class="main-content">
    <section class="management">
      <header class="management-header">
        <div>
          <h2>Mechanics</h2>
          <p class="management-subtitle">Manage your mechanics across all branches.</p>
        </div>

        <div class="tools">
          <a class="add-btn back-btn" href="<?= $B ?>/admin/admin-viewstaff">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Back to Staff Management</span>
          </a>

          <form method="get" action="<?= $B ?>/admin/mechanics" class="tools">
            <input type="text" class="search-input" id="searchInput" name="q"
              placeholder="Search by name, code, email or phone…"
              value="<?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?>" aria-label="Search mechanics">

            <select class="status-filter" name="status" aria-label="Filter mechanics by status">
              <option value="all" <?= $status === 'all' ? 'selected' : '' ?>>All Status</option>
              <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
              <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
              <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
            </select>

            <button class="add-btn" type="submit" style="border:none; cursor:pointer;">
              <i class="fa-solid fa-filter"></i>
              <span>Filter</span>
            </button>

            <a class="add-btn" href="<?= $B ?>/admin/mechanics/create">
              <i class="fa-solid fa-plus"></i>
              <span>Add Mechanic</span>
            </a>
          </form>

        </div>
      </header>

      <div class="table-wrap">
        <table id="mechanicsTable">
          <thead>
            <tr>
              <th>Code</th>
              <th>Full Name</th>
              <th>Specialization</th>
              <th>Phone</th>
              <th>Branch</th>
              <th>Status</th>
              <th>Created</th>
              <th class="th-actions">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($mechanics)): ?>
              <?php foreach ($mechanics as $m): ?>
                <tr>
                  <td><?= htmlspecialchars($m['mechanic_code'] ?? '') ?></td>
                  <td><?= htmlspecialchars(($m['first_name'] ?? '') . ' ' . ($m['last_name'] ?? '')) ?></td>
                  <td><?= htmlspecialchars($m['specialization'] ?? '') ?></td>
                  <td><?= htmlspecialchars($m['phone'] ?? '') ?></td>
                  <td>
                    <?php
                    $code = $m['branch_code'] ?? null;
                    $name = $m['branch_name'] ?? null;
                    echo ($name || $code)
                      ? '[' . htmlspecialchars($code ?? '-') . '] ' . htmlspecialchars($name ?? '-')
                      : '—';
                    ?>
                  </td>
                  <td>
                    <?php $statusClass = (($m['user_status'] ?? 'active') === 'inactive') ? 'status--inactive' : 'status--active'; ?>
                    <span class="status-pill <?= $statusClass ?>">
                      <span class="dot"></span>
                      <?= htmlspecialchars(ucfirst($m['user_status'] ?? 'active')) ?>
                    </span>
                  </td>
                  <td><?= htmlspecialchars($m['created_at'] ?? '') ?></td>
                  <td class="table-actions">
                    <a class="chip-btn chip-btn--light" href="<?= $B ?>/admin/mechanics/<?= urlencode($m['mechanic_id']) ?>"
                      title="View mechanic">
                      <i class="fa-solid fa-eye"></i><span>View</span>
                    </a>

                    <a class="chip-btn chip-btn--dark"
                      href="<?= $B ?>/admin/mechanics/<?= urlencode($m['mechanic_id']) ?>/edit" title="Edit mechanic">
                      <i class="fa-solid fa-pen"></i><span>Edit</span>
                    </a>

                    <form action="<?= $B ?>/admin/mechanics/<?= urlencode($m['mechanic_id']) ?>/delete" method="post"
                      onsubmit="return confirm('Delete this mechanic?');" class="inline-form">
                      <button type="submit" class="chip-btn chip-btn--danger" title="Delete mechanic">
                        <i class="fa-solid fa-trash"></i><span>Delete</span>
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="9" class="empty-row">
                  <i class="fa-regular fa-circle-question"></i>
                  <span>No mechanics found. Use “Add Mechanic” to create one.</span>
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