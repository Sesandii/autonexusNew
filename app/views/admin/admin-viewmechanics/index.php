<?php
$current = $current ?? 'mechanics';
$B = rtrim(BASE_URL, '/');
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
        <input
          type="text"
          class="search-input"
          id="searchInput"
          placeholder="Search by name, code, email or phone…"
          aria-label="Search mechanics"
        >

        <a class="add-btn" href="<?= $B ?>/admin/mechanics/create">
          <i class="fa-solid fa-plus"></i>
          <span>Add Mechanic</span>
        </a>
      </div>
    </header>

    <div class="table-wrap">
      <table id="mechanicsTable">
        <thead>
          <tr>
            <th>ID</th>
            <th>Code</th>
            <th>Full Name</th>
            <th>Specialization</th>
            <th>Exp (yrs)</th>
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
              <td><?= htmlspecialchars($m['mechanic_id']) ?></td>
              <td><?= htmlspecialchars($m['mechanic_code'] ?? '') ?></td>
              <td><?= htmlspecialchars(($m['first_name'] ?? '') . ' ' . ($m['last_name'] ?? '')) ?></td>
              <td><?= htmlspecialchars($m['specialization'] ?? '') ?></td>
              <td><?= htmlspecialchars($m['experience_years'] ?? 0) ?></td>
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
                <?php $statusClass = ($m['mech_status'] === 'inactive') ? 'status--inactive' : 'status--active'; ?>
                <span class="status-pill <?= $statusClass ?>">
                  <span class="dot"></span>
                  <?= htmlspecialchars(ucfirst($m['mech_status'])) ?>
                </span>
              </td>
              <td><?= htmlspecialchars($m['created_at'] ?? '') ?></td>
              <td class="table-actions">
                <a
                  class="chip-btn chip-btn--light"
                  href="<?= $B ?>/admin/mechanics/<?= urlencode($m['mechanic_id']) ?>"
                  title="View mechanic"
                >
                  <i class="fa-solid fa-eye"></i><span>View</span>
                </a>

                <a
                  class="chip-btn chip-btn--dark"
                  href="<?= $B ?>/admin/mechanics/<?= urlencode($m['mechanic_id']) ?>/edit"
                  title="Edit mechanic"
                >
                  <i class="fa-solid fa-pen"></i><span>Edit</span>
                </a>

                <form
                  action="<?= $B ?>/admin/mechanics/<?= urlencode($m['mechanic_id']) ?>/delete"
                  method="post"
                  onsubmit="return confirm('Delete this mechanic?');"
                  class="inline-form"
                >
                  <button type="submit" class="chip-btn chip-btn--danger" title="Delete mechanic">
                    <i class="fa-solid fa-trash"></i><span>Delete</span>
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="10" class="empty-row">
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

<script>
(function () {
  var q = document.getElementById('searchInput');
  if (!q) return;
  var rows = Array.from(document.querySelectorAll('#mechanicsTable tbody tr'));

  q.addEventListener('input', function () {
    var v = this.value.toLowerCase();
    rows.forEach(function (tr) {
      var text = tr.innerText.toLowerCase();
      tr.style.display = text.includes(v) ? '' : 'none';
    });
  });
})();
</script>

</body>
</html>
