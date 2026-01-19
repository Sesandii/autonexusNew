const monthYear = document.getElementById("month-year");
const calendarGrid = document.getElementById("calendar-grid");
const prevBtn = document.getElementById("prev");
const nextBtn = document.getElementById("next");

let today = new Date();
let currentMonth = today.getMonth();
let currentYear = today.getFullYear();

function renderCalendar(month, year) {
    // Display month and year
    monthYear.textContent = new Date(year, month).toLocaleString("default", { month: "long", year: "numeric" });

    // Remove old dates
    calendarGrid.querySelectorAll(".date").forEach(el => el.remove());

    let firstDay = new Date(year, month, 1).getDay();
    let daysInMonth = new Date(year, month + 1, 0).getDate();

    // Empty cells for first week
    for (let i = 0; i < firstDay; i++) {
        let emptyCell = document.createElement("div");
        calendarGrid.appendChild(emptyCell);
    }

    // Dates
    for (let day = 1; day <= daysInMonth; day++) {
        let dateEl = document.createElement("div");
        dateEl.classList.add("date");
        dateEl.textContent = day;

        // Highlight today
        if (day === today.getDate() && month === today.getMonth() && year === today.getFullYear()) {
            dateEl.classList.add("today");
        }

        // Click to go to dayAppointment.html with clicked date
        dateEl.addEventListener("click", () => {
            const clickedDate = `${year}-${String(month + 1).padStart(2, "0")}-${String(day).padStart(2, "0")}`;
            // Assuming you added BASE_URL in your view
window.location.href = `${BASE_URL}/receptionist/appointments/day?date=${clickedDate}`;

        });

        calendarGrid.appendChild(dateEl);
    }
}

// Navigation buttons
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


