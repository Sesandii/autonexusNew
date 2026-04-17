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

$q        = htmlspecialchars($filters['q'] ?? '', ENT_QUOTES, 'UTF-8');
$from     = htmlspecialchars($filters['from'] ?? '', ENT_QUOTES, 'UTF-8');
$to       = htmlspecialchars($filters['to'] ?? '', ENT_QUOTES, 'UTF-8');
$branchId = (int)($filters['branch_id'] ?? 0);
$typeId   = (int)($filters['type_id'] ?? 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle) ?></title>

  <!-- <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin> -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;400;500;700;600&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    * { box-sizing: border-box; }

    body {
      margin: 0;
      font-family: 'Inter', Arial, sans-serif;
      background: #f3f4f6;
      color: #111827;
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
      margin-bottom: 22px;
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

    .flash {
      margin-bottom: 16px;
      padding: 14px 16px;
      border-radius: 14px;
      background: #ecfdf5;
      color: #166534;
      border: 1px solid #bbf7d0;
      font-size: 14px;
      font-weight: 600;
    }

    .filters-card {
      background: #fff;
      border-radius: 18px;
      box-shadow: 0 2px 10px rgba(15, 23, 42, 0.06);
      padding: 18px;
      margin-bottom: 20px;
    }

    .filters-form {
      display: grid;
      grid-template-columns: 1.4fr repeat(4, minmax(150px, 1fr)) auto;
      gap: 12px;
      align-items: end;
    }

    .field {
      display: flex;
      flex-direction: column;
      gap: 6px;
    }

    .field label {
      font-size: 12px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: .04em;
      color: #94a3b8;
    }

    .input,
    .select {
      width: 100%;
      padding: 12px 14px;
      border: 1px solid #d1d5db;
      border-radius: 12px;
      background: #fff;
      color: #111827;
      font-size: 14px;
      font-family: inherit;
      transition: border-color .2s ease, box-shadow .2s ease;
    }

    .input:focus,
    .select:focus {
      outline: none;
      border-color: #fb923c;
      box-shadow: 0 0 0 3px rgba(251, 146, 60, 0.16);
    }

    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      border-radius: 10px;
      border: 1px solid transparent;
      padding: 11px 16px;
      font-size: 14px;
      font-weight: 700;
      text-decoration: none;
      cursor: pointer;
      transition: all .2s ease;
      white-space: nowrap;
    }

    .btn-apply {
      background: #111827;
      color: #fff;
      border-color: #111827;
    }

    .btn-apply:hover {
      background: #020617;
      border-color: #020617;
    }

    .table-card {
      background: #fff;
      border-radius: 18px;
      overflow: hidden;
      box-shadow: 0 2px 10px rgba(15, 23, 42, 0.06);
    }

    .table-wrap {
      overflow-x: auto;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      min-width: 1120px;
    }

    thead th {
      background: #f8fafc;
      color: #334155;
      font-size: 14px;
      font-weight: 700;
      text-align: left;
      padding: 18px 18px;
      border-bottom: 1px solid #e5e7eb;
      white-space: nowrap;
    }

    tbody td {
      padding: 16px 18px;
      font-size: 14px;
      color: #0f172a;
      border-bottom: 1px solid #eef2f7;
      vertical-align: middle;
    }

    tbody tr:last-child td {
      border-bottom: none;
    }

    .service-name {
      font-weight: 700;
      color: #0f172a;
      margin-bottom: 3px;
    }

    .service-meta {
      font-size: 12px;
      color: #64748b;
    }

    .status-pill {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 6px 12px;
      border-radius: 999px;
      font-size: 13px;
      font-weight: 700;
      background: #fef3c7;
      border: 1px solid #fde68a;
      color: #92400e;
      white-space: nowrap;
    }

    .status-pill::before {
      content: "";
      width: 9px;
      height: 9px;
      border-radius: 50%;
      background: #f59e0b;
      display: inline-block;
    }

    .table-actions {
      display: flex;
      align-items: center;
      gap: 8px;
      flex-wrap: wrap;
    }

    .chip-btn {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 8px 12px;
      border-radius: 999px;
      text-decoration: none;
      font-size: 13px;
      font-weight: 700;
      border: 1px solid transparent;
      transition: all .18s ease;
      cursor: pointer;
      font-family: inherit;
    }

    .chip-btn--view {
      background: #f3f4f6;
      border-color: #e5e7eb;
      color: #111827;
    }

    .chip-btn--view:hover {
      background: #e5e7eb;
    }

    .chip-btn--approve {
      background: #166534;
      color: #fff;
    }

    .chip-btn--approve:hover {
      background: #14532d;
    }

    .chip-btn--reject {
      background: #b91c1c;
      color: #fff;
    }

    .chip-btn--reject:hover {
      background: #991b1b;
    }

    .chip-btn--view,
    .chip-btn--approve,
    .chip-btn--reject {
      border: none;
    }

    .empty-state {
      padding: 32px 20px;
      text-align: center;
      color: #6b7280;
      font-size: 14px;
      background: #fff;
      border-radius: 18px;
      box-shadow: 0 2px 10px rgba(15, 23, 42, 0.06);
    }

    .empty-state i {
      display: block;
      font-size: 36px;
      margin-bottom: 10px;
      opacity: .5;
    }

    @media (max-width: 1200px) {
      .filters-form {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
    }

    @media (max-width: 768px) {
      .main-content {
        margin-left: 0;
        padding: 16px;
      }

      .filters-form {
        grid-template-columns: 1fr;
      }

      .page-title-wrap h1 {
        font-size: 26px;
      }
    }
  </style>
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
        <input
          type="text"
          name="q"
          class="input"
          placeholder="Search by service / code / branch"
          value="<?= $q ?>"
        >
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
            <option value="<?= (int)$br['branch_id'] ?>" <?= $branchId === (int)$br['branch_id'] ? 'selected' : '' ?>>
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
            <option value="<?= (int)$st['type_id'] ?>" <?= $typeId === (int)$st['type_id'] ? 'selected' : '' ?>>
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
              <th style="width:220px;">Actions</th>
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
                <td><?= (int)$c['duration'] ?> min</td>
                <td><span class="status-pill">Pending</span></td>
                <td>
                  <div class="table-actions">
                    <form method="post" action="<?= $B ?>/admin/admin-serviceapproval/update" style="display:inline;">
                      <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                      <input type="hidden" name="action" value="approve">
                      <button type="submit" class="chip-btn chip-btn--approve">
                        <i class="fa-solid fa-check"></i>
                        <span>Approve</span>
                      </button>
                    </form>

                    <form method="post" action="<?= $B ?>/admin/admin-serviceapproval/update" style="display:inline;">
                      <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                      <input type="hidden" name="action" value="reject">
                      <button type="submit" class="chip-btn chip-btn--reject">
                        <i class="fa-solid fa-xmark"></i>
                        <span>Reject</span>
                      </button>
                    </form>

                    <a href="<?= $B ?>/admin/admin-serviceapproval/show?id=<?= (int)$c['id'] ?>" class="chip-btn chip-btn--view">
                      <i class="fa-regular fa-eye"></i>
                      <span>View</span>
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