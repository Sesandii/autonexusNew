// Select all rows with class 'clickable-row'
document.querySelectorAll('.clickable-row').forEach(row => {
  row.addEventListener('click', () => {
    // Redirect to the URL in data-href
    window.location.href = row.dataset.href;
  });
});
