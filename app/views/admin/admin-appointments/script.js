document.addEventListener("DOMContentLoaded", function () {
  const searchInput = document.querySelector('.filters input[type="text"]');
  const statusSelect = document.querySelector('.filters select');
  const cards = document.querySelectorAll('.card');

  function filterCards() {
    const searchText = searchInput.value.toLowerCase();
    const selectedStatus = statusSelect.value;

    cards.forEach(card => {
      const name = card.querySelector('strong').textContent.toLowerCase();
      const service = card.querySelector('p:nth-of-type(1)').textContent.toLowerCase();
      const badge = card.querySelector('.badge').textContent;

      const matchesSearch = name.includes(searchText) || service.includes(searchText);
      const matchesStatus = selectedStatus === 'All Status' || badge === selectedStatus;

      if (matchesSearch && matchesStatus) {
        card.style.display = "block";
      } else {
        card.style.display = "none";
      }
    });
  }

  searchInput.addEventListener("input", filterCards);
  statusSelect.addEventListener("change", filterCards);

  // Placeholder for button actions
  document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener("click", () => {
      alert("Edit action coming soon!");
    });
  });

  document.querySelectorAll('.cancel-btn').forEach(btn => {
    btn.addEventListener("click", () => {
      if (confirm("Are you sure you want to cancel this appointment?")) {
        alert("Appointment canceled.");
      }
    });
  });
});
