<?php
$base  = rtrim(BASE_URL, '/');
$items = array_values($appointments ?? []);
$appointmentsCssVersion = @filemtime(dirname(APP_ROOT) . '/public/assets/css/customer/appointments.css') ?: time();
$sidebarCssVersion = @filemtime(dirname(APP_ROOT) . '/public/assets/css/customer/sidebar.css') ?: time();

$safeStatusClass = static function (string $value): string {
    $normalized = strtolower(trim($value));
    return match ($normalized) {
        'pending', 'ongoing', 'upcoming', 'completed', 'cancelled' => $normalized,
        default => 'upcoming',
    };
};

$statusGroup = static function (string $statusClass): string {
    return in_array($statusClass, ['upcoming', 'pending', 'ongoing'], true)
        ? 'upcoming'
        : $statusClass;
};

$formatDate = static function (?string $date): string {
    if (!$date) {
        return '-';
    }
    $timestamp = strtotime($date);
    return $timestamp ? date('d M', $timestamp) : $date;
};

$formatTime = static function (?string $time): string {
    if (!$time) {
        return '-';
    }
    return substr($time, 0, 5);
};

$rows = [];
foreach ($items as $item) {
    $rowStatus = $safeStatusClass((string)($item['status_class'] ?? 'upcoming'));
    $dateRaw   = (string)($item['date'] ?? '');
    $timeRaw   = (string)($item['time'] ?? '');
    $stamp     = strtotime(trim($dateRaw . ' ' . ($timeRaw !== '' ? $timeRaw : '00:00')));

    $rows[] = [
        'appointment_id' => (int)($item['appointment_id'] ?? 0),
        'service'        => (string)($item['service'] ?? 'Service'),
        'license_plate'  => (string)($item['license_plate'] ?? '—'),
        'branch'         => (string)($item['branch'] ?? '-'),
        'date_raw'       => $dateRaw,
        'time_raw'       => $formatTime($timeRaw),
        'date_label'     => $formatDate($dateRaw),
        'status_text'    => (string)($item['status'] ?? ucfirst($rowStatus)),
        'status_class'   => $rowStatus,
        'status_group'   => $statusGroup($rowStatus),
        'stamp'          => $stamp !== false ? (int)$stamp : 0,
    ];
}

$total = count($rows);
$todayKey = date('Y-m-d');
$todayRows = array_values(array_filter(
    $rows,
    static fn(array $row): bool => $row['date_raw'] === $todayKey
));

$statusCounts = [
    'upcoming' => 0,
    'completed' => 0,
    'cancelled' => 0,
];
foreach ($rows as $row) {
    if ($row['status_group'] === 'upcoming') {
        $statusCounts['upcoming']++;
    } elseif ($row['status_group'] === 'completed') {
        $statusCounts['completed']++;
    } elseif ($row['status_group'] === 'cancelled') {
        $statusCounts['cancelled']++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($title ?? 'Appointments', ENT_QUOTES, 'UTF-8') ?> - AutoNexus</title>

  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/normalize-ui.css">
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/sidebar.css?v=<?= (int)$sidebarCssVersion ?>">
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/page-header.css">
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/appointments.css?v=<?= (int)$appointmentsCssVersion ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

  <?php include APP_ROOT . '/views/layouts/customer-sidebar.php'; ?>

  <main class="main-content appointments-main customer-layout-main">
    <div class="appointments-page">
      <?php
        $headerIcon = 'fa-solid fa-calendar-check';
        $headerTitle = 'Your Appointments';
        $headerSubtitle = 'Manage upcoming visits, completed services, and past cancellations easily.';
        $headerActionBtn = '<span class="appointment-count-pill">' . $total . ' appointment' . ($total === 1 ? '' : 's') . '</span><a href="' . htmlspecialchars($base) . '/customer/book" class="book-service-btn">Book Service</a>';
        include APP_ROOT . '/views/partials/customer-page-header.php';
      ?>

      <section class="summary-grid" id="statusCards">
        <button type="button" class="summary-card summary-upcoming" data-filter="upcoming">
          <span class="summary-label">
            <span class="summary-icon" aria-hidden="true"><i class="fa-regular fa-clock"></i></span>
            Upcoming
          </span>
          <span class="summary-value"><?= $statusCounts['upcoming'] ?></span>
          <span class="summary-note">Requested, confirmed, and ongoing</span>
        </button>

        <button type="button" class="summary-card summary-completed" data-filter="completed">
          <span class="summary-label">
            <span class="summary-icon" aria-hidden="true"><i class="fa-regular fa-circle-check"></i></span>
            Completed
          </span>
          <span class="summary-value"><?= $statusCounts['completed'] ?></span>
          <span class="summary-note">Finished service visits</span>
        </button>

        <button type="button" class="summary-card summary-cancelled" data-filter="cancelled">
          <span class="summary-label">
            <span class="summary-icon" aria-hidden="true"><i class="fa-regular fa-circle-xmark"></i></span>
            Cancelled
          </span>
          <span class="summary-value"><?= $statusCounts['cancelled'] ?></span>
          <span class="summary-note">Cancelled bookings</span>
        </button>

        <button type="button" class="summary-card summary-total is-active" data-filter="all">
          <span class="summary-label">
            <span class="summary-icon" aria-hidden="true"><i class="fa-solid fa-layer-group"></i></span>
            Total
          </span>
          <span class="summary-value"><?= $total ?></span>
          <span class="summary-note">All appointments</span>
        </button>
      </section>

      <section class="controls-card">
        <div class="search-wrap">
          <label for="appointmentSearch">Search appointments</label>
          <div class="search-field">
            <span class="search-icon" aria-hidden="true"><i class="fa-solid fa-magnifying-glass"></i></span>
            <input id="appointmentSearch" type="search" placeholder="Search appointments...">
          </div>
        </div>

        <div class="sort-wrap">
          <label for="appointmentSort">Sort</label>
          <select id="appointmentSort" aria-label="Sort appointments">
            <option value="newest" selected>Newest first</option>
            <option value="oldest">Oldest first</option>
            <option value="service_asc">Service A-Z</option>
            <option value="service_desc">Service Z-A</option>
          </select>
        </div>
      </section>

      <section class="table-shell table-shell-today">
        <div class="section-head">
          <div class="section-title-wrap">
            <h2>Today</h2>
            <span id="todayCountText" class="section-count-badge"><?= count($todayRows) ?> appointment<?= count($todayRows) === 1 ? '' : 's' ?></span>
          </div>
        </div>

        <div class="table-scroll">
          <table id="todayAppointmentsTable" class="appointments-table">
            <thead>
              <tr>
                <th>Service Name</th>
                <th>License Plate</th>
                <th>Date</th>
                <th>Time</th>
                <th>Location</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($todayRows)): ?>
                <tr id="todayEmptyRow" class="empty-row">
                  <td colspan="7">No appointments scheduled for today.</td>
                </tr>
              <?php else: ?>
                <tr id="todayEmptyRow" class="empty-row" style="display:none;">
                  <td colspan="7">No appointments match your current filters.</td>
                </tr>
                <?php foreach ($todayRows as $row): ?>
                  <?php $id = (int)$row['appointment_id']; ?>
                  <tr
                    data-row="1"
                    data-group="<?= htmlspecialchars($row['status_group'], ENT_QUOTES, 'UTF-8') ?>"
                    data-stamp="<?= (int)$row['stamp'] ?>"
                    data-service="<?= htmlspecialchars(strtolower($row['service']), ENT_QUOTES, 'UTF-8') ?>"
                  >
                    <td class="service-name"><?= htmlspecialchars($row['service'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($row['license_plate'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($row['date_label'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($row['time_raw'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($row['branch'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                      <span class="status-pill status-<?= htmlspecialchars($row['status_class'], ENT_QUOTES, 'UTF-8') ?>">
                        <?= htmlspecialchars($row['status_text'], ENT_QUOTES, 'UTF-8') ?>
                      </span>
                    </td>
                    <td class="actions-cell">
                      <a class="action-btn view" href="<?= $base ?>/customer/appointments/<?= $id ?>">View</a>

                      <?php
                        $canCancel = in_array($row['status_class'], ['upcoming', 'pending'], true);
                        $canReview = ($row['status_class'] === 'completed');
                        $canReschedule = in_array($row['status_class'], ['upcoming', 'pending', 'completed', 'cancelled'], true);
                      ?>
                      <?php if ($canReview): ?>
                        <a class="action-btn review" href="<?= $base ?>/customer/rate-service?appointment=<?= $id ?>">Review</a>
                      <?php endif; ?>

                      <?php if ($canReschedule): ?>
                        <a class="action-btn reschedule" href="<?= $base ?>/customer/book?rebook=<?= $id ?>">Reschedule</a>
                      <?php endif; ?>

                      <?php if ($canCancel): ?>
                        <form method="post" action="<?= $base ?>/customer/appointments/cancel" class="inline-action" onsubmit="return confirm('Cancel this appointment?');">
                          <input type="hidden" name="appointment_id" value="<?= $id ?>">
                          <button class="action-btn cancel" type="submit">Cancel</button>
                        </form>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>

      <section class="table-shell all-appointments-shell">
        <div class="section-head">
          <div class="section-title-wrap">
            <h2>All Appointments</h2>
            <span id="allCountText" class="section-count-badge"><?= $total ?> appointment<?= $total === 1 ? '' : 's' ?></span>
          </div>
        </div>

        <div class="table-scroll">
          <table id="allAppointmentsTable" class="appointments-table">
            <thead>
              <tr>
                <th>Service Name</th>
                <th>License Plate</th>
                <th>Date</th>
                <th>Time</th>
                <th>Location</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($rows)): ?>
                <tr id="allEmptyRow" class="empty-row">
                  <td colspan="7">No appointments found. Book a service to get started.</td>
                </tr>
              <?php else: ?>
                <tr id="allEmptyRow" class="empty-row" style="display:none;">
                  <td colspan="7">No appointments match your current filters.</td>
                </tr>
                <?php foreach ($rows as $row): ?>
                  <?php $id = (int)$row['appointment_id']; ?>
                  <tr
                    data-row="1"
                    data-group="<?= htmlspecialchars($row['status_group'], ENT_QUOTES, 'UTF-8') ?>"
                    data-stamp="<?= (int)$row['stamp'] ?>"
                    data-service="<?= htmlspecialchars(strtolower($row['service']), ENT_QUOTES, 'UTF-8') ?>"
                  >
                    <td class="service-name"><?= htmlspecialchars($row['service'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($row['license_plate'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($row['date_label'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($row['time_raw'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($row['branch'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                      <span class="status-pill status-<?= htmlspecialchars($row['status_class'], ENT_QUOTES, 'UTF-8') ?>">
                        <?= htmlspecialchars($row['status_text'], ENT_QUOTES, 'UTF-8') ?>
                      </span>
                    </td>
                    <td class="actions-cell">
                      <a class="action-btn view" href="<?= $base ?>/customer/appointments/<?= $id ?>">View</a>

                      <?php
                        $canCancel = in_array($row['status_class'], ['upcoming', 'pending'], true);
                        $canReview = ($row['status_class'] === 'completed');
                        $canReschedule = in_array($row['status_class'], ['upcoming', 'pending', 'completed', 'cancelled'], true);
                      ?>
                      <?php if ($canReview): ?>
                        <a class="action-btn review" href="<?= $base ?>/customer/rate-service?appointment=<?= $id ?>">Review</a>
                      <?php endif; ?>

                      <?php if ($canReschedule): ?>
                        <a class="action-btn reschedule" href="<?= $base ?>/customer/book?rebook=<?= $id ?>">Reschedule</a>
                      <?php endif; ?>

                      <?php if ($canCancel): ?>
                        <form method="post" action="<?= $base ?>/customer/appointments/cancel" class="inline-action" onsubmit="return confirm('Cancel this appointment?');">
                          <input type="hidden" name="appointment_id" value="<?= $id ?>">
                          <button class="action-btn cancel" type="submit">Cancel</button>
                        </form>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <div class="table-footer">
          <div id="allPagination" class="pagination"></div>
          <label for="rowsPerPage" class="rows-control">
            Rows per page
            <select id="rowsPerPage">
              <option value="5">5</option>
              <option value="10" selected>10</option>
              <option value="20">20</option>
            </select>
          </label>
        </div>
      </section>
    </div>
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const cards = Array.from(document.querySelectorAll('.summary-card'));
      const searchInput = document.getElementById('appointmentSearch');
      const sortSelect = document.getElementById('appointmentSort');
      const rowsPerPageSelect = document.getElementById('rowsPerPage');

      const todayBody = document.querySelector('#todayAppointmentsTable tbody');
      const allBody = document.querySelector('#allAppointmentsTable tbody');

      const todayRows = todayBody
        ? Array.from(todayBody.querySelectorAll('tr[data-row="1"]'))
        : [];
      const allRows = allBody
        ? Array.from(allBody.querySelectorAll('tr[data-row="1"]'))
        : [];

      const todayEmptyRow = document.getElementById('todayEmptyRow');
      const allEmptyRow = document.getElementById('allEmptyRow');
      const todayCountText = document.getElementById('todayCountText');
      const allCountText = document.getElementById('allCountText');
      const pagination = document.getElementById('allPagination');

      const state = {
        filter: 'all',
        query: '',
        sort: 'newest',
        page: 1,
        rowsPerPage: parseInt(rowsPerPageSelect ? rowsPerPageSelect.value : '10', 10) || 10
      };

      const matchesFilters = function (row) {
        const group = row.getAttribute('data-group') || 'upcoming';
        if (state.filter !== 'all' && group !== state.filter) {
          return false;
        }
        if (!state.query) {
          return true;
        }
        return row.textContent.toLowerCase().includes(state.query);
      };

      const sorter = function (a, b) {
        if (state.sort === 'service_asc') {
          return (a.getAttribute('data-service') || '').localeCompare(b.getAttribute('data-service') || '');
        }
        if (state.sort === 'service_desc') {
          return (b.getAttribute('data-service') || '').localeCompare(a.getAttribute('data-service') || '');
        }

        const left = parseInt(a.getAttribute('data-stamp') || '0', 10);
        const right = parseInt(b.getAttribute('data-stamp') || '0', 10);

        if (state.sort === 'oldest') {
          return left - right;
        }
        return right - left;
      };

      const getSortedRows = function (rows) {
        return rows.slice().sort(sorter);
      };

      const setCountLabel = function (node, count) {
        if (!node) {
          return;
        }
        node.textContent = count + ' appointment' + (count === 1 ? '' : 's');
      };

      const updateEmptyState = function (emptyNode, count) {
        if (!emptyNode) {
          return;
        }
        emptyNode.style.display = count === 0 ? '' : 'none';
      };

      const buildPagination = function (totalPages) {
        if (!pagination) {
          return;
        }

        pagination.innerHTML = '';
        if (totalPages <= 1) {
          return;
        }

        const prevBtn = document.createElement('button');
        prevBtn.type = 'button';
        prevBtn.className = 'page-btn';
        prevBtn.textContent = 'Prev';
        prevBtn.disabled = state.page === 1;
        prevBtn.addEventListener('click', function () {
          if (state.page > 1) {
            state.page -= 1;
            renderAllTable();
          }
        });
        pagination.appendChild(prevBtn);

        for (let i = 1; i <= totalPages; i += 1) {
          const btn = document.createElement('button');
          btn.type = 'button';
          btn.className = 'page-btn' + (i === state.page ? ' is-active' : '');
          btn.textContent = String(i);
          btn.addEventListener('click', function () {
            state.page = i;
            renderAllTable();
          });
          pagination.appendChild(btn);
        }

        const nextBtn = document.createElement('button');
        nextBtn.type = 'button';
        nextBtn.className = 'page-btn';
        nextBtn.textContent = 'Next';
        nextBtn.disabled = state.page === totalPages;
        nextBtn.addEventListener('click', function () {
          if (state.page < totalPages) {
            state.page += 1;
            renderAllTable();
          }
        });
        pagination.appendChild(nextBtn);
      };

      const renderTodayTable = function () {
        if (!todayBody) {
          return;
        }

        const filtered = getSortedRows(todayRows.filter(matchesFilters));
        todayRows.forEach(function (row) {
          row.style.display = 'none';
        });

        filtered.forEach(function (row) {
          todayBody.appendChild(row);
          row.style.display = '';
        });

        updateEmptyState(todayEmptyRow, filtered.length);
        setCountLabel(todayCountText, filtered.length);
      };

      const renderAllTable = function () {
        if (!allBody) {
          return;
        }

        const filtered = getSortedRows(allRows.filter(matchesFilters));
        const totalVisible = filtered.length;
        const totalPages = Math.max(1, Math.ceil(totalVisible / state.rowsPerPage));

        if (state.page > totalPages) {
          state.page = totalPages;
        }

        const start = (state.page - 1) * state.rowsPerPage;
        const visibleRows = filtered.slice(start, start + state.rowsPerPage);

        allRows.forEach(function (row) {
          row.style.display = 'none';
        });

        visibleRows.forEach(function (row) {
          allBody.appendChild(row);
          row.style.display = '';
        });

        updateEmptyState(allEmptyRow, totalVisible);
        setCountLabel(allCountText, totalVisible);
        buildPagination(totalPages);
      };

      const renderTables = function () {
        renderTodayTable();
        renderAllTable();
      };

      cards.forEach(function (card) {
        card.addEventListener('click', function () {
          state.filter = card.getAttribute('data-filter') || 'all';
          state.page = 1;
          cards.forEach(function (node) {
            node.classList.remove('is-active');
          });
          card.classList.add('is-active');
          renderTables();
        });
      });

      if (searchInput) {
        searchInput.addEventListener('input', function () {
          state.query = searchInput.value.trim().toLowerCase();
          state.page = 1;
          renderTables();
        });
      }

      if (sortSelect) {
        sortSelect.addEventListener('change', function () {
          state.sort = sortSelect.value;
          state.page = 1;
          renderTables();
        });
      }

      if (rowsPerPageSelect) {
        rowsPerPageSelect.addEventListener('change', function () {
          const nextValue = parseInt(rowsPerPageSelect.value, 10);
          state.rowsPerPage = Number.isFinite(nextValue) && nextValue > 0 ? nextValue : 10;
          state.page = 1;
          renderAllTable();
        });
      }

      renderTables();
    });
  </script>
</body>
</html>
