<?php
/** @var array  $rows */
/** @var string $q */
/** @var string $status */

$current = 'service-managers';

$rows = $rows ?? [];
$q = $q ?? '';
$status = $status ?? 'all';

$B = rtrim(BASE_URL, '/');
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Service Managers • AutoNexus Admin</title>

  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="<?= $B ?>/app/views/admin/admin-viewmanagers/service-managers.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body>

  <?php include(__DIR__ . '/../../layouts/admin-sidebar/sidebar.php'); ?>

  <main class="main-content">

    <section class="management">
      <header class="management-header">
        <div>
          <h2>Service Managers</h2>
          <p class="management-subtitle">Manage branch service managers and their access.</p>
        </div>

        <div class="tools">
          <a href="<?= $B ?>/admin/admin-viewstaff" class="add-btn back-btn">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Back to Staff Management</span>
          </a>

          <!-- Server-side search/filter form -->
          <form method="get" action="<?= htmlspecialchars($B . '/admin/service-managers', ENT_QUOTES, 'UTF-8') ?>"
            class="tools">

            <input type="text" class="search-input" name="q"
              placeholder="Search by manager code, name, username, email, branch…"
              value="<?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?>" />

            <select class="status-filter" name="status">
              <?php
              $opts = ['all' => 'All Status', 'active' => 'Active', 'inactive' => 'Inactive'];
              foreach ($opts as $val => $label):
                ?>
                <option value="<?= $val ?>" <?= ($status === $val ? 'selected' : '') ?>>
                  <?= htmlspecialchars($label) ?>
                </option>
              <?php endforeach; ?>
            </select>

            <button type="submit" class="apply-btn">Filter</button>

            <!-- Link to create page -->
            <a href="<?= htmlspecialchars($B . '/admin/service-managers/create', ENT_QUOTES, 'UTF-8') ?>"
              class="add-btn">
              <i class="fa-solid fa-user-plus"></i>
              <span>Add Manager</span>
            </a>
          </form>
        </div>
      </header>

      <div class="table-wrap" style="margin-top: 10px;">
        <table id="tbl">
          <thead>
            <tr>
              <th>Manager Code</th>
              <th>Name</th>
              <th>Username</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Branch</th>
              <th>Status</th>
              <th class="th-actions">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($rows)): ?>
              <?php foreach ($rows as $r):
                $rawStatus = strtolower($r['status'] ?? 'active');
                $statusClass = ($rawStatus === 'inactive') ? 'status--inactive' : 'status--active';
                ?>
                <tr>
                  <td><?= htmlspecialchars($r['manager_code'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                  </td>
                  <td><?= htmlspecialchars($r['username'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($r['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($r['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                  <td>
                    <?php
                    $branchCode = trim((string) ($r['branch_code'] ?? ''));
                    $branchName = trim((string) ($r['branch_name'] ?? ''));
                    $branchText = ($branchCode !== '' || $branchName !== '')
                      ? trim($branchCode . ' ' . ($branchName !== '' ? '(' . $branchName . ')' : ''))
                      : 'Not assigned';
                    ?>
                    <?= htmlspecialchars($branchText, ENT_QUOTES, 'UTF-8') ?>
                  </td>
                  <td>
                    <span class="status-pill <?= $statusClass ?>">
                      <span class="dot"></span>
                      <?= htmlspecialchars(ucfirst($rawStatus), ENT_QUOTES, 'UTF-8') ?>
                    </span>
                  </td>
                  <td class="table-actions">
                    <a class="chip-btn chip-btn--light" title="View"
                      href="<?= htmlspecialchars($B . '/admin/service-managers/' . urlencode((string) $r['manager_id']), ENT_QUOTES, 'UTF-8') ?>">
                      <i class="fas fa-eye"></i>
                      <span>View</span>
                    </a>

                    <a class="chip-btn chip-btn--dark" title="Edit"
                      href="<?= htmlspecialchars($B . '/admin/service-managers/' . urlencode((string) $r['manager_id']) . '/edit', ENT_QUOTES, 'UTF-8') ?>">
                      <i class="fas fa-pen"></i>
                      <span>Edit</span>
                    </a>

                    <form method="post"
                      action="<?= htmlspecialchars($B . '/admin/service-managers/' . urlencode((string) $r['manager_id']) . '/delete', ENT_QUOTES, 'UTF-8') ?>"
                      onsubmit="return confirm('Delete this manager? This cannot be undone.');" class="inline-form">
                      <button type="submit" class="chip-btn chip-btn--danger">
                        <i class="fas fa-trash"></i>
                        <span>Delete</span>
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="8" class="empty-row">
                  <i class="fa-regular fa-circle-question"></i>
                  <span>No service managers found.</span>
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