// Select all rows with class 'clickable-row'
document.querySelectorAll('.clickable-row').forEach(row => {
  row.addEventListener('click', () => {
    // Redirect to the URL in data-href
   const id = row.dataset.customerId;
   window.location.href = `<?= BASE_URL ?>/receptionist/customers/${id}`;
  });
});
