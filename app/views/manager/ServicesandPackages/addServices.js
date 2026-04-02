// 🔁 Redirect function (reusable)
function redirectToPage() {
  window.location.href = "Services.html"; // Replace with your target page
}

// 🚪 Handle Save button click
document.querySelector('.save-button').addEventListener('click', function () {
  // Optional: Add validation here before redirecting
  redirectToPage();
});

// ❌ Handle Cancel button click
document.querySelector('.cancel-button').addEventListener('click', function () {
  redirectToPage();
});

// ❌ Handle Close (×) button click
document.querySelector('.close-button').addEventListener('click', function () {
  redirectToPage();
});

// ➕ Handle Add Item clicks for all buttons
document.querySelectorAll('.add-item').forEach(button => {
  button.addEventListener('click', function (e) {
    e.preventDefault();

    // Find the container for THIS button
    const container = this.closest('.service-items-container');

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
    container.insertBefore(itemWrapper, this);
  });
});

// Enable remove on existing default remove buttons on page load
document.querySelectorAll('.remove-item').forEach(button => {
  button.addEventListener('click', function () {
    this.parentElement.remove();
  });
});

const typeSelect = document.getElementById('typeSelect');
const serviceFields = document.getElementById('serviceFields');
const packageFields = document.getElementById('packageFields');

typeSelect.addEventListener('change', () => {
  serviceFields.style.display = typeSelect.value === 'service' ? 'block' : 'none';
  packageFields.style.display = typeSelect.value === 'package' ? 'block' : 'none';
});
