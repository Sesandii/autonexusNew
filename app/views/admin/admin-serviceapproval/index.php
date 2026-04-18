<?php
/** @var array  $cards */
/** @var array  $branches */
/** @var array  $serviceTypes */
/** @var array  $filters */
/** @var string $pageTitle */
/** @var string $current */
/** @var string|null $message */

$current = $current ?? 'approval';
$B = rtrim(BASE_URL, '/');

$q = htmlspecialchars($filters['q'] ?? '', ENT_QUOTES, 'UTF-8');
$from = htmlspecialchars($filters['from'] ?? '', ENT_QUOTES, 'UTF-8');
$to = htmlspecialchars($filters['to'] ?? '', ENT_QUOTES, 'UTF-8');
$branchId = (int) ($filters['branch_id'] ?? 0);
$typeId = (int) ($filters['type_id'] ?? 0);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle) ?></title>

  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="<?= $B ?>/app/views/admin/admin-serviceapproval/service-approval.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
  <?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

  <main class="main-content">
    <header class="page-header">
      <div class="page-title-wrap">
        <h1>Service Approval Queue</h1>
        <p>Review pending services before they become available in the system</p>
      </div>
    </header>

    <?php if (!empty($message)): ?>
      <div class="flash">
        <?= htmlspecialchars($message) ?>
      </div>
    <?php endif; ?>

    <section class="filters-card">
      <form class="filters-form" method="get" action="<?= $B ?>/admin/admin-serviceapproval">
        <div class="field">
          <label>Search</label>
          <input type="text" name="q" class="input" placeholder="Search by service / code / branch" value="<?= $q ?>">
        </div>

        <div class="field">
          <label>From</label>
          <input type="date" name="from" class="input" value="<?= $from ?>">
        </div>

        <div class="field">
          <label>To</label>
          <input type="date" name="to" class="input" value="<?= $to ?>">
        </div>

        <div class="field">
          <label>Branch</label>
          <select name="branch_id" class="select">
            <option value="">All Branches</option>
            <?php foreach ($branches as $br): ?>
              <option value="<?= (int) $br['branch_id'] ?>" <?= $branchId === (int) $br['branch_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($br['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="field">
          <label>Service Type</label>
          <select name="type_id" class="select">
            <option value="">All Service Types</option>
            <?php foreach ($serviceTypes as $st): ?>
              <option value="<?= (int) $st['type_id'] ?>" <?= $typeId === (int) $st['type_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($st['type_name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <button type="submit" class="btn btn-apply">
          <i class="fa-solid fa-magnifying-glass"></i>
          <span>Apply</span>
        </button>
      </form>
    </section>

    <?php if (empty($cards)): ?>
      <div class="empty-state">
        <i class="fa-regular fa-folder-open"></i>
        No pending services found for the selected filters.
      </div>
    <?php else: ?>
      <section class="table-card">
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Service</th>
                <th>Type</th>
                <th>Branches</th>
                <th>Submitted By</th>
                <!-- <th>Created At</th> -->
                <th>Price</th>
                <th>Duration</th>
                <th>Status</th>
                <th class="u-width-actions">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($cards as $c): ?>
                <?php $createdFmt = (new DateTime($c['created_at']))->format('Y-m-d H:i'); ?>
                <tr>
                  <td>
                    <div class="service-name"><?= htmlspecialchars($c['name']) ?></div>
                    <div class="service-meta">Code: <?= htmlspecialchars($c['code']) ?></div>
                  </td>
                  <td><?= htmlspecialchars($c['type']) ?></td>
                  <td><?= htmlspecialchars($c['branches']) ?></td>
                  <td><?= htmlspecialchars($c['submitted_by']) ?></td>
                  <!-- <td><?= htmlspecialchars($createdFmt) ?></td> -->
                  <td><?= number_format($c['price'], 2) ?></td>
                  <td><?= (int) $c['duration'] ?> min</td>
                  <td><span class="status-pill">Pending</span></td>
                  <td>
                    <div class="table-actions">
                      <form method="post" action="<?= $B ?>/admin/admin-serviceapproval/update" class="u-inline">
                        <input type="hidden" name="id" value="<?= (int) $c['id'] ?>">
                        <input type="hidden" name="action" value="approve">
                        <button type="submit" class="chip-btn chip-btn--approve">
                          <i class="fa-solid fa-check"></i>

                        </button>
                      </form>

                      <form method="post" action="<?= $B ?>/admin/admin-serviceapproval/update" class="u-inline">
                        <input type="hidden" name="id" value="<?= (int) $c['id'] ?>">
                        <input type="hidden" name="action" value="reject">
                        <button type="submit" class="chip-btn chip-btn--reject">
                          <i class="fa-solid fa-xmark"></i>

                        </button>
                      </form>

                      <a href="<?= $B ?>/admin/admin-serviceapproval/show?id=<?= (int) $c['id'] ?>"
                        class="chip-btn chip-btn--view">
                        <i class="fa-regular fa-eye"></i>

                      </a>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </section>
    <?php endif; ?>
  </main>
</body>

</html>