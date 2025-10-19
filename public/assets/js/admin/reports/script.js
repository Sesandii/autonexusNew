document.addEventListener('DOMContentLoaded', () => {
  // Helper to build a simple line chart
  function lineChart(ctx, labels, datasets, title) {
    return new Chart(ctx, {
      type: 'line',
      data: { labels, datasets },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { title: { display: true, text: title }, legend: { display: true } },
        interaction: { mode: 'index', intersect: false },
        scales: {
          x: { ticks: { autoSkip: true, maxTicksLimit: 12 } },
          y: { beginAtZero: true }
        }
      }
    });
  }

  // Helper to build a doughnut chart
  function doughnutChart(ctx, labels, data, title) {
    return new Chart(ctx, {
      type: 'doughnut',
      data: { labels, datasets: [{ data }] },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { title: { display: true, text: title }, legend: { position: 'right' } },
        cutout: '55%'
      }
    });
  }

  // ===== Service Trends (Line) - Dummy data =====
  const serviceCtx = document.getElementById('serviceChart').getContext('2d');
  const months = ['May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'];
  lineChart(serviceCtx, months, [
    { label: 'Oil Changes', data: [45, 52, 58, 62, 64, 64] },
    { label: 'Brake Service', data: [22, 26, 28, 30, 31, 35] },
    { label: 'Diagnostics', data: [15, 17, 19, 21, 23, 26] },
  ], 'Service Trends (Last 6 Months)');

  // ===== Revenue Trends (Line) - Dummy data =====
  const revenueCtx = document.getElementById('revenueChart').getContext('2d');
  lineChart(revenueCtx, months, [
    { label: 'Revenue (LKR x1,000)', data: [820, 910, 980, 1040, 1100, 1150] },
  ], 'Revenue Trends');

  // ===== Service Distribution (Doughnut) - Dummy data =====
  const distributionCtx = document.getElementById('distributionChart').getContext('2d');
  doughnutChart(distributionCtx,
    ['Oil Changes', 'Brake Service', 'Tire Service', 'Diagnostics'],
    [45, 25, 20, 10],
    'Service Distribution'
  );

  // ===== Simple tab switcher (if not already implemented) =====
  const tabs = Array.from(document.querySelectorAll('.tab-btn'));
  const panels = Array.from(document.querySelectorAll('.tab-content'));
  tabs.forEach(btn => {
    btn.addEventListener('click', () => {
      tabs.forEach(b => b.classList.remove('active'));
      panels.forEach(p => p.classList.remove('active'));
      btn.classList.add('active');
      const id = btn.getAttribute('data-tab');
      document.getElementById(id).classList.add('active');
    });
  });
});
