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

const tabs = document.querySelectorAll(".tab-item");
const contents = document.querySelectorAll(".tab-content");

tabs.forEach(tab => {
  tab.addEventListener("click", () => {
    // remove active classes
    tabs.forEach(t => t.classList.remove("active"));
    contents.forEach(c => c.classList.remove("active"));

    // activate clicked tab
    tab.classList.add("active");
    document.getElementById(tab.dataset.tab).classList.add("active");
  });
});

function toggleDropdown(row) {
  const dropdown = row.querySelector('.Service-item');
  dropdown.classList.toggle('hidden');
}



