// highlight current link in sidebar
document.addEventListener('DOMContentLoaded', () => {
  const current = window.location.pathname.replace(/\/+$/, '');
  document.querySelectorAll('.sidebar .menu a').forEach(a => {
    const href = a.getAttribute('href').replace(/\/+$/, '');
    if (href && current.startsWith(new URL(href, window.location.origin).pathname)) {
      a.classList.add('active');
    }
  });
});
