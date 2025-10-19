<?php $current='supervisors'; ?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Admin • Supervisors</title>
<link rel="stylesheet" href="../app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="../app/views/layouts/admin-sidebar/styles.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>.sidebar{position:fixed;top:0;left:0;width:260px;height:100vh;overflow-y:auto}.main-content{margin-left:260px;padding:30px;background:#fff;min-height:100vh}</style>
</head><body>
<?php include(__DIR__ . '/../../layouts/admin-sidebar/sidebar.php'); ?>
<main class="main-content">
  <div class="management">
    <div class="management-header">
      <h2>Workshop Supervisors</h2>
      <div class="tools">
        <input class="search-input" placeholder="Search by code/name/email">
        <a class="add-btn" href="<?= BASE_URL ?>/admin/supervisors/create">+ Add New Supervisor</a>
      </div>
    </div>

    <table>
      <thead><tr>
        <th>Supervisor Code</th><th>Full Name</th><th>Email</th><th>Contact</th>
        <th>Status</th><th>Created</th><th>Actions</th>
      </tr></thead>
      <tbody>
      <?php foreach (($supervisors ?? []) as $row):
          $status = $row['status'] ?? 'active';
          $pill = $status==='inactive'?'status--inactive':'status--active';
          $name = trim(($row['first_name']??'').' '.($row['last_name']??''));
      ?>
        <tr data-status="<?= htmlspecialchars($status) ?>">
          <td><?= htmlspecialchars($row['supervisor_code']) ?></td>
          <td><?= htmlspecialchars($name) ?></td>
          <td><?= htmlspecialchars($row['email'] ?? '') ?></td>
          <td><?= htmlspecialchars($row['phone'] ?? '') ?></td>
          <td class="<?= $pill ?>"><?= ucfirst($status) ?></td>
          <td><?= htmlspecialchars(substr($row['created_at'],0,10)) ?></td>
          <td>
           <a class="icon-btn" title="View" href="<?= BASE_URL ?>/admin/supervisors/<?= urlencode($row['supervisor_code']) ?>"><i class="fas fa-eye"></i></a>
<a class="icon-btn" title="Edit" href="<?= BASE_URL ?>/admin/supervisors/<?= urlencode($row['supervisor_code']) ?>/edit"><i class="fas fa-pen"></i></a>
<form action="<?= BASE_URL ?>/admin/supervisors/<?= urlencode($row['supervisor_code']) ?>/delete" method="post" style="display:inline" onsubmit="return confirm('Delete this supervisor?')">
  <button class="icon-btn" title="Delete" type="submit"><i class="fas fa-trash"></i></button>
</form>

          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</main>
</body></html>
