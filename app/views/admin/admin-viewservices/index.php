<?php $current = 'services';
$base = rtrim(BASE_URL, '/'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Service & Package Management</title>

  <link rel="stylesheet" href="<?= $base ?>/app/views/layouts/admin-sidebar/styles.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

  <style>
    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      width: 260px;
      height: 100vh;
      overflow-y: auto
    }

    .main-content {
      margin-left: 260px;
      padding: 30px;
      background: #f8fafc;
      min-height: 100vh
    }

    .page-head {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 20px;
      margin-bottom: 20px
    }

    .page-head h1 {
      margin: 0;
      font-size: 28px
    }

    .muted {
      color: #64748b
    }

    .btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 10px 14px;
      border-radius: 10px;
      text-decoration: none;
      border: 0;
      cursor: pointer
    }

    .btn-primary {
      background: #dc2626;
      color: #fff
    }

    .btn-primary:hover {
      background: #ef4444
    }

    .section-card {
      background: #fff;
      border: 1px solid #e2e8f0;
      border-radius: 16px;
      padding: 20px;
      margin-bottom: 22px
    }

    .section-card h2 {
      margin: 0 0 6px
    }

    .tabs {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
      margin-top: 14px
    }

    .tab {
      border: 1px solid #cbd5e1;
      background: #fff;
      border-radius: 999px;
      padding: 8px 14px;
      cursor: pointer
    }

    .tab.active {
      background: #111827;
      color: #fff;
      border-color: #111827
    }

    .table-wrap {
      overflow: auto;
      border: 1px solid #e2e8f0;
      border-radius: 14px;
      margin-top: 16px
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: #fff
    }

    th,
    td {
      padding: 12px 14px;
      border-bottom: 1px solid #e2e8f0;
      text-align: left;
      vertical-align: top
    }

    th {
      background: #f8fafc;
      font-size: 14px
    }

    .badge {
      display: inline-block;
      padding: 4px 10px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 700
    }

    .badge-type {
      background: #eef2ff;
      color: #3730a3
    }

    .badge-active {
      background: #dcfce7;
      color: #166534
    }

    .badge-inactive {
      background: #fee2e2;
      color: #991b1b
    }

    .actions {
      display: flex;
      gap: 10px;
      align-items: center
    }

    .icon-link,
    .icon-btn {
      background: none;
      border: 0;
      cursor: pointer;
      color: #111827
    }

    .pkg-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
      gap: 16px;
      margin-top: 16px
    }

    .pkg-card {
      background: #fff;
      border: 1px solid #e2e8f0;
      border-radius: 16px;
      padding: 18px
    }

    .pkg-top {
      display: flex;
      justify-content: space-between;
      gap: 12px;
      align-items: flex-start
    }

    .pkg-top h3 {
      margin: 0
    }

    .pkg-meta {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 10px;
      margin: 14px 0
    }

    .metric {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 12px;
      padding: 10px
    }

    .metric .label {
      font-size: 12px;
      color: #64748b
    }

    .metric .value {
      font-weight: 700;
      margin-top: 3px
    }

    .pkg-items {
      margin: 12px 0 0;
      padding-left: 18px
    }

    .pkg-items li {
      margin-bottom: 6px
    }

    .small {
      font-size: 12px;
      color: #64748b
    }

    .empty {
      padding: 14px;
      color: #64748b
    }
  </style>
</head>

<body>
  <?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

  <main class="main-content">
    <div class="page-head">
      <div>
        <h1>Service & Package Management</h1>
        <p class="muted">Manage standalone services and bundled packages from one admin area.</p>
      </div>
      <div style="display: flex; gap: 10px;">
        <a class="btn btn-primary" href="<?= $base ?>/admin/services/create">
          <i class="fa-solid fa-plus"></i> Add Service
        </a>
        <a class="btn btn-primary" href="<?= $base ?>/admin/packages/create">
          <i class="fa-solid fa-plus"></i> Add Package
        </a>
      </div>
    </div>

    <section class="section-card">
      <h2>Services</h2>
      <p class="muted">Regular services excluding package-type entries.</p>

      <div class="tabs" id="serviceTabs">
        <button class="tab active" data-tab="all">All</button>
        <?php foreach (($tabs ?? []) as $t): ?>
          <?php if (!in_array(strtolower($t['type_name']), ['package', 'packages'], true)): ?>
            <button class="tab" data-tab="type-<?= (int) $t['type_id'] ?>">
              <?= htmlspecialchars($t['type_name']) ?>
            </button>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>

      <div class="table-wrap">
        <table id="serviceTable">
          <thead>
            <tr>
              <th>Code</th>
              <th>Name</th>
              <th>Category</th>
              <th>Duration</th>
              <th>Price</th>
              <th>Branches</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($services)): ?>
              <?php foreach ($services as $row): ?>
                <tr data-type="type-<?= (int) ($row['type_id'] ?? 0) ?>">
                  <td><?= htmlspecialchars($row['service_code']) ?></td>
                  <td>
                    <strong><?= htmlspecialchars($row['name']) ?></strong><br>
                    <span class="small"><?= htmlspecialchars($row['description'] ?? '') ?></span>
                  </td>
                  <td><span class="badge badge-type"><?= htmlspecialchars($row['type_name'] ?? 'Uncategorized') ?></span>
                  </td>
                  <td><?= (int) $row['base_duration_minutes'] ?> min</td>
                  <td>Rs. <?= number_format((float) $row['default_price'], 2) ?></td>
                  <td>
                    <?php if ((int) $row['branch_count'] > 0): ?>
                      <?= htmlspecialchars($row['branches']) ?><br>
                      <span class="small"><?= (int) $row['branch_count'] ?> branch(es)</span>
                    <?php else: ?>
                      <span class="small">Not assigned</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <span class="badge <?= $row['status'] === 'active' ? 'badge-active' : 'badge-inactive' ?>">
                      <?= htmlspecialchars($row['status']) ?>
                    </span>
                  </td>
                  <td class="actions">
                    <a class="icon-link" href="<?= $base ?>/admin/services/<?= (int) $row['service_id'] ?>/edit"
                      title="Edit">
                      <i class="fa-solid fa-pen"></i>
                    </a>
                    <form method="post" action="<?= $base ?>/admin/services/<?= (int) $row['service_id'] ?>/delete"
                      onsubmit="return confirm('Delete this service?');">
                      <button type="submit" class="icon-btn" title="Delete">
                        <i class="fa-solid fa-trash"></i>
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="8" class="empty">No services found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>

    <section class="section-card">
      <h2>Packages</h2>
      <p class="muted">Package-type services with included items and usage analytics.</p>

      <?php if (!empty($packages)): ?>
        <div class="pkg-grid">
          <?php foreach ($packages as $pkg): ?>
            <div class="pkg-card">
              <div class="pkg-top">
                <div>
                  <h3><?= htmlspecialchars($pkg['name']) ?></h3>
                  <div class="small"><?= htmlspecialchars($pkg['service_code']) ?> ·
                    <?= htmlspecialchars($pkg['type_name']) ?></div>
                </div>
                <span class="badge <?= $pkg['status'] === 'active' ? 'badge-active' : 'badge-inactive' ?>">
                  <?= htmlspecialchars($pkg['status']) ?>
                </span>
              </div>

              <p class="small" style="margin-top:10px;"><?= htmlspecialchars($pkg['description'] ?? '') ?></p>

              <div class="pkg-meta">
                <div class="metric">
                  <div class="label">Package price</div>
                  <div class="value">Rs. <?= number_format((float) $pkg['default_price'], 2) ?></div>
                </div>
                <div class="metric">
                  <div class="label">Duration</div>
                  <div class="value"><?= (int) $pkg['base_duration_minutes'] ?> min</div>
                </div>
                <div class="metric">
                  <div class="label">Base item total</div>
                  <div class="value">Rs. <?= number_format((float) $pkg['package_base_total'], 2) ?></div>
                </div>
                <div class="metric">
                  <div class="label">Items</div>
                  <div class="value"><?= (int) $pkg['package_item_count'] ?></div>
                </div>
                <div class="metric">
                  <div class="label">Usage count</div>
                  <div class="value"><?= (int) $pkg['usage_count'] ?></div>
                </div>
                <div class="metric">
                  <div class="label">Estimated revenue</div>
                  <div class="value">Rs. <?= number_format((float) $pkg['estimated_revenue'], 2) ?></div>
                </div>
              </div>

              <div class="small">
                <strong>Branches:</strong>
                <?= (int) $pkg['branch_count'] > 0 ? htmlspecialchars($pkg['branches']) : 'Not assigned' ?>
              </div>

              <div class="small" style="margin-top:8px;">
                <strong>Last booked:</strong>
                <?= !empty($pkg['last_booked_date']) ? htmlspecialchars($pkg['last_booked_date']) : 'Never' ?>
              </div>

              <ul class="pkg-items">
                <?php if (!empty($pkg['package_items'])): ?>
                  <?php foreach ($pkg['package_items'] as $item): ?>
                    <li>
                      <?= htmlspecialchars($item['name']) ?>
                      (<?= htmlspecialchars($item['service_code']) ?>)
                      × <?= (int) $item['quantity'] ?>
                    </li>
                  <?php endforeach; ?>
                <?php else: ?>
                  <li>No items attached.</li>
                <?php endif; ?>
              </ul>

              <div class="actions" style="margin-top:14px;">
                <a class="icon-link" href="<?= $base ?>/admin/packages/<?= (int) $pkg['service_id'] ?>/edit" title="Edit">
                  <i class="fa-solid fa-pen"></i> Edit
                </a>
                <form method="post" action="<?= $base ?>/admin/services/<?= (int) $pkg['service_id'] ?>/delete"
                  onsubmit="return confirm('Delete this package?');">
                  <button type="submit" class="icon-btn" title="Delete">
                    <i class="fa-solid fa-trash"></i> Delete
                  </button>
                </form>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="empty">No packages found.</div>
      <?php endif; ?>
    </section>
  </main>

  <script>
    const tabs = document.querySelectorAll('#serviceTabs .tab');
    const rows = document.querySelectorAll('#serviceTable tbody tr');

    tabs.forEach(tab => {
      tab.addEventListener('click', () => {
        tabs.forEach(t => t.classList.remove('active'));
        tab.classList.add('active');

        const key = tab.dataset.tab;
        rows.forEach(row => {
          if (key === 'all') {
            row.style.display = '';
            return;
          }
          row.style.display = row.dataset.type === key ? '' : 'none';
        });
      });
    });
  </script>
</body>

</html>