<?php
/** @var array  $appointments */
/** @var array  $branches */
/** @var array  $services */
/** @var string $selectedDate */
/** @var string $pageTitle */
/** @var string $current */
$current = $current ?? 'appointments';
$B = rtrim(BASE_URL, '/');
$selectedDate = $selectedDate ?? date('Y-m-d');
$dateFrom = $dateFrom ?? '';
$dateTo = $dateTo ?? '';
$displayDate = new DateTime($selectedDate);
$adminName = trim((($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''))) ?: 'Admin User';

function e($value)
{
  return htmlspecialchars((string) $value);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($pageTitle ?? 'Appointments') ?></title>
  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="<?= $B ?>/public/assets/css/admin/appointments/index.css">
  <link rel="stylesheet" href="<?= $B ?>/public/assets/css/admin-dashboard.css?v=4">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
  <?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>
  <main class="main-content">
    <header class="topbar">
      <div>
        <h1 class="page-title">Appointments</h1>
        <p class="subtitle">Manage and track service appointments</p>
      </div>

      <a class="user-chip user-chip--link" href="<?= $B ?>/admin/profile" aria-label="Open profile">
        <div class="avatar"><i class="fa-solid fa-user"></i></div>
        <span><?= e($adminName) ?></span>
      </a>
    </header>

    <section class="dash-wrap">
      <!-- Statistics -->
      <div class="stat-grid">
        <div class="stat-card">
          <div class="stat-label">Total</div>
          <div class="stat-value"><?= count($appointments) ?></div>
        </div>
        <?php
        $scheduled = count(array_filter($appointments, fn($a) => $a['db_status'] === 'requested'));
        $confirmed = count(array_filter($appointments, fn($a) => $a['db_status'] === 'confirmed'));
        $inProgress = count(array_filter($appointments, fn($a) => $a['db_status'] === 'in_progress'));
        ?>
        <div class="stat-card">
          <div class="stat-label">Scheduled</div>
          <div class="stat-value"><?= $scheduled ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Confirmed</div>
          <div class="stat-value"><?= $confirmed ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-label">In Progress</div>
          <div class="stat-value"><?= $inProgress ?></div>
        </div>
      </div>

      <!-- Filters -->
      <section class="filter-section">
        <div class="u-flex-between-center u-mb-16">
          <h3 class="filter-title"><i class="fa-solid fa-filter"></i> Search & Filter</h3>
          <div class="date-navigation u-flex-center-gap-8">
            <button class="nav-btn" onclick="goToPreviousDay()" title="Previous day">
              <i class="fa-solid fa-chevron-left"></i> Previous
            </button>
            <div class="current-date-display u-minw-140 u-text-center u-font-semibold u-text-gray-700">
              <?= $displayDate->format('M d, Y') ?>
            </div>
            <button class="nav-btn" onclick="goToNextDay()" title="Next day">
              Next <i class="fa-solid fa-chevron-right"></i>
            </button>
            <button class="nav-btn secondary" onclick="goToToday()" title="Go to today">
              <i class="fa-solid fa-calendar-days"></i> Today
            </button>
          </div>
        </div>
        <div class="filter-grid">
          <div class="filter-input">
            <label for="customerSearch">Customer Name</label>
            <input type="text" id="customerSearch" placeholder="Search..." />
          </div>
          <div class="filter-input">
            <label for="serviceFilter">Service</label>
            <select id="serviceFilter">
              <option value="">All Services</option>
              <?php foreach ($services as $s): ?>
                <option value="<?= (int) $s['service_id'] ?>">
                  <?= e($s['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="filter-input">
            <label for="branchFilter">Branch</label>
            <select id="branchFilter">
              <option value="">All Branches</option>
              <?php foreach ($branches as $b): ?>
                <option value="<?= (int) $b['branch_id'] ?>">
                  <?= e($b['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="filter-input">
            <label for="statusFilter">Status</label>
            <select id="statusFilter">
              <option value="">All Status</option>
              <option value="Scheduled">Scheduled</option>
              <option value="Confirmed">Confirmed</option>
              <option value="In Progress">In Progress</option>
              <option value="Completed">Completed</option>
              <option value="Cancelled">Cancelled</option>
            </select>
          </div>
          <div class="filter-input">
            <label for="dateFrom">From Date</label>
            <input type="date" id="dateFrom" value="<?= e($dateFrom) ?>" />
          </div>
          <div class="filter-input">
            <label for="dateTo">To Date</label>
            <input type="date" id="dateTo" value="<?= e($dateTo) ?>" />
          </div>
        </div>
        <div class="filter-actions u-mt-12">
          <button class="filter-btn" onclick="applyFilters()">
            <i class="fa-solid fa-search"></i> Apply Filters
          </button>
          <button class="filter-btn secondary" onclick="clearFilters()">
            <i class="fa-solid fa-xmark"></i> Clear
          </button>
        </div>
      </section>

      <!-- Appointments Table -->
      <section class="panel">
        <?php if (!empty($appointments)): ?>
          <div class="table-wrap">
            <div class="table-wrap">
              <table class="table" id="appointmentsTable">
                <thead>
                  <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Customer</th>
                    <th>Service</th>
                    <th>Branch</th>
                    <th>Supervisor</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($appointments as $a):
                    $time = DateTime::createFromFormat('H:i:s', $a['time']);
                    $timeFormatted = $time ? $time->format('h:i A') : $a['time'];

                    $dateFormatted = !empty($a['date'])
                      ? date('d M Y', strtotime($a['date']))
                      : '';

                    $statusLabel = $a['status'];
                    $statusClass = 'status-pill--' . str_replace(' ', '-', strtolower($statusLabel));
                    ?>
                    <tr class="appointment-row" data-customer="<?= e($a['customer']) ?>"
                      data-service-id="<?= (int) $a['service_id'] ?>" data-branch-id="<?= (int) $a['branch_id'] ?>"
                      data-status="<?= e($statusLabel) ?>"
                      data-date="<?= !empty($a['date']) ? e(date('Y-m-d', strtotime($a['date']))) : '' ?>">
                      <td><?= e($dateFormatted) ?></td>
                      <td><?= e($timeFormatted) ?></td>
                      <td><?= e($a['customer']) ?></td>
                      <td><?= e($a['service']) ?></td>
                      <td><?= e($a['branch']) ?></td>
                      <td><?= e($a['supervisor']) ?></td>
                      <td>
                        <span class="status-pill <?= e($statusClass) ?>">
                          <?= e($statusLabel) ?>
                        </span>
                      </td>
                      <td>
                        <div class="action-buttons">
                          <a href="<?= $B ?>/admin/admin-appointments/show?id=<?= (int) $a['id'] ?>"
                            class="action-btn action-btn--view" title="View appointment">
                            <i class="fa-regular fa-eye"></i> View
                          </a>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        <?php else: ?>
          <div class="empty-state">
            <i class="fa-regular fa-calendar"></i>
            <p>No appointments found</p>
          </div>
        <?php endif; ?>
      </section>
    </section>
  </main>

  <script>
    function applyFilters() {
      const customerSearch = document.getElementById('customerSearch').value.toLowerCase().trim();
      const serviceFilter = document.getElementById('serviceFilter').value.trim();
      const branchFilter = document.getElementById('branchFilter').value.trim();
      const statusFilter = document.getElementById('statusFilter').value.trim();
      const dateFrom = document.getElementById('dateFrom').value.trim();
      const dateTo = document.getElementById('dateTo').value.trim();

      if (dateFrom && dateTo) {
        const url = new URL(window.location.href);
        url.searchParams.set('dateFrom', dateFrom);
        url.searchParams.set('dateTo', dateTo);
        url.searchParams.delete('date');
        window.location.href = url.toString();
        return;
      }

      const rows = document.querySelectorAll('.appointment-row');
      let visibleCount = 0;

      rows.forEach(row => {
        const customer = (row.dataset.customer || '').toLowerCase();
        const serviceId = row.dataset.serviceId || '';
        const branchId = row.dataset.branchId || '';
        const status = (row.dataset.status || '').trim();

        const matchCustomer = !customerSearch || customer.includes(customerSearch);
        const matchService = !serviceFilter || serviceId === serviceFilter;
        const matchBranch = !branchFilter || branchId === branchFilter;
        const matchStatus = !statusFilter || status === statusFilter;

        const shouldShow =
          matchCustomer &&
          matchService &&
          matchBranch &&
          matchStatus;

        row.style.display = shouldShow ? '' : 'none';

        if (shouldShow) {
          visibleCount++;
        }
      });

      updateEmptyState(visibleCount);
    }

    function clearFilters() {
      document.getElementById('customerSearch').value = '';
      document.getElementById('serviceFilter').value = '';
      document.getElementById('branchFilter').value = '';
      document.getElementById('statusFilter').value = '';
      document.getElementById('dateFrom').value = '';
      document.getElementById('dateTo').value = '';

      window.location.href = '<?= $B ?>/admin/admin-appointments?date=<?= e($selectedDate) ?>';
    }

    function updateEmptyState(visibleCount) {
      const table = document.getElementById('appointmentsTable');
      if (!table) return;

      let emptyState = document.getElementById('appointmentsEmptyState');
      const tableWrap = table.closest('.table-wrap');

      if (visibleCount === 0) {
        if (tableWrap) {
          tableWrap.style.display = 'none';
        }

        if (!emptyState) {
          emptyState = document.createElement('div');
          emptyState.id = 'appointmentsEmptyState';
          emptyState.className = 'empty-state';
          emptyState.innerHTML = `
          <i class="fa-regular fa-inbox"></i>
          <p>No appointments match your filters</p>
        `;

          if (tableWrap && tableWrap.parentElement) {
            tableWrap.parentElement.appendChild(emptyState);
          }
        } else {
          emptyState.style.display = '';
        }
      } else {
        if (tableWrap) {
          tableWrap.style.display = '';
        }

        if (emptyState) {
          emptyState.style.display = 'none';
        }
      }
    }

    document.getElementById('customerSearch')?.addEventListener('keypress', function (e) {
      if (e.key === 'Enter') {
        applyFilters();
      }
    });

    function goToDate(dateStr) {
      window.location.href = '<?= $B ?>/admin/admin-appointments?date=' + encodeURIComponent(dateStr);
    }

    function goToPreviousDay() {
      const date = new Date('<?= $selectedDate ?>');
      date.setDate(date.getDate() - 1);
      const dateStr = date.toISOString().split('T')[0];
      goToDate(dateStr);
    }

    function goToNextDay() {
      const date = new Date('<?= $selectedDate ?>');
      date.setDate(date.getDate() + 1);
      const dateStr = date.toISOString().split('T')[0];
      goToDate(dateStr);
    }

    function goToToday() {
      const today = new Date().toISOString().split('T')[0];
      goToDate(today);
    }
  </script>
</body>

</html>