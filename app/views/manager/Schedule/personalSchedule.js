let availableMechanics = [];

// Load mechanics when page loads
document.addEventListener('DOMContentLoaded', function() {
  loadAvailableMechanics();
});

function loadAvailableMechanics() {
  const branchId = document.getElementById('branchId').value;
  const baseUrl = document.querySelector('meta[name="base-url"]').getAttribute('content');
  
  fetch(baseUrl + '/manager/schedule/getMechanics?branch_id=' + branchId)
    .then(response => response.json())
    .then(data => {
      availableMechanics = data;
    })
    .catch(error => {
      console.error('Error loading mechanics:', error);
    });
}

function openReassignModal(workOrderId, currentMechanicId) {
  document.getElementById('workOrderId').value = workOrderId;
  
  // Populate mechanics dropdown
  const select = document.getElementById('newMechanic');
  select.innerHTML = '<option value="">Select a mechanic...</option>';
  
  availableMechanics.forEach(mechanic => {
    // Don't show the current mechanic
    if (mechanic.mechanic_id != currentMechanicId) {
      const option = document.createElement('option');
      option.value = mechanic.mechanic_id;
      option.textContent = `${mechanic.name} - ${mechanic.specialization || 'General'}`;
      select.appendChild(option);
    }
  });
  
  document.getElementById('reassignModal').style.display = 'block';
  document.getElementById('modalOverlay').style.display = 'block';
}

function closeReassignModal() {
  document.getElementById('reassignModal').style.display = 'none';
  document.getElementById('modalOverlay').style.display = 'none';
  document.getElementById('reassignForm').reset();
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    closeReassignModal();
  }
});