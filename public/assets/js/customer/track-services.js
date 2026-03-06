// Fallback if backend didn't inject data
let servicesData = Array.isArray(window.INITIAL_TRACK_DATA) ? window.INITIAL_TRACK_DATA : [];

// Render table
function renderTable(data) {
  const tbody = document.querySelector("#servicesTable tbody");
  if (!tbody) return;
  tbody.innerHTML = "";

  if (!data.length) {
    tbody.innerHTML = `
      <tr>
<<<<<<< HEAD
        <td colspan="5" style="text-align:center; padding:40px; color:#6B7280;">
=======
<<<<<<< HEAD
        <td colspan="5" style="text-align:center; padding:40px; color:#6B7280;">
=======
        <td colspan="4" style="text-align:center; padding:40px; color:#6B7280;">
>>>>>>> bc21bfd776db2147cd644a47aeb727bb8ca3d276
>>>>>>> 9f3bba9761a3aa1448bff2f28e7a96e5bf60ec85
          No services found matching your criteria
        </td>
      </tr>
    `;
    return;
  }

  data.forEach(service => {
    const tr = document.createElement("tr");
<<<<<<< HEAD
    const statusClass = (service.status || '').replace(/\s+/g, '-').toLowerCase();
=======
<<<<<<< HEAD
    const statusClass = (service.status || '').replace(/\s+/g, '-').toLowerCase();
    tr.innerHTML = `
      <td>${service.type || ''}</td>
      <td>${service.vehicle || ''}</td>
      <td>${service.dateBooked || ''}</td>
      <td><span class="status ${statusClass}">${service.status || ''}</span></td>
      <td>${service.estCompletion || '-'}</td>
=======
    const statusClass = (service.status || '').replace(/\s+/g, '.');
>>>>>>> 9f3bba9761a3aa1448bff2f28e7a96e5bf60ec85
    tr.innerHTML = `
      <td>${service.type || ''}</td>
      <td>${service.vehicle || ''}</td>
      <td>${service.dateBooked || ''}</td>
      <td><span class="status ${statusClass}">${service.status || ''}</span></td>
<<<<<<< HEAD
      <td>${service.estCompletion || '-'}</td>
=======
      <td>${service.estCompletion || ''}</td>
>>>>>>> bc21bfd776db2147cd644a47aeb727bb8ca3d276
>>>>>>> 9f3bba9761a3aa1448bff2f28e7a96e5bf60ec85
    `;
    tbody.appendChild(tr);
  });
}

// Filter (with AJAX if endpoint exists)
async function filterServices() {
  const qEl = document.getElementById("searchInput");
  const sEl = document.getElementById("statusFilter");
  const q = qEl ? qEl.value.trim() : '';
  const status = sEl ? sEl.value : 'All';

  // If we have a backend endpoint, use it; else filter locally
  if (typeof LIST_URL === 'string' && LIST_URL) {
    const url = `${LIST_URL}?q=${encodeURIComponent(q)}&status=${encodeURIComponent(status)}`;
    try {
      const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
      const json = await res.json();
      renderTable(Array.isArray(json.data) ? json.data : []);
      return;
    } catch (e) {
      // fallback to local filter
    }
  }

  // Local (client) filtering fallback
  const filtered = servicesData.filter(s => {
    const matchesSearch =
      (s.type || '').toLowerCase().includes(q.toLowerCase()) ||
<<<<<<< HEAD
      (s.vehicle || '').toLowerCase().includes(q.toLowerCase()) ||
=======
<<<<<<< HEAD
      (s.vehicle || '').toLowerCase().includes(q.toLowerCase()) ||
=======
>>>>>>> bc21bfd776db2147cd644a47aeb727bb8ca3d276
>>>>>>> 9f3bba9761a3aa1448bff2f28e7a96e5bf60ec85
      (s.dateBooked || '').includes(q.toLowerCase());
    const matchesStatus = (status === 'All') || (s.status === status);
    return matchesSearch && matchesStatus;
  });

  renderTable(filtered);
}

// Init
function initTrackServices() {
  const searchBtn     = document.getElementById("searchBtn");
  const searchInput   = document.getElementById("searchInput");
  const statusFilter  = document.getElementById("statusFilter");

  searchBtn && searchBtn.addEventListener("click", filterServices);
  searchInput && searchInput.addEventListener("keyup", (e) => { if (e.key === "Enter") filterServices(); });
  statusFilter && statusFilter.addEventListener("change", filterServices);

<<<<<<< HEAD
  // Apply default filter on page load (this will handle initial data loading and rendering)
  filterServices();
=======
  // First paint
  if (!servicesData.length) {
    // If no injected data, try to load from endpoint
    if (typeof LIST_URL === 'string' && LIST_URL) {
      fetch(LIST_URL).then(r => r.json()).then(j => {
        servicesData = Array.isArray(j.data) ? j.data : [];
        renderTable(servicesData);
      }).catch(() => renderTable([]));
    } else {
      renderTable([]);
    }
  } else {
    renderTable(servicesData);
  }
>>>>>>> bc21bfd776db2147cd644a47aeb727bb8ca3d276
}

document.readyState === 'loading'
  ? document.addEventListener('DOMContentLoaded', initTrackServices)
  : initTrackServices();
