// Get elements
const modal = document.getElementById('invoiceModal');
const createBtn = document.querySelector('.create-btn');
const closeBtn = document.querySelector('.close-btn');
const cancelBtn = document.querySelector('.cancel-btn');

// Open modal
createBtn.addEventListener('click', () => {
  modal.style.display = 'block';
});

// Close modal when clicking X
closeBtn.addEventListener('click', () => {
  modal.style.display = 'none';
});

// Close modal when clicking Cancel button
cancelBtn.addEventListener('click', () => {
  modal.style.display = 'none';
});

// Close modal if clicking outside the modal box
window.addEventListener('click', (e) => {
  if (e.target === modal) {
    modal.style.display = 'none';
  }
});
