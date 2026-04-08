// Toggle between Today’s Appointments and In-Progress Vehicles
document.querySelectorAll('.toggle-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    // 1. Update button active states
    document.querySelectorAll('.toggle-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    // 2. Hide all table wrappers (Note: changed selector from .appointments to .table-wrapper)
    document.querySelectorAll('.table-wrapper').forEach(sec => sec.classList.add('hidden'));
    
    // 3. Show the selected section
    const targetSection = document.getElementById(btn.dataset.target);
    if (targetSection) targetSection.classList.remove('hidden');

    // Note: Removed drawAppointmentsChart call here because the Weekly Chart 
    // is now permanently visible at the bottom of the page.
  });
});

// Function to draw weekly appointments trend chart
function drawWeeklyChart() {
  const canvas = document.getElementById('weekly-chart');
  if (!canvas) return;

  // Make canvas responsive to its container width
  const container = canvas.parentElement;
  canvas.width = container.clientWidth - 40; // Account for padding

  const ctx = canvas.getContext('2d');
  const data = window.weeklyAppointments || [];

  if (data.length === 0) return;

  const labels = data.map(item => item.appt_date);
  const counts = data.map(item => parseInt(item.count));

  const padding = 40;
  const chartWidth = canvas.width - padding * 2;
  const chartHeight = canvas.height - padding * 2;
  const maxCount = Math.max(...counts, 5); // Default scale to at least 5

  ctx.clearRect(0, 0, canvas.width, canvas.height);

  // Axis styling
  ctx.strokeStyle = "#eee";
  ctx.lineWidth = 1;

  // Y-axis & Grid lines
  for (let i = 0; i <= maxCount; i++) {
    const y = canvas.height - padding - (i / maxCount) * chartHeight;
    ctx.beginPath();
    ctx.moveTo(padding, y);
    ctx.lineTo(canvas.width - padding, y);
    ctx.stroke();
    
    ctx.fillStyle = "#888";
    ctx.textAlign = "right";
    ctx.fillText(i, padding - 10, y + 3);
  }

  // Draw line connecting points
  ctx.strokeStyle = "#3498db"; // Modern blue to match UCSC/AutoNexus theme
  ctx.lineWidth = 3;
  ctx.lineJoin = "round";
  ctx.beginPath();
  
  counts.forEach((count, i) => {
    const x = padding + (chartWidth / (counts.length - 1)) * i;
    const y = canvas.height - padding - (count / maxCount) * chartHeight;
    if (i === 0) ctx.moveTo(x, y);
    else ctx.lineTo(x, y);
  });
  ctx.stroke();

  // Draw points and X-labels
  counts.forEach((count, i) => {
    const x = padding + (chartWidth / (counts.length - 1)) * i;
    const y = canvas.height - padding - (count / maxCount) * chartHeight;
    
    // Point
    ctx.fillStyle = "#3498db";
    ctx.beginPath();
    ctx.arc(x, y, 5, 0, Math.PI * 2);
    ctx.fill();
    ctx.strokeStyle = "#fff";
    ctx.lineWidth = 2;
    ctx.stroke();

    // Label
    ctx.fillStyle = "#333";
    ctx.textAlign = "center";
    ctx.fillText(labels[i], x, canvas.height - padding + 20);
  });
}

// Handle Modal and Chart on Load
document.addEventListener('DOMContentLoaded', () => {
    drawWeeklyChart();
    
    // Resize chart on window resize
    window.addEventListener('resize', drawWeeklyChart);

    const deleteModal = document.getElementById('deleteModal');
    const cancelBtn = document.getElementById('cancelDelete');
    const confirmBtn = document.getElementById('confirmDelete');
    let formToSubmit = null;

    // Use event delegation for delete forms
    document.addEventListener('submit', function(e) {
        if (e.target.classList.contains('delete-form')) {
            e.preventDefault();
            formToSubmit = e.target;
            deleteModal.style.display = 'flex'; // Use display flex for center alignment
            deleteModal.classList.add('show');
        }
    });

    const closeModal = () => {
        deleteModal.style.display = 'none';
        deleteModal.classList.remove('show');
        formToSubmit = null;
    };

    if(cancelBtn) cancelBtn.addEventListener('click', closeModal);
    
    if(confirmBtn) {
        confirmBtn.addEventListener('click', function() {
            if (formToSubmit) formToSubmit.submit();
        });
    }

    deleteModal.addEventListener('click', (e) => {
        if (e.target === deleteModal) closeModal();
    });
});