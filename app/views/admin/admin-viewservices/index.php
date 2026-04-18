<?php $current = 'services';
$base = rtrim(BASE_URL, '/'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Service & Package Management</title>

  <link rel="stylesheet" href="<?= $base ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="<?= $base ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/admin/services/style.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body>
  <?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

  <main class="main-content services-page">
    <div class="page-head">
      <div>
        <h1>Service & Package Management</h1>
        <p class="muted">Manage standalone services and bundled packages from one admin area.</p>
      </div>
      <div class="page-head-actions">
        <a class="btn btn-primary" href="<?= $base ?>/admin/services/create">
          <i class="fa-solid fa-plus"></i> Add Service
        </a>
        <a class="btn btn-primary" href="<?= $base ?>/admin/packages/create">
          <i class="fa-solid fa-plus"></i> Add Package
        </a>
      </div>
    </div>

    <section class="section-card">
      <div class="tabs main-tabs" id="mainTabs">
        <button class="tab active" data-main-tab="services">
          <i class="fa-solid fa-wrench"></i> Services
        </button>
        <button class="tab" data-main-tab="packages">
          <i class="fa-solid fa-box"></i> Packages
        </button>
      </div>

      <div id="servicesView" class="main-view">
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
                <th class="actions-col">Actions</th>
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
                    <td class="actions-col">
                      <div class="table-actions">
                        <a class="chip-btn chip-btn--edit"
                          href="<?= $base ?>/admin/services/<?= (int) $row['service_id'] ?>/edit" title="Edit">
                          <i class="fa-solid fa-pen"></i>
                        </a>
                        <form class="inline-form" method="post"
                          action="<?= $base ?>/admin/services/<?= (int) $row['service_id'] ?>/delete"
                          onsubmit="return confirm('Delete this service?');">
                          <button type="submit" class="chip-btn chip-btn--delete" title="Delete">
                            <i class="fa-solid fa-trash"></i>
                          </button>
                        </form>
                      </div>
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
      </div>

      <div id="packagesView" class="main-view is-hidden">
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
                      <?= htmlspecialchars($pkg['type_name']) ?>
                    </div>
                  </div>
                  <span class="badge <?= $pkg['status'] === 'active' ? 'badge-active' : 'badge-inactive' ?>">
                    <?= htmlspecialchars($pkg['status']) ?>
                  </span>
                </div>

                <p class="small pkg-desc"><?= htmlspecialchars($pkg['description'] ?? '') ?></p>

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

                <div class="small pkg-last-booked">
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

                <div class="actions pkg-actions">
                  <a class="chip-btn chip-btn--edit chip-btn--label"
                    href="<?= $base ?>/admin/packages/<?= (int) $pkg['service_id'] ?>/edit" title="Edit">
                    <i class="fa-solid fa-pen"></i>
                    <span>Edit</span>
                  </a>
                  <form class="inline-form" method="post"
                    action="<?= $base ?>/admin/services/<?= (int) $pkg['service_id'] ?>/delete"
                    onsubmit="return confirm('Delete this package?');">
                    <button type="submit" class="chip-btn chip-btn--delete chip-btn--label" title="Delete">
                      <i class="fa-solid fa-trash"></i>
                      <span>Delete</span>
                    </button>
                  </form>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="empty">No packages found.</div>
        <?php endif; ?>
      </div>
    </section>
  </main>

  <script>
    // Main tab switching between Services and Packages
    const mainTabs = document.querySelectorAll('#mainTabs .tab');
    const servicesView = document.getElementById('servicesView');
    const packagesView = document.getElementById('packagesView');

    mainTabs.forEach(tab => {
      tab.addEventListener('click', () => {
        mainTabs.forEach(t => t.classList.remove('active'));
        tab.classList.add('active');

        const view = tab.dataset.mainTab;
        if (view === 'services') {
          servicesView.style.display = 'block';
          packagesView.style.display = 'none';
        } else {
          servicesView.style.display = 'none';
          packagesView.style.display = 'block';
        }
      });
    });

    // Service category tabs
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