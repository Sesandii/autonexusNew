<<<<<<< HEAD
document.addEventListener("DOMContentLoaded", function () {
    const userName = document.getElementById("user-name");
    const dropdown = document.getElementById("dropdown");

    userName.addEventListener("click", function () {
      dropdown.classList.toggle("hidden");
    });

    // Close dropdown when clicking outside
    document.addEventListener("click", function (event) {
      if (!userName.contains(event.target) && !dropdown.contains(event.target)) {
        dropdown.classList.add("hidden");
      }
    });
  });

  document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById("search");
    const resultsContainer = document.getElementById("search-results");
  
    // Define searchable items (name + id of section)
    const searchItems = [
      { name: "Complaints Section", target: "complaints-sec" }
    ];
  
    // Show results on typing
    searchInput.addEventListener("input", () => {
      const query = searchInput.value.toLowerCase();
      resultsContainer.innerHTML = "";
  
      if (!query) {
        resultsContainer.classList.add("hidden");
        return;
      }
  
      const matches = searchItems.filter(item => item.name.toLowerCase().includes(query));
  
      if (matches.length > 0) {
        matches.forEach(item => {
          const li = document.createElement("li");
          li.textContent = item.name;
          li.addEventListener("click", () => {
            // Scroll to section
            document.getElementById(item.target).scrollIntoView({ behavior: "smooth" });
            resultsContainer.classList.add("hidden");
            searchInput.value = "";
          });
          resultsContainer.appendChild(li);
        });
        resultsContainer.classList.remove("hidden");
      } else {
        resultsContainer.classList.add("hidden");
      }
    });
  
    // Hide dropdown if click outside
    document.addEventListener("click", (e) => {
      if (!e.target.closest(".search-wrapper")) {
        resultsContainer.classList.add("hidden");
      }
    });
  });
=======
// Toggle between Today’s Appointments and In-Progress Vehicles
document.querySelectorAll('.toggle-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    // Remove active class from all buttons
    document.querySelectorAll('.toggle-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    // Hide all sections
    document.querySelectorAll('.appointments').forEach(sec => sec.classList.add('hidden'));
    
    // Show the selected section
    const targetSection = document.getElementById(btn.dataset.target);
    if (targetSection) targetSection.classList.remove('hidden');

    // Draw the chart if showing "Today’s Appointments"
    if (btn.dataset.target === 'appointments') {
      drawAppointmentsChart();
    }
  });
});

// Function to draw weekly appointments chart
function drawWeeklyChart() {
  const canvas = document.getElementById('weekly-chart');
  if (!canvas) return;

  const ctx = canvas.getContext('2d');
  const data = window.weeklyAppointments || [];

  const labels = data.map(item => item.appt_date);
  const counts = data.map(item => parseInt(item.count));

  const padding = 30;
  const chartWidth = canvas.width - padding * 2;
  const chartHeight = canvas.height - padding * 2;
  const maxCount = Math.max(...counts, 1);

  ctx.clearRect(0, 0, canvas.width, canvas.height);

  // Y-axis
  ctx.strokeStyle = "#ccc";
  ctx.beginPath();
  ctx.moveTo(padding, padding);
  ctx.lineTo(padding, canvas.height - padding);
  ctx.stroke();

  // X-axis
  ctx.beginPath();
  ctx.moveTo(padding, canvas.height - padding);
  ctx.lineTo(canvas.width - padding, canvas.height - padding);
  ctx.stroke();

  // Draw line connecting points
  ctx.strokeStyle = "#FF5722"; // nice orange color
  ctx.lineWidth = 2;
  ctx.beginPath();
  counts.forEach((count, i) => {
    const x = padding + (chartWidth / (counts.length - 1)) * i;
    const y = canvas.height - padding - (count / maxCount) * chartHeight;
    if (i === 0) ctx.moveTo(x, y);
    else ctx.lineTo(x, y);
  });
  ctx.stroke();

  // Draw points on top of the line
  ctx.fillStyle = "#FF5722";
  counts.forEach((count, i) => {
    const x = padding + (chartWidth / (counts.length - 1)) * i;
    const y = canvas.height - padding - (count / maxCount) * chartHeight;
    ctx.beginPath();
    ctx.arc(x, y, 4, 0, Math.PI * 2);
    ctx.fill();
  });

  // X labels
  ctx.fillStyle = "#000";
  ctx.textAlign = "center";
  ctx.textBaseline = "top";
  labels.forEach((label, i) => {
    const x = padding + (chartWidth / (labels.length - 1)) * i;
    ctx.fillText(label, x, canvas.height - padding + 5);
  });

  // Y labels
  ctx.textAlign = "right";
  ctx.textBaseline = "middle";
  for (let i = 0; i <= maxCount; i++) {
    const y = canvas.height - padding - (i / maxCount) * chartHeight;
    ctx.fillText(i, padding - 5, y);
  }
}

document.addEventListener('DOMContentLoaded', drawWeeklyChart);
>>>>>>> bc21bfd776db2147cd644a47aeb727bb8ca3d276
