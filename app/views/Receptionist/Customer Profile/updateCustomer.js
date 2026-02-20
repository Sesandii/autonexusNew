
  // JS for adding/removing vehicle entries dynamically
  document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('vehicles-container');
    const addBtn = document.getElementById('add-vehicle');

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

    container.addEventListener('click', (e) => {
      if (e.target.classList.contains('remove-vehicle')) {
        const vehicleDiv = e.target.closest('.vehicle-entry');
        vehicleDiv.remove();

        // Reindex remaining entries
        container.querySelectorAll('.vehicle-entry').forEach((entry, i) => {
          entry.querySelectorAll('input').forEach(input => {
            const nameParts = input.name.split(']');
            const fieldName = nameParts[1].split('[')[1].replace(']', '');
            input.name = `vehicles[${i}][${fieldName}]`;
          });
        });
      }
    });
  });
