// public/js/performanceChart.js

fetch('/autonexus/manager/performance/jobsByDay')
  .then(response => response.json())
  .then(data => {
    const labels = data.map(item => item.day);
    const totals = data.map(item => item.total);

    const ctx = document.getElementById('jobsByDayChart').getContext('2d');
    const chart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Completed Jobs',
          data: totals,
          backgroundColor: 'rgba(54, 162, 235, 0.6)',
          borderColor: 'rgba(54, 162, 235, 1)',
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: false },
          title: {
            display: true,
            text: 'Jobs Completed by Day of Week'
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            stepSize: 1
          }
        }
      }
    });
  })
  .catch(err => console.error('Error loading chart data:', err));
