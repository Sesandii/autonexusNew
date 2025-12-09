<?php
/** @var array  $appointments */
/** @var array  $branches */
/** @var array  $services */
/** @var string $pageTitle */
/** @var string $current */
$current = $current ?? 'appointments';
$B = rtrim(BASE_URL, '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($pageTitle ?? 'Appointments') ?></title>

  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="<?= $B ?>/public/assets/css/admin/appointments/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

<main class="main-content appointments-main">

  <header class="page-header">
    <div class="page-breadcrumb">
      <span>Admin</span>
      <span>›</span>
      <span>Appointments</span>
    </div>

    <div class="page-header-main">
      <div class="page-title-wrap">
        <div class="page-icon"><i class="fa-regular fa-calendar-check"></i></div>
        <div>
          <h2>Appointments</h2>
          <p class="appointments-subtitle">Review and manage upcoming and past bookings.</p>
        </div>
      </div>
      <span class="page-chip">Today: <?= date('M j, Y'); ?></span>
    </div>
  </header>

  <section class="appointments-section">

    <div class="appointments-filters">
      <!-- search -->
      <div class="filter-item">
        <label for="searchInput">Search</label>
        <div class="filter-input-icon">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input id="searchInput"
                 type="text"
                 placeholder="Search by customer or service..."/>
        </div>
      </div>

      <!-- status -->
      <div class="filter-item">
        <label for="statusSelect">Status</label>
        <select id="statusSelect">
          <option value="">All</option>
          <option value="Scheduled">Scheduled</option>
          <option value="In Progress">In Progress</option>
          <option value="Completed">Completed</option>
          <option value="Cancelled">Cancelled</option>
        </select>
      </div>

      <!-- branch filter -->
      <div class="filter-item">
        <label for="branchSelect">Branch</label>
        <select id="branchSelect">
          <option value="">All branches</option>
          <?php foreach ($branches as $b): ?>
            <option value="<?= (int)$b['branch_id'] ?>">
              <?= htmlspecialchars($b['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- service filter -->
      <div class="filter-item">
        <label for="serviceSelect">Service</label>
        <select id="serviceSelect">
          <option value="">All services</option>
          <?php foreach ($services as $s): ?>
            <option value="<?= (int)$s['service_id'] ?>">
              <?= htmlspecialchars($s['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- date & time filters -->
      <div class="filter-item">
        <label for="dateInput">Date</label>
        <input id="dateInput" type="date"/>
      </div>

      <div class="filter-item">
        <label for="timeInput">From time</label>
        <input id="timeInput" type="time"/>
      </div>
    </div>

    <div id="cardsContainer" class="appointments-cards">
      <?php foreach ($appointments as $a):
        $dt = new DateTime($a['datetime']);
        $dateISO = $dt->format('Y-m-d');
        $timeFmt = $dt->format('M j, g:i A');
        $time24  = $dt->format('H:i');

        $status  = $a['status'];
        $badgeClass = [
          'Scheduled'   => 'status-pill--scheduled',
          'In Progress' => 'status-pill--progress',
          'Completed'   => 'status-pill--completed',
          'Cancelled'   => 'status-pill--cancelled',
        ][$status] ?? 'status-pill--scheduled';
      ?>
        <article class="appointment-card"
          data-id="<?= (int)$a['id'] ?>"
          data-customer="<?= htmlspecialchars($a['customer']) ?>"
          data-service="<?= htmlspecialchars($a['service']) ?>"
          data-service-id="<?= (int)$a['service_id'] ?>"
          data-branch="<?= htmlspecialchars($a['branch']) ?>"
          data-branch-id="<?= (int)$a['branch_id'] ?>"
          data-status="<?= htmlspecialchars($status) ?>"
          data-date="<?= $dateISO ?>"
          data-time="<?= $time24 ?>">

          <header class="appointment-card__header">
            <div>
              <p class="appointment-card__customer">
                <?= htmlspecialchars($a['customer']) ?>
              </p>
              <p class="appointment-card__service">
                <?= htmlspecialchars($a['service']) ?>
              </p>
            </div>
            <span class="status-pill <?= $badgeClass ?>">
              <?= htmlspecialchars($status) ?>
            </span>
          </header>

          <div class="appointment-card__meta">
            <p><i class="fa-solid fa-location-dot"></i>
               <span><?= htmlspecialchars($a['branch']) ?></span></p>
            <p><i class="fa-regular fa-clock"></i>
               <span><?= htmlspecialchars($timeFmt) ?></span></p>
          </div>

          <footer class="appointment-card__actions">
            <button class="chip-btn chip-btn--light view-btn"
                    data-url="<?= $B ?>/admin/admin-appointments/show?id=<?= (int)$a['id'] ?>">
              <i class="fa-regular fa-eye"></i>
              <span>View</span>
            </button>

            <button class="chip-btn chip-btn--dark edit-btn"
                    data-url="<?= $B ?>/admin/admin-appointments/edit?id=<?= (int)$a['id'] ?>">
              <i class="fa-regular fa-pen-to-square"></i>
              <span>Edit</span>
            </button>

            <form action="<?= $B ?>/admin/admin-appointments/delete"
                  method="post"
                  class="inline-form"
                  onsubmit="return confirm('Cancel / delete this appointment?');">
              <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
              <button type="submit" class="chip-btn chip-btn--danger cancel-btn">
                <i class="fa-regular fa-circle-xmark"></i>
                <span>Cancel</span>
              </button>
            </form>
          </footer>
        </article>
      <?php endforeach; ?>

      <?php if (empty($appointments)): ?>
        <p class="appointments-empty">
          <i class="fa-regular fa-folder-open"></i>
          No appointments found for the selected filters.
        </p>
      <?php endif; ?>
    </div>
  </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const cardsContainer = document.getElementById('cardsContainer');
  if (!cardsContainer) return;

  const cards        = Array.from(cardsContainer.querySelectorAll('.appointment-card'));
  const searchInput  = document.getElementById('searchInput');
  const statusSelect = document.getElementById('statusSelect');
  const branchSelect = document.getElementById('branchSelect');
  const serviceSelect = document.getElementById('serviceSelect');
  const dateInput    = document.getElementById('dateInput');
  const timeInput    = document.getElementById('timeInput');

  function applyFilters() {
    const searchTerm   = (searchInput && searchInput.value || '').toLowerCase().trim();
    const statusValue  = statusSelect && statusSelect.value || '';
    const branchValue  = branchSelect && branchSelect.value || '';
    const serviceValue = serviceSelect && serviceSelect.value || '';
    const dateValue    = dateInput && dateInput.value || '';
    const timeValue    = timeInput && timeInput.value || '';

    cards.forEach(card => {
      const customer = (card.dataset.customer || '').toLowerCase();
      const service  = (card.dataset.service || '').toLowerCase();
      const status   = card.dataset.status || '';
      const branchId = card.dataset.branchId || '';
      const serviceId = card.dataset.serviceId || '';
      const date     = card.dataset.date || '';
      const time     = card.dataset.time || '';

      const matchesSearch =
        !searchTerm ||
        customer.includes(searchTerm) ||
        service.includes(searchTerm);

      const matchesStatus  = !statusValue || status === statusValue;
      const matchesBranch  = !branchValue || branchId === branchValue;
      const matchesService = !serviceValue || serviceId === serviceValue;
      const matchesDate    = !dateValue || date === dateValue;
      const matchesTime    = !timeValue || (time && time.startsWith(timeValue));

      const visible = matchesSearch && matchesStatus &&
                      matchesBranch && matchesService &&
                      matchesDate && matchesTime;

      card.style.display = visible ? '' : 'none';
    });
  }

  if (searchInput)  searchInput.addEventListener('input', applyFilters);
  if (statusSelect) statusSelect.addEventListener('change', applyFilters);
  if (branchSelect) branchSelect.addEventListener('change', applyFilters);
  if (serviceSelect) serviceSelect.addEventListener('change', applyFilters);
  if (dateInput)     dateInput.addEventListener('change', applyFilters);
  if (timeInput)     timeInput.addEventListener('change', applyFilters);

  // View / Edit navigation
  cardsContainer.addEventListener('click', (e) => {
    const btn = e.target.closest('.view-btn, .edit-btn');
    if (!btn) return;
    const url = btn.dataset.url;
    if (url) window.location.href = url;
  });
});
</script>
</body>
</html>
