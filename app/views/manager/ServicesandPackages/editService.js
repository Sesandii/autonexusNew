// public/assets/js/manager/editService.js

document.addEventListener('DOMContentLoaded', () => {

  // 🔁 Redirect function (reusable)
  function redirectToPage() {
    window.location.href = "Services.html"; // Replace with your target page
  }

  // 🚪 Handle Save button click
  const saveBtn = document.querySelector('.save-button');
  if (saveBtn) {
    saveBtn.addEventListener('click', () => {
      // Optional: Add form validation here
      redirectToPage();
    });
  }

  // ❌ Handle Cancel button click
  const cancelBtn = document.querySelector('.cancel-button');
  if (cancelBtn) {
    cancelBtn.addEventListener('click', redirectToPage);
  }

  // ❌ Handle Close (×) button click
  const closeBtn = document.querySelector('.close-button');
  if (closeBtn) {
    closeBtn.addEventListener('click', redirectToPage);
  }

  // ➕ Handle Add Item clicks for all buttons
  document.querySelectorAll('.add-item').forEach(button => {
    button.addEventListener('click', e => {
      e.preventDefault();

      // Find the container for THIS button
      const container = button.closest('.service-items-container');

      // Create wrapper for input + remove button
      const itemWrapper = document.createElement('div');
      itemWrapper.className = 'service-items';

      // Create the new input
      const newItem = document.createElement('input');
      newItem.type = 'text';
      newItem.placeholder = 'Service item';
      newItem.className = 'service-item-input';

      // Create remove button
      const removeButton = document.createElement('span');
      removeButton.className = 'remove-item';
      removeButton.textContent = '❌';

      // Add remove event
      removeButton.addEventListener('click', () => {
        itemWrapper.remove();
      });

      // Append input and remove button to wrapper
      itemWrapper.appendChild(newItem);
      itemWrapper.appendChild(removeButton);

      // Insert wrapper before the add button
      container.insertBefore(itemWrapper, button);
    });
  });

  // Enable remove on existing default remove buttons on page load
  document.querySelectorAll('.remove-item').forEach(button => {
    button.addEventListener('click', () => {
      button.parentElement.remove();
    });
  });

  // 🔄 Handle Service/Package field toggle
  const typeSelect = document.getElementById('typeSelect');
  const serviceFields = document.getElementById('serviceFields');
  const packageFields = document.getElementById('packageFields');
  const serviceCodeGroup = document.getElementById('serviceCodeGroup');
  const packageCodeGroup = document.getElementById('packageCodeGroup');

  const toggleFields = () => {
    if (!typeSelect) return;

    if (typeSelect.value === 'service') {
      serviceFields.style.display = '';
      packageFields.style.display = 'none';
      serviceCodeGroup.style.display = '';
      packageCodeGroup.style.display = 'none';

      serviceFields.querySelectorAll('input, select').forEach(el => el.required = true);
      packageFields.querySelectorAll('input, select').forEach(el => el.required = false);
    } else {
      serviceFields.style.display = 'none';
      packageFields.style.display = '';
      serviceCodeGroup.style.display = 'none';
      packageCodeGroup.style.display = '';

      packageFields.querySelectorAll('input, select').forEach(el => el.required = true);
      serviceFields.querySelectorAll('input, select').forEach(el => el.required = false);
    }
  };

  // Initial toggle
  toggleFields();

  // On change
  if (typeSelect) {
    typeSelect.addEventListener('change', toggleFields);
  }

});