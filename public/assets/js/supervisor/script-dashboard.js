document.querySelectorAll('.toggle-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.toggle-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    document.querySelectorAll('.appointments').forEach(sec => sec.classList.add('hidden'));
    document.getElementById(btn.dataset.target)?.classList.remove('hidden');
  });
});


  document.addEventListener('DOMContentLoaded', () => {
    const userName = document.getElementById('user-name');
    const dropdown = document.getElementById('dropdown');

    if(userName && dropdown) {
      userName.addEventListener('click', () => {
        dropdown.classList.toggle('hidden');
      });

      // Optional: click outside to close dropdown
      document.addEventListener('click', (e) => {
        if (!userName.contains(e.target) && !dropdown.contains(e.target)) {
          dropdown.classList.add('hidden');
        }
      });
    }
  });
