/*const toggleButtons = document.querySelectorAll('.toggle-button');

toggleButtons.forEach(button => {
  button.addEventListener('click', () => {
    const dropdown = button.nextElementSibling;

    // Toggle visibility of the associated list
    dropdown.classList.toggle('hidden');

    // Toggle arrow direction
    button.textContent = dropdown.classList.contains('hidden') ? '▼' : '▲';
  });
});*/
function toggleDropdown(row) {
  const dropdown = row.querySelector('.Service-item');
  dropdown.classList.toggle('hidden');
}



