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

document.addEventListener('DOMContentLoaded', function() {
  const deleteModal = document.getElementById('deleteModal');
  const cancelBtn = document.getElementById('cancelDelete');
  const confirmBtn = document.getElementById('confirmDelete');
  let formToSubmit = null;

  // Listen for clicks on the document to catch forms even if tables toggle
  document.addEventListener('submit', function(e) {
      if (e.target.classList.contains('delete-form')) {
          e.preventDefault(); // Stop immediate PHP execution
          formToSubmit = e.target;
          deleteModal.classList.add('show');
      }
  });

  // Close logic
  const closeModal = () => {
      deleteModal.classList.remove('show');
      formToSubmit = null;
  };

  cancelBtn.addEventListener('click', closeModal);

  confirmBtn.addEventListener('click', function() {
      if (formToSubmit) {
          formToSubmit.submit(); // Now the PHP delete actually runs
      }
  });

  // Close if clicking the dark background
  deleteModal.addEventListener('click', function(e) {
      if (e.target === deleteModal) closeModal();
  });
});