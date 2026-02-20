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
        <td colspan="4" style="text-align:center; padding:40px; color:#6B7280;">
          No services found matching your criteria
        </td>
      </tr>
    `;
    return;
  }

  data.forEach(service => {
    const tr = document.createElement("tr");
    const statusClass = (service.status || '').replace(/\s+/g, '.');
    tr.innerHTML = `
      <td>${service.type || ''}</td>
      <td>${service.dateBooked || ''}</td>
      <td><span class="status ${statusClass}">${service.status || ''}</span></td>
      <td>${service.estCompletion || ''}</td>
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
}

document.readyState === 'loading'
  ? document.addEventListener('DOMContentLoaded', initTrackServices)
  : initTrackServices();
