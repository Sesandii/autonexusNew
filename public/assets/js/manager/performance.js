/**
 * performanceStats.js
 * Handles loading of stats tiles and team performance table
 */

async function loadStats() {
    // Fetch stats from the server
    const resp = await fetch(`${BASE_URL}/manager/performance/stats`);
    const stats = await resp.json();

    // Update Completed Jobs
    document.getElementById('completed-jobs').textContent = stats.completed_jobs;

    // Update Customer Satisfaction
    document.getElementById('customer-satisfaction').textContent =
        stats.customer_satisfaction !== null
            ? stats.customer_satisfaction + ' ★'
            : 'N/A';

    // Update Average Service Time
    document.getElementById('avg-service-time').textContent =
        stats.avg_service_time + ' min';

    // Update Return Rate
    document.getElementById('return-rate').textContent =
        stats.return_rate + '%';

    // Update Revenue
    document.getElementById('revenue').textContent =
        'Rs. ' + Number(stats.revenue).toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
}

async function loadTeamData() {
    // Fetch team performance data
    const resp = await fetch(`${BASE_URL}/manager/performance/team`);
    const data = await resp.json();

    const tbody = document.getElementById('team-data');
    tbody.innerHTML = '';

    if (!data.length) {
        tbody.innerHTML = '<tr><td colspan="4">No data available</td></tr>';
        return;
    }

    // Populate table rows
    data.forEach(member => {
        const rating =
            member.customer_satisfaction !== null
                ? member.customer_satisfaction + ' ★'
                : 'N/A';

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${member.first_name} ${member.last_name}</td>
            <td>${member.specialization}</td>
            <td>${member.completed_jobs}</td>
            <td>${rating}</td>
        `;
        tbody.appendChild(tr);
    });
}

// Initial load
loadStats();
loadTeamData();

// Auto refresh every 30 seconds
setInterval(() => {
    loadStats();
    loadTeamData();
}, 30000);


document.addEventListener('DOMContentLoaded', () => {
    const rows = document.querySelectorAll('.clickable-row');

    rows.forEach(row => {
        row.addEventListener('click', () => {
            const url = row.dataset.url;
            if (url) {
                window.location.href = url;
            }
        });
    });
});

