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

$q        = htmlspecialchars($filters['q']         ?? '', ENT_QUOTES, 'UTF-8');
$from     = htmlspecialchars($filters['from']      ?? '', ENT_QUOTES, 'UTF-8');
$to       = htmlspecialchars($filters['to']        ?? '', ENT_QUOTES, 'UTF-8');
$branchId = (int)($filters['branch_id']           ?? 0);
$typeId   = (int)($filters['type_id']             ?? 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle) ?></title>

  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    .main-content{margin-left:260px;padding:30px;background:#f4f5f7;min-height:100vh;}
    .filters-form{display:flex;flex-wrap:wrap;gap:12px;margin-bottom:20px;align-items:center;}
    .filters-form input,.filters-form select{padding:8px 10px;border-radius:8px;border:1px solid #d1d5db;font-size:14px;}
    .filters-form .search{min-width:220px;}
    .filters-form label{font-size:13px;color:#4b5563;display:flex;flex-direction:column;gap:4px;}
    table{width:100%;border-collapse:collapse;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 1px 4px rgba(15,23,42,.08);}
    th,td{padding:10px 12px;font-size:13px;border-bottom:1px solid #e5e7eb;text-align:left;}
    th{background:#f9fafb;font-weight:600;color:#4b5563;}
    .status--pending{color:#b45309;background:#fffbeb;padding:2px 8px;border-radius:999px;font-size:11px;display:inline-block;}
    .btn{border:none;border-radius:8px;padding:5px 10px;font-size:12px;cursor:pointer;display:inline-flex;align-items:center;gap:4px;}
    .btn-view{background:#111827;color:#fff;}
    .btn-approve{background:#16a34a;color:#fff;margin-right:4px;}
    .btn-reject{background:#b91c1c;color:#fff;}
    .flash{margin-bottom:16px;padding:10px 12px;border-radius:8px;font-size:13px;background:#ecfdf5;color:#166534;}
  </style>
</head>
<body>
<?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

<main class="main-content">
  <div class="management-header">
    <h2>Service Approval Queue</h2>
  </div>

  <?php if (!empty($message)): ?>
    <div class="flash">
      <?= htmlspecialchars($message) ?>
    </div>
  <?php endif; ?>

  <form class="filters-form" method="get" action="<?= $B ?>/admin/admin-serviceapproval">
    <input
      type="text"
      name="q"
      class="search"
      placeholder="Search by service / code / branch"
      value="<?= $q ?>"
    >

    <label>
      From
      <input type="date" name="from" value="<?= $from ?>">
    </label>

    <label>
      To
      <input type="date" name="to" value="<?= $to ?>">
    </label>

    <select name="branch_id">
      <option value="">All Branches</option>
      <?php foreach ($branches as $br): ?>
        <option value="<?= (int)$br['branch_id'] ?>" <?= $branchId === (int)$br['branch_id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($br['name']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <select name="type_id">
      <option value="">All Service Types</option>
      <?php foreach ($serviceTypes as $st): ?>
        <option value="<?= (int)$st['type_id'] ?>" <?= $typeId === (int)$st['type_id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($st['type_name']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <button type="submit" class="btn btn-view">
      <i class="fa-solid fa-magnifying-glass"></i> Apply
    </button>
  </form>

  <?php if (empty($cards)): ?>
    <p style="margin-top:10px;font-size:14px;color:#6b7280;">
      No pending services found for the selected filters.
    </p>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>Service</th>
          <th>Type</th>
          <th>Branches</th>
          <th>Submitted By</th>
          <th>Created At</th>
          <th>Price</th>
          <th>Duration</th>
          <th>Status</th>
          <th style="width:160px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($cards as $c): ?>
          <?php
            $createdFmt = (new DateTime($c['created_at']))->format('Y-m-d H:i');
          ?>
          <tr>
            <td>
              <strong><?= htmlspecialchars($c['name']) ?></strong><br>
              <span style="font-size:11px;color:#6b7280;">Code: <?= htmlspecialchars($c['code']) ?></span>
            </td>
            <td><?= htmlspecialchars($c['type']) ?></td>
            <td><?= htmlspecialchars($c['branches']) ?></td>
            <td><?= htmlspecialchars($c['submitted_by']) ?></td>
            <td><?= htmlspecialchars($createdFmt) ?></td>
            <td><?= number_format($c['price'], 2) ?></td>
            <td><?= (int)$c['duration'] ?> min</td>
            <td><span class="status--pending">Pending</span></td>
            <td>
              <form method="post" action="<?= $B ?>/admin/admin-serviceapproval/update" style="display:inline;">
                <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                <input type="hidden" name="action" value="approve">
                <button type="submit" class="btn btn-approve">
                  <i class="fa-solid fa-check"></i> Approve
                </button>
              </form>

              <form method="post" action="<?= $B ?>/admin/admin-serviceapproval/update" style="display:inline;">
                <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                <input type="hidden" name="action" value="reject">
                <button type="submit" class="btn btn-reject">
                  <i class="fa-solid fa-xmark"></i> Reject
                </button>
              </form>

              <a href="<?= $B ?>/admin/admin-serviceapproval/show?id=<?= (int)$c['id'] ?>" class="btn btn-view" style="margin-top:4px;display:inline-flex;">
                <i class="fa-regular fa-eye"></i> View
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</main>
</body>
</html>
