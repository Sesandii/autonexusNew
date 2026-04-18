document.addEventListener('DOMContentLoaded', function () {
  var openButton = document.getElementById('addVehicleBtn');
  var modal = document.getElementById('vehicleModal');
  if (!openButton || !modal) return;

  var closeButtons = modal.querySelectorAll('[data-close]');
  var firstField = modal.querySelector('input[name="license_plate"]');
  var previouslyFocused = null;

  function openModal() {
    previouslyFocused = document.activeElement;
    modal.hidden = false;
    modal.setAttribute('aria-hidden', 'false');
    if (firstField) firstField.focus();
  }

  function closeModal() {
    modal.hidden = true;
    modal.setAttribute('aria-hidden', 'true');
    if (previouslyFocused && typeof previouslyFocused.focus === 'function') {
      previouslyFocused.focus();
    }
  }

  openButton.addEventListener('click', openModal);

  closeButtons.forEach(function (button) {
    button.addEventListener('click', closeModal);
  });

  modal.addEventListener('click', function (event) {
    if (event.target === modal) {
      closeModal();
    }
  });

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape' && !modal.hidden) {
      closeModal();
    }
  });
});
