<?php
/** @var array $receptionists */
$current = 'receptionists';
$B = rtrim(BASE_URL, '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle ?? 'Receptionists • AutoNexus Admin') ?></title>

  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

<main class="main-content">
  <section class="management">
    <header class="management-header">
      <div>
        <h2>Receptionists</h2>
        <p class="management-subtitle">Manage reception staff across all branches.</p>
      </div>

      <div class="tools">
        <input
          type="text"
          class="search-input"
          id="searchInput"
          placeholder="Search by receptionist code, name, email…"
        />

        <select class="status-filter" id="statusFilter">
          <option value="all">All Status</option>
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </select>

        <a href="<?= $B ?>/admin/receptionists/create" class="add-btn">
          <i class="fa-solid fa-user-plus"></i>
          <span>Add Receptionist</span>
        </a>
      </div>
    </header>

    <div class="table-wrap">
      <table id="receptionistsTable">
        <thead>
          <tr>
            <th>Rec ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Contact Number</th>
            <th>Branch</th>
            <th>Status</th>
            <th>Created At</th>
            <th class="th-actions">Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php if (!empty($receptionists)): ?>
          <?php foreach ($receptionists as $r): ?>
            <?php
              $status = strtolower($r['status'] ?? 'active');
              $pill   = $status === 'inactive' ? 'status--inactive' : 'status--active';
              $code   = $r['receptionist_code'] ?? ('R' . $r['receptionist_id']);
              $name   = trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? ''));
            ?>
            <tr data-status="<?= htmlspecialchars($status) ?>">
              <td><?= htmlspecialchars($code) ?></td>
              <td><?= htmlspecialchars($name ?: '—') ?></td>
              <td><?= htmlspecialchars($r['email'] ?? '—') ?></td>
              <td><?= htmlspecialchars($r['phone'] ?? '—') ?></td>
              <td>
                <?php if (!empty($r['branch_name'])): ?>
                  <?= htmlspecialchars($r['branch_name']) ?>
                  <?php if (!empty($r['branch_code'])): ?>
                    (<?= htmlspecialchars($r['branch_code']) ?>)
                  <?php endif; ?>
                <?php else: ?>
                  —
                <?php endif; ?>
              </td>
              <td>
                <span class="status-pill <?= $pill ?>">
                  <span class="dot"></span>
                  <?= htmlspecialchars(ucfirst($status)) ?>
                </span>
              </td>
              <td><?= htmlspecialchars($r['created_at'] ?? '') ?></td>
              <td class="table-actions">
                <a href="<?= $B ?>/admin/receptionists/show?id=<?= urlencode((string)$r['receptionist_id']) ?>"
                   class="chip-btn chip-btn--light" title="View">
                  <i class="fas fa-eye"></i>
                  <span>View</span>
                </a>

                <a href="<?= $B ?>/admin/receptionists/edit?id=<?= urlencode((string)$r['receptionist_id']) ?>"
                   class="chip-btn chip-btn--dark" title="Edit">
                  <i class="fas fa-pen"></i>
                  <span>Edit</span>
                </a>

                <form class="inline-form"
                      action="<?= $B ?>/admin/receptionists/delete"
                      method="post"
                      onsubmit="return confirm('Delete this receptionist?');">
                  <input type="hidden" name="id"
                         value="<?= htmlspecialchars((string)$r['receptionist_id']) ?>">
                  <button type="submit" class="chip-btn chip-btn--danger" title="Delete">
                    <i class="fa fa-trash"></i>
                    <span>Delete</span>
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="8" class="empty-row">
              <i class="fa-regular fa-face-smile"></i>
              No receptionists added yet.
            </td>
          </tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>
</main>

<script>
  (function () {
    const searchInput  = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const rows         = document.querySelectorAll('#receptionistsTable tbody tr');

    function applyFilters() {
      const search = (searchInput.value || '').toLowerCase();
      const status = statusFilter.value;

      rows.forEach(row => {
        const text      = row.innerText.toLowerCase();
        const rowStatus = row.getAttribute('data-status');

        const matchSearch = !search || text.includes(search);
        const matchStatus = (status === 'all') || (status === rowStatus);

        row.style.display = (matchSearch && matchStatus) ? '' : 'none';
      });
    }

    if (searchInput)  searchInput.addEventListener('input', applyFilters);
    if (statusFilter) statusFilter.addEventListener('change', applyFilters);

    // Default to active on load
    if (statusFilter) {
      statusFilter.value = 'active';
      applyFilters();
    }
  })();
</script>

</body>
</html>
