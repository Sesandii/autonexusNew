document.addEventListener('DOMContentLoaded', () => {
  const q = (sel, el = document) => el.querySelector(sel);
  const qa = (sel, el = document) => Array.from(el.querySelectorAll(sel));

  const searchInput = q('#searchInput');
  const statusSelect = q('#statusSelect');
  const dateInput = q('#dateInput');
  const cards = qa('#cardsContainer .card');

  function matches(card) {
    const qtext = (searchInput.value || '').trim().toLowerCase();
    const status = statusSelect.value;
    const date = dateInput.value;

    const customer = (card.dataset.customer || '').toLowerCase();
    const service  = (card.dataset.service || '').toLowerCase();
    const cstatus  = card.dataset.status || '';
    const cdate    = card.dataset.date || '';

    const textOk   = !qtext || customer.includes(qtext) || service.includes(qtext);
    const statusOk = !status || cstatus === status;
    const dateOk   = !date || cdate === date;

    return textOk && statusOk && dateOk;
  }

  function filter() {
    let shown = 0;
    cards.forEach(card => {
      if (matches(card)) {
        card.style.display = '';
        shown++;
      } else {
        card.style.display = 'none';
      }
    });
    // You could show a “No results” message if shown === 0
  }

  [searchInput, statusSelect, dateInput].forEach(el => el.addEventListener('input', filter));

  // Demo actions
  document.body.addEventListener('click', (e) => {
    const btn = e.target.closest('button[data-action]');
    if (!btn) return;
    const card = e.target.closest('.card');
    if (!card) return;

    const action = btn.dataset.action;
    const badge = card.querySelector('.badge');

    if (action === 'edit') {
      alert('Edit (demo): You can wire this to open a modal/edit form later.');
    }
    if (action === 'cancel') {
      // Demo-only: toggle to Cancelled state
      card.dataset.status = 'Cancelled';
      badge.textContent = 'Cancelled';
      badge.className = 'badge cancelled';
      filter(); // re-apply filters to reflect change
    }
  });
});
