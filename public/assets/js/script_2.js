document.addEventListener('DOMContentLoaded', () => {
  const searchInput = document.getElementById('searchInput');
  const ratingFilter = document.getElementById('ratingFilter');
  const repliedFilter = document.getElementById('repliedFilter');
  const dateFilter = document.getElementById('dateFilter');
  const cardsContainer = document.getElementById('cardsContainer');
  const cards = cardsContainer.querySelectorAll('.card');

  function filterCards() {
    const searchText = searchInput.value.toLowerCase();
    const selectedRating = ratingFilter.value;
    const selectedReplied = repliedFilter.value;
    const selectedDate = dateFilter.value; // yyyy-mm-dd or empty

    cards.forEach(card => {
      const name = card.querySelector('strong').textContent.toLowerCase();
      const service = card.querySelector('p strong').nextSibling.textContent.toLowerCase();
      const feedbackText = card.querySelector('.feedback-text').textContent.toLowerCase();
      const rating = card.dataset.rating;
      const replied = card.dataset.replied === 'true';
      const date = card.dataset.date; // yyyy-mm-dd

      // Text search (name, service, feedback)
      const matchesSearch =
        name.includes(searchText) ||
        service.includes(searchText) ||
        feedbackText.includes(searchText);

      // Rating filter
      const matchesRating = selectedRating === 'all' || rating === selectedRating;

      // Replied filter
      const matchesReplied =
        selectedReplied === 'all' ||
        (selectedReplied === 'replied' && replied) ||
        (selectedReplied === 'notReplied' && !replied);

      // Date filter (exact match or empty)
      const matchesDate = !selectedDate || date === selectedDate;

      if (matchesSearch && matchesRating && matchesReplied && matchesDate) {
        card.style.display = 'flex';
      } else {
        card.style.display = 'none';
      }
    });
  }

  searchInput.addEventListener('input', filterCards);
  ratingFilter.addEventListener('change', filterCards);
  repliedFilter.addEventListener('change', filterCards);
  dateFilter.addEventListener('change', filterCards);
});
