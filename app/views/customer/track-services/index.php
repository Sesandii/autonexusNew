<?php
$base    = rtrim(BASE_URL, '/');
$initial = $services ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title ?? 'Track Services') ?> - AutoNexus</title>

  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/page-header.css">
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/track-services.css">
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/sidebar.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

  <?php include APP_ROOT . '/views/layouts/customer-sidebar.php'; ?>

  <div class="container">
    <main class="main-content">
      <?php
        $headerIcon = 'fa-solid fa-list-check';
        $headerTitle = 'Track Services';
        $headerSubtitle = 'Monitor the progress of your service jobs.';
        include APP_ROOT . '/views/partials/customer-page-header.php';
      ?>

      <section class="search-filter">
        <input type="text" id="searchInput"
               placeholder="Search by service, vehicle plate, or date (YYYY-MM-DD)">
        <select id="statusFilter">
          <option value="All">All Statuses</option>
          <option value="Pending">Pending</option>
          <option value="In Progress">In Progress</option>
          <option value="Completed">Completed</option>
        </select>
        <button id="searchBtn">
          <i class="fa-solid fa-magnifying-glass"></i>
          <span>Search</span>
        </button>
      </section>

      <section class="table-container">
        <table id="servicesTable">
          <thead>
            <tr>
              <th>Service Type</th>
              <th>Vehicle</th>
              <th>Date Booked</th>
              <th>Status</th>
              <th>Est. Completion</th>
            </tr>
          </thead>
          <tbody>
          <!-- JS fills rows -->
          </tbody>
        </table>
        <div id="emptyState" class="empty-state" hidden>
          <i class="fa-regular fa-folder-open"></i>
          <p>No services match your filters yet.</p>
        </div>
      </section>
    </main>
  </div>

  <script>
    const BASE_URL  = "<?= $base ?>";
    const LIST_URL  = BASE_URL + "/customer/track-services/list";
    const INITIAL_TRACK_DATA = <?= json_encode($initial, JSON_UNESCAPED_UNICODE) ?>;
    
    // Debug info
    console.log('User session:', <?= json_encode($_SESSION['user'] ?? null) ?>);
    console.log('Initial data:', INITIAL_TRACK_DATA);

    // Inline JS to bypass cache
    // Seed table with server-rendered data even if fetch fails
    let servicesData = Array.isArray(INITIAL_TRACK_DATA) ? INITIAL_TRACK_DATA : [];

    function renderTable(data) {
      const tbody = document.querySelector("#servicesTable tbody");
      if (!tbody) return;
      tbody.innerHTML = "";

      if (!data.length) {
        tbody.innerHTML = `
          <tr>
            <td colspan="5" style="text-align:center; padding:40px; color:#6B7280;">
              No services found matching your criteria
            </td>
          </tr>
        `;
        return;
      }

      data.forEach(service => {
        const tr = document.createElement("tr");
        const statusClass = (service.status || '').replace(/\s+/g, '-').toLowerCase();
        tr.innerHTML = `
          <td>${service.type || ''}</td>
          <td>${service.vehicle || ''}</td>
          <td>${service.dateBooked || ''}</td>
          <td><span class="status ${statusClass}">${service.status || ''}</span></td>
          <td>${service.estCompletion || '-'}</td>
        `;
        tbody.appendChild(tr);
      });
    }

    async function filterServices() {
      const qEl = document.getElementById("searchInput");
      const sEl = document.getElementById("statusFilter");
      const q = qEl ? qEl.value.trim() : '';
      const status = sEl ? sEl.value : 'All';

      if (typeof LIST_URL === 'string' && LIST_URL) {
        const url = `${LIST_URL}?q=${encodeURIComponent(q)}&status=${encodeURIComponent(status)}`;
        try {
          const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
          const json = await res.json();
          console.log('API response:', json);
          const data = Array.isArray(json.data) ? json.data : [];
          servicesData = data;
          renderTable(data);
          return;
        } catch (e) {
          console.error('Fetch error:', e);
        }
      }

      const filtered = servicesData.filter(s => {
        const matchesSearch =
          (s.type || '').toLowerCase().includes(q.toLowerCase()) ||
          (s.vehicle || '').toLowerCase().includes(q.toLowerCase()) ||
          (s.dateBooked || '').includes(q.toLowerCase());
        const matchesStatus = (status === 'All') || (s.status === status);
        return matchesSearch && matchesStatus;
      });
      renderTable(filtered);
    }

    function initTrackServices() {
      const searchBtn = document.getElementById("searchBtn");
      const searchInput = document.getElementById("searchInput");
      const statusFilter = document.getElementById("statusFilter");

      searchBtn && searchBtn.addEventListener("click", filterServices);
      searchInput && searchInput.addEventListener("keyup", (e) => { if (e.key === "Enter") filterServices(); });
      statusFilter && statusFilter.addEventListener("change", filterServices);

      filterServices();
    }

    document.readyState === 'loading'
      ? document.addEventListener('DOMContentLoaded', initTrackServices)
      : initTrackServices();
  </script>
</body>
</html>
