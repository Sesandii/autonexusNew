<?php
/** @var array $branches */
/** @var string $base */
/** @var string $q */
/** @var string $status */

$current  = 'branches';
$branches = $branches ?? [];
$base     = rtrim($base ?? BASE_URL, '/');
$q        = $q ?? '';
$status   = $status ?? 'all';

function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Branches Management</title>

  <link rel="stylesheet" href="<?= $base ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
      background: #f3f4f6;
      color: #111827;
    }

    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      width: 260px;
      height: 100vh;
      overflow-y: auto;
    }

    .main-content {
      margin-left: 260px;
      min-height: 100vh;
      padding: 28px;
      background: #f3f4f6;
    }

    .page-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 16px;
      flex-wrap: wrap;
      margin-bottom: 24px;
    }

    .page-title-wrap h1 {
      margin: 0;
      font-size: 32px;
      line-height: 1.15;
      font-weight: 800;
      color: #0f172a;
    }

    .page-title-wrap p {
      margin: 8px 0 0;
      font-size: 15px;
      color: #64748b;
    }

    .toolbar {
      display: flex;
      align-items: center;
      gap: 12px;
      flex-wrap: wrap;
    }

    .toolbar form {
      display: flex;
      align-items: center;
      gap: 12px;
      flex-wrap: wrap;
      margin: 0;
    }

    .search-wrap {
      position: relative;
    }

    .search-input {
      width: 290px;
      height: 46px;
      border: 1px solid #d1d5db;
      border-radius: 999px;
      padding: 0 18px;
      font-size: 15px;
      background: #fff;
      color: #111827;
      outline: none;
      transition: border-color .2s ease, box-shadow .2s ease;
    }

    .search-input::placeholder {
      color: #94a3b8;
    }

    .search-input:focus {
      border-color: #fb923c;
      box-shadow: 0 0 0 3px rgba(251, 146, 60, 0.16);
    }

    .status-filter {
      height: 46px;
      border: 1px solid #d1d5db;
      border-radius: 12px;
      padding: 0 14px;
      font-size: 14px;
      background: #fff;
      color: #111827;
      outline: none;
    }

    .filter-btn {
      height: 46px;
      padding: 0 16px;
      border: 1px solid #d1d5db;
      border-radius: 12px;
      background: #fff;
      color: #374151;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      transition: all .2s ease;
    }

    .filter-btn:hover {
      background: #f9fafb;
      border-color: #9ca3af;
    }

    .add-btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      height: 48px;
      padding: 0 18px;
      border-radius: 999px;
      background: #d94801;
      color: #fff;
      text-decoration: none;
      font-size: 15px;
      font-weight: 700;
      transition: all .2s ease;
      white-space: nowrap;
    }

    .add-btn:hover {
      background: #c2410c;
    }

    .table-card {
      background: #fff;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 2px 10px rgba(15, 23, 42, 0.06);
    }

    .table-wrap {
      overflow-x: auto;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      min-width: 980px;
    }

    thead th {
      background: #f8fafc;
      color: #334155;
      font-size: 14px;
      font-weight: 700;
      text-align: left;
      padding: 18px 20px;
      border-bottom: 1px solid #e5e7eb;
      white-space: nowrap;
    }

    tbody td {
      padding: 14px 20px;
      font-size: 14px;
      color: #0f172a;
      border-bottom: 1px solid #eef2f7;
      vertical-align: middle;
      white-space: nowrap;
    }

    tbody tr:last-child td {
      border-bottom: none;
    }

    .status-pill {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 6px 14px;
      border-radius: 999px;
      font-size: 13px;
      font-weight: 700;
      border: 1px solid transparent;
      white-space: nowrap;
    }

    .status-pill .dot {
      width: 9px;
      height: 9px;
      border-radius: 50%;
      flex: 0 0 9px;
    }

    .status-pill.active {
      background: #dcfce7;
      border-color: #bbf7d0;
      color: #166534;
    }

    .status-pill.active .dot {
      background: #22c55e;
    }

    .status-pill.inactive {
      background: #fee2e2;
      border-color: #fecaca;
      color: #991b1b;
    }

    .status-pill.inactive .dot {
      background: #dc2626;
    }

    .table-actions {
      display: flex;
      justify-content: flex-end;
      align-items: center;
      gap: 8px;
      flex-wrap: nowrap;
      white-space: nowrap;
    }

    .chip-btn {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 7px 12px;
      border-radius: 999px;
      text-decoration: none;
      font-size: 13px;
      font-weight: 700;
      border: 1px solid transparent;
      transition: all .18s ease;
      cursor: pointer;
    }

    .chip-btn i {
      font-size: 13px;
    }

    .chip-btn--view {
      background: #f3f4f6;
      border-color: #e5e7eb;
      color: #111827;
    }

    .chip-btn--view:hover {
      background: #e5e7eb;
    }

    .chip-btn--edit {
      background: #0f172a;
      color: #fff;
    }

    .chip-btn--edit:hover {
      background: #020617;
    }

    .chip-btn--delete {
      background: #fee2e2;
      border-color: #fecaca;
      color: #b91c1c;
    }

    .chip-btn--delete:hover {
      background: #fecaca;
    }

    .inline-form {
      display: inline;
      margin: 0;
    }

    button.chip-btn {
      border: none;
      font-family: inherit;
    }

    .empty-row {
      text-align: center;
      color: #6b7280;
      padding: 28px 20px !important;
      font-size: 14px;
    }

    @media (max-width: 1100px) {
      .main-content {
        padding: 20px;
      }

      .search-input {
        width: 240px;
      }
    }

    @media (max-width: 768px) {
      .main-content {
        margin-left: 0;
        padding: 16px;
      }

      .sidebar {
        position: static;
        width: 100%;
        height: auto;
      }

      .page-title-wrap h1 {
        font-size: 26px;
      }

      .toolbar,
      .toolbar form {
        width: 100%;
      }

      .search-wrap,
      .search-input,
      .status-filter,
      .filter-btn,
      .add-btn {
        width: 100%;
      }
    }
  </style>
</head>
<body>
  <?php include(__DIR__ . '/../../layouts/admin-sidebar/sidebar.php'); ?>

  <main class="main-content">
    <header class="page-header">
      <div class="page-title-wrap">
        <h1>Branches Management</h1>
        <p>Monitor and manage service branches</p>
      </div>

      <div class="toolbar">
        <form method="get" action="<?= e($base . '/admin/branches') ?>">
          <div class="search-wrap">
            <input
              type="text"
              class="search-input"
              name="q"
              placeholder="Search branch..."
              value="<?= e($q) ?>"
            />
          </div>

          <select class="status-filter" name="status">
            <option value="all" <?= $status === 'all' ? 'selected' : '' ?>>All Status</option>
            <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
            <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
          </select>

          <button type="submit" class="filter-btn">Filter</button>

          <a href="<?= e($base . '/admin/branches/create') ?>" class="add-btn">
            <i class="fa-solid fa-building-circle-arrow-right"></i>
            <span>Add Branch</span>
          </a>
        </form>
      </div>
    </header>

    <section class="table-card">
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Branch Code</th>
              <th>Branch Name</th>
              <th>City</th>
              <th>Manager Name</th>
              <th>Contact</th>
              <th>Status</th>
              <th style="text-align:right;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($branches)): ?>
              <?php foreach ($branches as $b): ?>
                <?php
                  $code = (string)($b['branch_code'] ?? '');
                  $rowStatus = strtolower((string)($b['status'] ?? 'active'));
                  $isInactive = $rowStatus === 'inactive';
                  $statusClass = $isInactive ? 'inactive' : 'active';
                  $statusLabel = $isInactive ? 'Inactive' : 'Active';

                  $managerName = trim(
                    ((string)($b['m_first'] ?? '')) . ' ' . ((string)($b['m_last'] ?? ''))
                  );
                  $managerName = $managerName !== '' ? $managerName : 'Not assigned';
                ?>
                <tr>
                  <td><?= e($code) ?></td>
                  <td><?= e($b['name'] ?? '') ?></td>
                  <td><?= e($b['city'] ?? '') ?></td>
                  <td><?= e($managerName) ?></td>
                  <td><?= e($b['phone'] ?? '') ?></td>
                  <td>
                    <span class="status-pill <?= e($statusClass) ?>">
                      <span class="dot"></span>
                      <?= e($statusLabel) ?>
                    </span>
                  </td>
                  <td>
                    <div class="table-actions">
                      <a
                        href="<?= e($base . '/admin/branches/' . rawurlencode($code)) ?>"
                        class="chip-btn chip-btn--view"
                        title="View"
                      >
                        <i class="fa-solid fa-eye"></i>
                        <span>View</span>
                      </a>

                      <a
                        href="<?= e($base . '/admin/branches/' . rawurlencode($code) . '/edit') ?>"
                        class="chip-btn chip-btn--edit"
                        title="Edit"
                      >
                        <i class="fa-solid fa-pen"></i>
                        <span>Edit</span>
                      </a>

                      <form
                        method="post"
                        action="<?= e($base . '/admin/branches/' . rawurlencode($code) . '/delete') ?>"
                        class="inline-form"
                        onsubmit="return confirm('Delete this branch?');"
                      >
                        <button type="submit" class="chip-btn chip-btn--delete" title="Delete">
                          <i class="fa-solid fa-trash"></i>
                          <span>Delete</span>
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" class="empty-row">No branches found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>
</body>
</html>