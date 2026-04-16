document.addEventListener("DOMContentLoaded", function () {
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

  const toggleBtns = document.querySelectorAll('.toggle-btn');
  toggleBtns.forEach(btn => {
      btn.addEventListener('click', () => {
          toggleBtns.forEach(b => b.classList.remove('active'));
          btn.classList.add('active');

          document.querySelectorAll('.table-wrapper').forEach(sec => sec.classList.add('hidden'));

          const targetSection = document.getElementById(btn.dataset.target);
          if (targetSection) targetSection.classList.remove('hidden');
      });
  });

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