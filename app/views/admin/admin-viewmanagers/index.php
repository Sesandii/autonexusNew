<?php
/** @var array $rows */
/** @var string $q */
/** @var string $status */
/** @var string $base */
$current = 'service-managers';

// Safe defaults (prevents undefined variable warnings)
$rows   = $rows   ?? [];
$q      = $q      ?? '';
$status = $status ?? 'all';
$base   = $base   ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Service Managers</title>
  <link rel="stylesheet" href="../app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="../app/views/layouts/admin-sidebar/styles.css">
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
      <h2>Service Managers Management</h2>

      <!-- Server-side search/filter form -->
      <form method="get" action="<?= htmlspecialchars($base . '/service-managers', ENT_QUOTES, 'UTF-8') ?>" class="tools">
        <input
          type="text"
          class="search-input"
          name="q"
          placeholder="Search by id/name/username/email/code..."
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

        <!-- Optional: Link to a separate "create" page or open your modal page -->
       <a href="<?= htmlspecialchars($base . '/admin/service-managers/create', ENT_QUOTES, 'UTF-8') ?>" class="add-btn">+ Add New Manager</a>

        
      </form>
    </div>

    <div style="margin-top:20px; overflow:auto;">
      <table id="tbl">
        <thead>
          <tr>
            <th>Manager ID</th>
            <th>Name</th>
            <th>Username</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Status</th>
            <th>Created</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php if (!empty($rows)): ?>
          <?php foreach ($rows as $r):
            $statusClass = (($r['status'] ?? 'active') === 'inactive') ? 'status--inactive' : 'status--active';
          ?>
          <tr>
            <td><?= htmlspecialchars($r['manager_id'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($r['username'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($r['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($r['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
            <td class="<?= $statusClass ?>"><?= htmlspecialchars($r['status'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($r['created_at'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
            <td>
  <a class="icon-btn" title="View"
     href="<?= htmlspecialchars($base . '/admin/service-managers/' . urlencode((string)$r['manager_id']), ENT_QUOTES, 'UTF-8') ?>">
    <i class="fas fa-eye"></i>
  </a>

  <a class="icon-btn" title="Edit"
   href="<?= htmlspecialchars($base . '/admin/service-managers/' . urlencode((string)$r['manager_id']) . '/edit', ENT_QUOTES, 'UTF-8') ?>">
  <i class="fas fa-pen"></i>
</a>


<form method="post"
      action="<?= htmlspecialchars($base . '/admin/service-managers/' . urlencode((string)$row['manager_id']) . '/delete', ENT_QUOTES, 'UTF-8') ?>"
      onsubmit="return confirm('Delete this manager? This cannot be undone.');"
      style="display:inline">
  <button type="submit" class="btn-danger"><i class="fas fa-trash"></i></button>
</form>

</td>

          </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="8" class="muted">No managers found.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>
</body>
</html>
