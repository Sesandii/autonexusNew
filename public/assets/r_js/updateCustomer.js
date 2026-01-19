document.addEventListener('DOMContentLoaded', () => {
  const container = document.getElementById('vehicles-container');
  const addBtn = document.getElementById('add-vehicle');

  // Add new vehicle
  addBtn.addEventListener('click', () => {
    const index = container.querySelectorAll('.vehicle-entry').length;
    const div = document.createElement('div');
    div.className = 'vehicle-entry';
    div.innerHTML = `
      <input type="text" name="vehicles[${index}][license_plate]" placeholder="License Plate" required>
      <input type="text" name="vehicles[${index}][make]" placeholder="Make">
      <input type="text" name="vehicles[${index}][model]" placeholder="Model">
      <input type="text" name="vehicles[${index}][year]" placeholder="Year">
      <input type="text" name="vehicles[${index}][color]" placeholder="Color">
      <button type="button" class="remove-vehicle">❌</button>
    `;
    container.appendChild(div);
  });

  // Remove vehicle and reindex inputs
  container.addEventListener('click', (e) => {
    if (e.target.classList.contains('remove-vehicle')) {
      const vehicleDiv = e.target.closest('.vehicle-entry');
      vehicleDiv.remove();

      // Reindex all remaining vehicle inputs
      container.querySelectorAll('.vehicle-entry').forEach((entry, i) => {
        entry.querySelectorAll('input').forEach(input => {
          const field = input.name.match(/\[([a-z_]+)\]$/)[1]; // extract field name
          input.name = `vehicles[${i}][${field}]`;
        });
      });
    }
  });
});
