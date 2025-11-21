<?php $current = 'customers'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>Customers • AutoNexus Admin</title>

  <link rel="stylesheet" href="<?= BASE_URL ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php include(__DIR__ . '/../../layouts/admin-sidebar/sidebar.php'); ?>

<main class="main-content">

  <section class="management">
    <header class="management-header">
      <div>
        <h2>Customers</h2>
        <p class="management-subtitle">Manage all registered customers in the system.</p>
      </div>

      <div class="tools">
        <input
          type="text"
          class="search-input"
          id="searchInput"
          placeholder="Search by name or email…"
        >

        <select class="status-filter" id="statusFilter">
          <option value="all">All Status</option>
          <option value="active" selected>Active</option>
          <option value="inactive">Inactive</option>
          <option value="pending">Pending</option>
        </select>

        <a class="add-btn" href="<?= BASE_URL ?>/admin/customers/create">
          <i class="fa-solid fa-user-plus"></i>
          <span>Add Customer</span>
        </a>
      </div>
    </header>

    <div class="table-wrap">
      <table id="customersTable">
        <thead>
          <tr>
            <th>Customer Code</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Contact</th>
            <th>Status</th>
            <th>Created At</th>
            <th class="th-actions">Actions</th>
          </tr>
        </thead>

        <tbody>
        <?php foreach (($customers ?? []) as $row): ?>
          <?php
            $status = strtolower($row['status'] ?? 'active');
            $pill   = ($status === 'inactive')
                ? 'status--inactive'
                : (($status === 'pending')
                    ? 'status--pending'
                    : 'status--active');
          ?>
          <tr data-status="<?= htmlspecialchars($status) ?>">
            <td><?= htmlspecialchars($row['customer_code']) ?></td>

            <td><?= htmlspecialchars(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')) ?></td>

            <td><?= htmlspecialchars($row['email'] ?? '') ?></td>

            <td><?= htmlspecialchars($row['phone'] ?? '') ?></td>

            <td>
              <span class="status-pill <?= $pill ?>">
                <span class="dot"></span>
                <?= ucfirst($status) ?>
              </span>
            </td>

            <td><?= htmlspecialchars(substr($row['created_at'], 0, 10)) ?></td>

            <td class="table-actions">

              <a class="chip-btn chip-btn--light"
                 href="<?= BASE_URL ?>/admin/customers/<?= (int)$row['customer_id'] ?>">
                <i class="fa-solid fa-eye"></i>
                <span>View</span>
              </a>

              <a class="chip-btn chip-btn--dark"
                 href="<?= BASE_URL ?>/admin/customers/<?= (int)$row['customer_id'] ?>/edit">
                <i class="fa-solid fa-pen"></i>
                <span>Edit</span>
              </a>

              <?php if ($status !== 'inactive'): ?>
                  <form action="<?= BASE_URL ?>/admin/customers/<?= (int)$row['customer_id'] ?>/deactivate" method="post">
                      <button class="chip-btn chip-btn--danger">
                          <i class="fa-solid fa-user-slash"></i> Delete
                      </button>
                  </form>
              <?php else: ?>
                  <form action="<?= BASE_URL ?>/admin/customers/<?= (int)$row['customer_id'] ?>/activate" method="post">
                      <button class="chip-btn chip-btn--dark">
                          <i class="fa-solid fa-user-check"></i> Activate
                      </button>
                  </form>
              <?php endif; ?>

            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>

  </section>
</main>

<script>
  // Search filter
  const searchInput = document.getElementById("searchInput");
  const statusFilter = document.getElementById("statusFilter");
  const rows = document.querySelectorAll("#customersTable tbody tr");

  function applyFilters() {
    const search = searchInput.value.toLowerCase();
    const status = statusFilter.value;

    rows.forEach(row => {
      const text = row.innerText.toLowerCase();
      const rowStatus = row.dataset.status;

      const matchSearch = text.includes(search);
      const matchStatus = (status === "all") || (status === rowStatus);

      row.style.display = (matchSearch && matchStatus) ? "" : "none";
    });
  }

  searchInput.addEventListener("input", applyFilters);
  statusFilter.addEventListener("change", applyFilters);

  // Apply filter on page load
  window.addEventListener("DOMContentLoaded", () => {
      statusFilter.value = "active";
      applyFilters();
  });
</script>

</body>
</html>
