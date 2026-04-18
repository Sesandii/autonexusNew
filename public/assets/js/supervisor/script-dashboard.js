document.querySelectorAll('.toggle-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.toggle-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    document.querySelectorAll('.table-wrapper').forEach(sec => sec.classList.add('hidden'));
    
    const targetSection = document.getElementById(btn.dataset.target);
    if (targetSection) targetSection.classList.remove('hidden');

  });
});

function drawWeeklyChart() {
  const canvas = document.getElementById('weekly-chart');
  if (!canvas) return;

  const container = canvas.parentElement;
  canvas.width = container.clientWidth - 40; 

  const ctx = canvas.getContext('2d');
  const data = window.weeklyAppointments || [];

  if (data.length === 0) return;

  const labels = data.map(item => item.appt_date);
  const counts = data.map(item => parseInt(item.count));

  const padding = 40;
  const chartWidth = canvas.width - padding * 2;
  const chartHeight = canvas.height - padding * 2;
  const maxCount = Math.max(...counts, 5); 

  ctx.clearRect(0, 0, canvas.width, canvas.height);

  ctx.strokeStyle = "#eee";
  ctx.lineWidth = 1;

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

  ctx.strokeStyle = "#3498db"; 
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

  counts.forEach((count, i) => {
    const x = padding + (chartWidth / (counts.length - 1)) * i;
    const y = canvas.height - padding - (count / maxCount) * chartHeight;
    
    ctx.fillStyle = "#3498db";
    ctx.beginPath();
    ctx.arc(x, y, 5, 0, Math.PI * 2);
    ctx.fill();
    ctx.strokeStyle = "#fff";
    ctx.lineWidth = 2;
    ctx.stroke();

    ctx.fillStyle = "#333";
    ctx.textAlign = "center";
    ctx.fillText(labels[i], x, canvas.height - padding + 20);
  });
}

document.addEventListener('DOMContentLoaded', () => {
    drawWeeklyChart();
    
    window.addEventListener('resize', drawWeeklyChart);

    const deleteModal = document.getElementById('deleteModal');
    const cancelBtn = document.getElementById('cancelDelete');
    const confirmBtn = document.getElementById('confirmDelete');
    let formToSubmit = null;

    document.addEventListener('submit', function(e) {
        if (e.target.classList.contains('delete-form')) {
            e.preventDefault();
            formToSubmit = e.target;
            deleteModal.style.display = 'flex';
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

