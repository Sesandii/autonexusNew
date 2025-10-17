<?php $current = 'customers'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>Admin Panel - Customers</title>
    <link rel="stylesheet" href="../app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="../app/views/layouts/admin-sidebar/styles.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>.sidebar{position:fixed;top:0;left:0;width:260px;height:100vh;overflow-y:auto}.main-content{margin-left:260px;padding:30px;background:#fff;min-height:100vh}</style>
</head>
<body>
<?php include(__DIR__ . '/../../layouts/admin-sidebar/sidebar.php'); ?>

<main class="main-content">
  <div class="management">
    <div class="management-header">
      <h2>Customers Management</h2>
      <div class="tools">
        <input type="text" class="search-input" id="searchInput" placeholder="Search by name/email...">
        <select class="status-filter" id="statusFilter">
          <option value="all">All Status</option>
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
          <option value="pending">Pending</option>
        </select>
        <a class="add-btn" href="<?= BASE_URL ?>/admin/customers/create">+ Add New Customer</a>
      </div>
    </div>

    <table id="customersTable">
      <thead>
        <tr>
          <th>Customer Code</th>
          <th>Full Name</th>
          <th>Email</th>
          <th>Contact</th>
          <th>Status</th>
          <th>Created At</th>
          <th>Actions</th>
        </tr>
      </thead>

      <tbody>
      <?php foreach (($customers ?? []) as $row):
        $status = $row['status'] ?? 'active';
        $pill   = $status === 'inactive' ? 'status--inactive'
                : ($status === 'pending' ? 'status--pending' : 'status--active');
      ?>
        <tr data-status="<?= htmlspecialchars($status) ?>">
          <td><?= htmlspecialchars($row['customer_code']) ?></td>
          <td><?= htmlspecialchars(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')) ?></td>
          <td><?= htmlspecialchars($row['email'] ?? '') ?></td>
          <td><?= htmlspecialchars($row['phone'] ?? '') ?></td>
          <td class="<?= $pill ?>"><?= ucfirst($status) ?></td>
          <td><?= htmlspecialchars(substr($row['created_at'], 0, 10)) ?></td>
          <td>
            <a class="icon-btn" title="View" href="<?= BASE_URL ?>/admin/customers/<?= (int)$row['customer_id'] ?>"><i class="fas fa-eye"></i></a>
            <a class="icon-btn" title="Edit" href="<?= BASE_URL ?>/admin/customers/<?= (int)$row['customer_id'] ?>/edit"><i class="fas fa-pen"></i></a>
            <form action="<?= BASE_URL ?>/admin/customers/<?= (int)$row['customer_id'] ?>/delete" method="post" style="display:inline" onsubmit="return confirm('Delete this customer?')">
              <button class="icon-btn" title="Delete" type="submit"><i class="fas fa-trash"></i></button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</main>
<!-- (rest of script unchanged) -->
</body>
</html>
