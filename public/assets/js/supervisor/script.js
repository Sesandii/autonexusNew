// --- Modal ---
const modal = document.getElementById('modal');
const openAddBtn = document.getElementById('openAddBtn');
const modalClose = document.getElementById('modalClose');
const cancelBtn = document.getElementById('cancelBtn');
const modalForm = document.getElementById('modalForm');
const formAction = document.getElementById('formAction');
const workIdInput = document.getElementById('work_order_id');

// Open Add Modal
openAddBtn.addEventListener('click', () => {
  formAction.value = 'add_workorder';
  workIdInput.value = '';
  document.getElementById('modalTitle').textContent = 'Add Work Order';
  modal.setAttribute('aria-hidden', 'false');
});

// Close modal
[modalClose, cancelBtn].forEach(btn => {
  btn.addEventListener('click', () => modal.setAttribute('aria-hidden', 'true'));
});

// --- View Drawer ---
const viewDrawer = document.getElementById('viewDrawer');
const viewClose = document.getElementById('viewClose');
viewClose.addEventListener('click', () => viewDrawer.setAttribute('aria-hidden', 'true'));

// View details
document.querySelectorAll('.viewBtn').forEach(btn => {
  btn.addEventListener('click', () => {
    const row = btn.closest('tr');
    const cols = row.querySelectorAll('td');
    const html = `
      <p><strong>ID:</strong> ${cols[0].innerText}</p>
      <p><strong>Appointment:</strong> ${cols[1].innerText}</p>
      <p><strong>Service:</strong> ${cols[2].innerText}</p>
      <p><strong>Mechanic:</strong> ${cols[3].innerText}</p>
      <p><strong>Status:</strong> ${cols[4].innerText}</p>
      <p><strong>Total:</strong> ${cols[5].innerText}</p>
    `;
    document.getElementById('viewContent').innerHTML = html;
    viewDrawer.setAttribute('aria-hidden', 'false');
  });
});

// --- Edit Modal Prefill ---
document.querySelectorAll('.editBtn').forEach(btn => {
  btn.addEventListener('click', () => {
    const row = btn.closest('tr');
    const cols = row.querySelectorAll('td');
    document.getElementById('modalTitle').textContent = 'Edit Work Order #' + cols[0].innerText;
    formAction.value = 'edit_workorder';
    workIdInput.value = cols[0].innerText;

    const mechText = cols[3].innerText;
    const mechSelect = document.getElementById('mechanic_id');
    if (mechText === 'Unassigned') mechSelect.value = '';
    else {
      for (let opt of mechSelect.options) {
        if (opt.text.includes(mechText.split(' — ')[0])) {
          mechSelect.value = opt.value;
          break;
        }
      }
    }

    document.getElementById('total_cost').value = cols[5].innerText.replace(/,/g, '') || 0;
    modal.setAttribute('aria-hidden', 'false');
  });
});

// --- Delete Confirmation ---
function confirmDelete(e, form) {
  e.preventDefault();
  const confirmed = confirm('Are you sure you want to DELETE this work order?');
  if (confirmed) form.submit();
  return false;
}

// --- Auto-update total when selecting service ---
const serviceSelect = document.getElementById('service_id');
if (serviceSelect) {
  serviceSelect.addEventListener('change', e => {
    const opt = e.target.selectedOptions[0];
    const price = opt ? opt.dataset.price : 0;
    document.getElementById('total_cost').value = price || 0;
  });
}

// --- Hide toast after a few seconds ---
setTimeout(() => {
  const t = document.querySelector('.toast');
  if (t) t.style.display = 'none';
}, 4000);
