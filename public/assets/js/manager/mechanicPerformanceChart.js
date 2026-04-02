document.addEventListener('DOMContentLoaded', () => {
  const rawData = window.mechanicChartData.jobsByDay;
  const selectedMonth = window.mechanicChartData.month;

  const [year, monthNumber] = selectedMonth.split('-');
  const daysInMonth = new Date(year, monthNumber, 0).getDate();

  const labels = [];
  const values = [];
  const dataMap = {};

  // Map backend results
  rawData.forEach(row => {
    dataMap[row.day] = row.total;
  });

  // Fill 1 → 30/31
  for (let day = 1; day <= daysInMonth; day++) {
    labels.push(day.toString());
    values.push(dataMap[day] ?? 0);
  }

  const ctx = document.getElementById('jobsByDayChart');

  // Destroy old chart if exists (future-proof)
  if (window.jobsChart) {
    window.jobsChart.destroy();
  }

  window.jobsChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: 'Jobs Completed',
        data: values,
        tension: 0.35,
        borderWidth: 2,
        pointRadius: 4,
        pointHoverRadius: 6,
        borderColor: 'rgba(54, 162, 235, 1)',
        pointBackgroundColor: 'rgba(54, 162, 235, 1)',
        fill: false
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        x: {
          title: {
            display: true,
            text: 'Day of Month'
          }
        },
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: 'Jobs'
          },
          ticks: {
            precision: 0
          }
        }
      }
    }
  });
});
