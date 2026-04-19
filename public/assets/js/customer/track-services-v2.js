document.addEventListener('DOMContentLoaded', function () {
  const cardsContainer = document.getElementById('cardsContainer');
  if (!cardsContainer) {
    return;
  }

  const cards = Array.from(cardsContainer.querySelectorAll('.card'));
  const emptyState = document.getElementById('emptyState');

  const branchFilter = document.getElementById('branchFilter');
  const statusFilter = document.getElementById('statusFilter');
  const timeFilter = document.getElementById('timeFilter');
  const searchInput = document.getElementById('searchInput');

  const normalize = function (value) {
    return String(value || '').trim().toLowerCase();
  };

  const applyFilters = function () {
    const branchVal = normalize(branchFilter ? branchFilter.value : '');
    const statusVal = normalize(statusFilter ? statusFilter.value : '');
    const timeVal = normalize(timeFilter ? timeFilter.value : '');
    const queryVal = normalize(searchInput ? searchInput.value : '');

    let visibleCount = 0;

    cards.forEach(function (card) {
      const cardBranch = normalize(card.getAttribute('data-branch'));
      const cardStatus = normalize(card.getAttribute('data-status'));
      const cardTime = normalize(card.getAttribute('data-time'));
      const searchText = normalize(card.getAttribute('data-search'));

      const matchesBranch = !branchVal || cardBranch === branchVal;
      const matchesStatus = !statusVal || cardStatus === statusVal;
      const matchesTime = !timeVal || cardTime.indexOf(timeVal) === 0;
      const matchesQuery = !queryVal || searchText.indexOf(queryVal) !== -1;

      const visible = matchesBranch && matchesStatus && matchesTime && matchesQuery;
      card.style.display = visible ? '' : 'none';

      if (visible) {
        visibleCount += 1;
      }
    });

    if (emptyState) {
      if (visibleCount === 0) {
        emptyState.removeAttribute('hidden');
      } else {
        emptyState.setAttribute('hidden', 'hidden');
      }
    }
  };

  if (branchFilter) {
    branchFilter.addEventListener('change', applyFilters);
  }
  if (statusFilter) {
    statusFilter.addEventListener('change', applyFilters);
  }
  if (timeFilter) {
    timeFilter.addEventListener('change', applyFilters);
  }
  if (searchInput) {
    searchInput.addEventListener('input', applyFilters);
  }

  applyFilters();
});
