document.addEventListener('DOMContentLoaded', () => {
  const tabs = document.querySelectorAll('.tab');
  const rows = document.querySelectorAll('.service-table tbody tr');

  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      // Remove active class from all tabs
      tabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');

      const selected = tab.getAttribute('data-tab');

      rows.forEach(row => {
        if (selected === 'all') {
          row.style.display = 'table-row';
        } else if (row.classList.contains(selected)) {
          row.style.display = 'table-row';
        } else {
          row.style.display = 'none';
        }
      });
    });
  });
});
