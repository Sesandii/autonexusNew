<?php
/** @var array $branches */
/** @var string $base */
$current  = 'branches';
$branches = $branches ?? [];
$base     = rtrim($base ?? BASE_URL, '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Branches Management</title>

  <!-- Use absolute URLs so they don't break under /admin/branches -->
  <link rel="stylesheet" href="<?= $base ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="<?= $base ?>/app/views/layouts/admin-sidebar/styles.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

  <style>
    .sidebar { position: fixed; top:0; left:0; width:260px; height:100vh; overflow-y:auto; }
    .main-content { margin-left:260px; padding:30px; background:#fff; min-height:100vh; }
  </style>
</head>
<body>
  <?php include(__DIR__ . '/../../layouts/admin-sidebar/sidebar.php'); ?>

  <main class="main-content">
    <div class="management-header">
      <h2>Branch Management</h2>

      <!-- Filter must submit to /admin/branches -->
      <form method="get" action="<?= htmlspecialchars($base . '/admin/branches', ENT_QUOTES, 'UTF-8') ?>" class="tools">
        <input
          type="text"
          class="search-input"
          name="q"
          placeholder="Search by branch_code/name..."
          value="<?= htmlspecialchars($q ?? '', ENT_QUOTES, 'UTF-8') ?>"
        />
        <select class="status-filter" name="status">
          <?php
            $opts = ['all'=>'All Status','active'=>'Active','inactive'=>'Inactive'];
            foreach ($opts as $val => $label):
          ?>
            <option value="<?= $val ?>" <?= ($status === $val ? 'selected' : '') ?>><?= $label ?></option>
          <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-secondary">Filter</button>

        <!-- Create page under /admin/branches/create -->
        <a href="<?= htmlspecialchars($base . '/admin/branches/create', ENT_QUOTES, 'UTF-8') ?>" class="add-btn">+ Add New Branch</a>
      </form>
    </div>

    <div style="margin-top:20px; overflow:auto;">
      <table id="tbl">
        <thead>
          <tr>
            <th>Branch Code</th>
            <th>Name</th>
            <th>City</th>
            <th>Manager ID</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Status</th>
            <th>Created</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php if (!empty($branches)): ?>
          <?php foreach ($branches as $b):
            $statusClass = (($b['status'] ?? 'active') === 'inactive') ? 'status--inactive' : 'status--active';
            $code = (string)($b['branch_code'] ?? '');
          ?>
          <tr>
            <td><?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($b['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($b['city'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($b['manager_id'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($b['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($b['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
            <td class="<?= $statusClass ?>"><?= htmlspecialchars($b['status'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($b['created_at'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
            <td>
              <!-- Routes must be /admin/branches/... -->
              <a class="icon-btn" title="View"
                 href="<?= htmlspecialchars($base . '/admin/branches/' . rawurlencode($code), ENT_QUOTES, 'UTF-8') ?>">
                <i class="fas fa-eye"></i>
              </a>

              <a class="icon-btn" title="Edit"
                 href="<?= htmlspecialchars($base . '/admin/branches/' . rawurlencode($code) . '/edit', ENT_QUOTES, 'UTF-8') ?>">
                <i class="fas fa-pen"></i>
              </a>

              <form class="inline" method="post"
                    action="<?= htmlspecialchars($base . '/admin/branches/' . rawurlencode($code) . '/delete', ENT_QUOTES, 'UTF-8') ?>"
                    onsubmit="return confirm('Delete this branch?');"
                    style="display:inline-block">
                <button type="submit" class="icon-btn" title="Delete">
                  <i class="fas fa-trash"></i>
                </button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="9" class="muted">No branches found.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>
</body>
</html>
