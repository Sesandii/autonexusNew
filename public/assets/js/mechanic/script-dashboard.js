document.addEventListener("DOMContentLoaded", function () {
  // 1. User Dropdown Logic
  const userName = document.getElementById("user-name");
  const dropdown = document.getElementById("dropdown");

  if (userName && dropdown) {
      userName.addEventListener("click", function () {
          dropdown.classList.toggle("hidden");
      });

      document.addEventListener("click", function (event) {
          if (!userName.contains(event.target) && !dropdown.contains(event.target)) {
              dropdown.classList.add("hidden");
          }
      });
  }

  // 2. Table Section Toggle (Today’s Appointments vs In-Progress)
  const toggleBtns = document.querySelectorAll('.toggle-btn');
  toggleBtns.forEach(btn => {
      btn.addEventListener('click', () => {
          // Update button active states
          toggleBtns.forEach(b => b.classList.remove('active'));
          btn.classList.add('active');

          // Hide all table wrappers
          document.querySelectorAll('.table-wrapper').forEach(sec => sec.classList.add('hidden'));
          
          // Show the selected section
          const targetSection = document.getElementById(btn.dataset.target);
          if (targetSection) targetSection.classList.remove('hidden');
      });
  });

  // 3. Assignment Filter (Mine vs Others)
  const assignmentFilter = document.getElementById("assignmentFilter");
  const rows = document.querySelectorAll(".appointment-row");

  if (assignmentFilter) {
      assignmentFilter.addEventListener("change", function () {
          const filterValue = this.value;

          rows.forEach(row => {
              const rowType = row.getAttribute("data-assignment");

              if (filterValue === "all") {
                  row.style.display = "";
              } else if (filterValue === rowType) {
                  row.style.display = "";
              } else {
                  row.style.display = "none";
              }
          });
      });
  }
});