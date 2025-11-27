<?php
/** @var array  $appointments */
/** @var array  $branches */
/** @var array  $services */
/** @var string $pageTitle */
/** @var string $current */
?>
<?php $current = $current ?? 'appointments'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($pageTitle ?? 'Appointments') ?></title>

  <link rel="stylesheet" href="<?= rtrim(BASE_URL,'/') ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="<?= rtrim(BASE_URL,'/') ?>/public/assets/css/admin/appointments/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

<main class="main-content">
  <section class="appointments-section">
    <h2>Appointments Management</h2>

    <div class="filters">
      <!-- search -->
      <input id="searchInput" type="text" placeholder="Search by customer or service..."/>

      <!-- status -->
      <select id="statusSelect">
        <option value="">All Status</option>
        <option value="Scheduled">Scheduled</option>
        <option value="In Progress">In Progress</option>
        <option value="Completed">Completed</option>
        <option value="Cancelled">Cancelled</option>
      </select>

      <!-- branch filter -->
      <select id="branchSelect">
        <option value="">All Branches</option>
        <?php foreach ($branches as $b): ?>
          <option value="<?= (int)$b['branch_id'] ?>">
              <?= htmlspecialchars($b['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <!-- service filter -->
      <select id="serviceSelect">
        <option value="">All Services</option>
        <?php foreach ($services as $s): ?>
          <option value="<?= (int)$s['service_id'] ?>">
              <?= htmlspecialchars($s['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <!-- date & time filters -->
      <input id="dateInput" type="date"/>
      <input id="timeInput" type="time"/>
    </div>

    <div id="cardsContainer" class="cards-container">
      <?php foreach ($appointments as $a):
        $dt = new DateTime($a['datetime']);
        $dateISO = $dt->format('Y-m-d');
        $timeFmt = $dt->format('M j, g:i A');
        $time24  = $dt->format('H:i');

        $status  = $a['status'];
        $badgeClass = [
          'Scheduled'   => 'scheduled',
          'In Progress' => 'in-progress',
          'Completed'   => 'completed',
          'Cancelled'   => 'cancelled',
        ][$status] ?? 'scheduled';
      ?>
        <div class="card"
             data-id="<?= (int)$a['id'] ?>"
             data-customer="<?= htmlspecialchars($a['customer']) ?>"
             data-service="<?= htmlspecialchars($a['service']) ?>"
             data-service-id="<?= (int)$a['service_id'] ?>"
             data-branch="<?= htmlspecialchars($a['branch']) ?>"
             data-branch-id="<?= (int)$a['branch_id'] ?>"
             data-status="<?= htmlspecialchars($status) ?>"
             data-date="<?= $dateISO ?>"
             data-time="<?= $time24 ?>">
          <div class="card-header">
            <strong><?= htmlspecialchars($a['customer']) ?></strong>
            <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($status) ?></span>
          </div>
          <p>Service: <?= htmlspecialchars($a['service']) ?></p>
          <p>Branch: <?= htmlspecialchars($a['branch']) ?></p>
          <p>Time: <?= htmlspecialchars($timeFmt) ?></p>

          <div class="card-actions">
            <button class="view-btn"
                    data-url="<?= rtrim(BASE_URL,'/') ?>/admin/admin-appointments/show?id=<?= (int)$a['id'] ?>">
              <i class="fa-regular fa-eye"></i> View
            </button>

            <button class="edit-btn"
                    data-url="<?= rtrim(BASE_URL,'/') ?>/admin/admin-appointments/edit?id=<?= (int)$a['id'] ?>">
              <i class="fa-regular fa-pen-to-square"></i> Edit
            </button>

            <form action="<?= rtrim(BASE_URL,'/') ?>/admin/admin-appointments/delete"
                  method="post"
                  class="inline-form"
                  onsubmit="return confirm('Are you sure you want to cancel/delete this appointment?');">
              <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
              <button type="submit" class="cancel-btn">
                <i class="fa-regular fa-circle-xmark"></i> Cancel
              </button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
</main>

<!-- Inline JS for filters + button actions -->
<script>
document.addEventListener('DOMContentLoaded', () => {
  const cardsContainer = document.getElementById('cardsContainer');
  if (!cardsContainer) return;

  const cards        = Array.from(cardsContainer.querySelectorAll('.card'));
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

  // View / Edit buttons navigation
  cardsContainer.addEventListener('click', (e) => {
    const btn = e.target.closest('.view-btn, .edit-btn');
    if (!btn) return;
    const url = btn.dataset.url;
    if (url) {
      window.location.href = url;
    }
  });
});
</script>
</body>
</html>
