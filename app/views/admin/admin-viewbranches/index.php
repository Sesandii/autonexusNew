<?php
/** @var array $branches */
/** @var string $base */
/** @var string $q */
/** @var string $status */

$current = 'branches';
$branches = $branches ?? [];
$base = rtrim($base ?? BASE_URL, '/');
$q = $q ?? '';
$status = $status ?? 'all';

function e($value): string
{
  return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
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
  <link rel="stylesheet" href="<?= $base ?>/app/views/admin/admin-viewbranches/branches.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            <input type="text" class="search-input" name="q" placeholder="Search branch..." value="<?= e($q) ?>" />
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
              <th class="u-text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($branches)): ?>
              <?php foreach ($branches as $b): ?>
                <?php
                $code = (string) ($b['branch_code'] ?? '');
                $rowStatus = strtolower((string) ($b['status'] ?? 'active'));
                $isInactive = $rowStatus === 'inactive';
                $statusClass = $isInactive ? 'inactive' : 'active';
                $statusLabel = $isInactive ? 'Inactive' : 'Active';

                $managerName = trim(
                  ((string) ($b['m_first'] ?? '')) . ' ' . ((string) ($b['m_last'] ?? ''))
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
                      <a href="<?= e($base . '/admin/branches/' . rawurlencode($code)) ?>" class="chip-btn chip-btn--view"
                        title="View">
                        <i class="fa-solid fa-eye"></i>
                        <span>View</span>
                      </a>

                      <a href="<?= e($base . '/admin/branches/' . rawurlencode($code) . '/edit') ?>"
                        class="chip-btn chip-btn--edit" title="Edit">
                        <i class="fa-solid fa-pen"></i>
                        <span>Edit</span>
                      </a>

                      <form method="post" action="<?= e($base . '/admin/branches/' . rawurlencode($code) . '/delete') ?>"
                        class="inline-form" onsubmit="return confirm('Delete this branch?');">
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