document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.complaint').forEach(item => {
    item.style.cursor = 'pointer';
    item.addEventListener('click', () => {
      const url = item.getAttribute('data-url');
      if (url) {
        window.location.href = url;
      }
    });
  });
});

// Get the selects
const statusSelect = document.querySelector('select:nth-of-type(1)');
const prioritySelect = document.querySelector('select:nth-of-type(2)');

// Add event listeners for change
statusSelect.addEventListener('change', handleFilterChange);
prioritySelect.addEventListener('change', handleFilterChange);

function handleFilterChange() {
  const selectedStatus = statusSelect.value;
  const selectedPriority = prioritySelect.value;
  
  console.log('Selected Status:', selectedStatus);
  console.log('Selected Priority:', selectedPriority);
  
  // Here you can call a function to filter your complaints based on these values
  // e.g., filterComplaints(selectedStatus, selectedPriority);
}

