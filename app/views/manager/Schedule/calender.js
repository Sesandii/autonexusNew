const monthYear = document.getElementById("month-year");
const calendarGrid = document.getElementById("calendar-grid");
const prevBtn = document.getElementById("prev");
const nextBtn = document.getElementById("next");

let today = new Date();
let currentMonth = today.getMonth();
let currentYear = today.getFullYear();

function renderCalendar(month, year) {
  monthYear.textContent = new Date(year, month).toLocaleString("default", { month: "long", year: "numeric" });
  
  // remove old dates
  calendarGrid.querySelectorAll(".date").forEach(el => el.remove());

  let firstDay = new Date(year, month, 1).getDay();
  let daysInMonth = new Date(year, month + 1, 0).getDate();

  // blank spaces before 1st
  for (let i = 0; i < firstDay; i++) {
    let emptyCell = document.createElement("div");
    calendarGrid.appendChild(emptyCell);
  }

  // dates
  for (let day = 1; day <= daysInMonth; day++) {
    let dateEl = document.createElement("div");
    dateEl.classList.add("date");
    dateEl.textContent = day;

    // highlight today
    if (day === today.getDate() && month === today.getMonth() && year === today.getFullYear()) {
      dateEl.classList.add("today");
    }

    // click to go to another page
    dateEl.addEventListener("click", () => {
      window.location.href = "daySchedule.html";
    });

    calendarGrid.appendChild(dateEl);
  }
}

prevBtn.addEventListener("click", () => {
  currentMonth--;
  if (currentMonth < 0) {
    currentMonth = 11;
    currentYear--;
  }
  renderCalendar(currentMonth, currentYear);
});

nextBtn.addEventListener("click", () => {
  currentMonth++;
  if (currentMonth > 11) {
    currentMonth = 0;
    currentYear++;
  }
  renderCalendar(currentMonth, currentYear);
});

renderCalendar(currentMonth, currentYear);
