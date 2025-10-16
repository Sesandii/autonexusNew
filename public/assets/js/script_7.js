document.addEventListener('DOMContentLoaded', () => {
  const tabs = document.querySelectorAll('.tab');
  const tables = document.querySelectorAll('table');

  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      // Remove the 'active' class from all tabs and tables
      tabs.forEach(tab => tab.classList.remove('active'));
      tables.forEach(table => table.classList.remove('active'));

      // Add 'active' class to clicked tab
      tab.classList.add('active');

      // Show the corresponding table based on the selected tab
      const activeTab = tab.getAttribute('data-tab');
      const activeTable = document.querySelector(`#${activeTab}`);
      activeTable.classList.add('active');
    });
  });
});
