/**
 * performance.js
 * Handles loading stats tiles and team performance table
 */

document.addEventListener('DOMContentLoaded', () => {
    // Load stats tiles
    async function loadStats() {
        const resp = await fetch(`${BASE_URL}/manager/performance/stats`);
        const stats = await resp.json();

        document.getElementById('completed-jobs').textContent = stats.completed_jobs;

        document.getElementById('customer-satisfaction').textContent =
            stats.customer_satisfaction !== null
                ? stats.customer_satisfaction + ' ★'
                : 'N/A';

        document.getElementById('avg-service-time').textContent =
            stats.avg_service_time + ' min';

        document.getElementById('return-rate').textContent =
            stats.return_rate + '%';

        document.getElementById('revenue').textContent =
            'Rs. ' + Number(stats.revenue).toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
    }

    // Load team performance table
    async function loadTeamData() {
        const resp = await fetch(`${BASE_URL}/manager/performance/team`);
        const data = await resp.json();

        const tbody = document.getElementById('team-data');
        tbody.innerHTML = '';

        if (!data.length) {
            tbody.innerHTML = '<tr><td colspan="4">No data available</td></tr>';
            return;
        }

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

    // Optional: Team table card click
    const card = document.querySelector('.card.team-table');
    if (card) {
        card.addEventListener('click', () => {
            const url = card.getAttribute('data-url');
            if (url) {
                window.location.href = url;
            }
        });
    }

   document.addEventListener('DOMContentLoaded', () => {
    const rows = document.querySelectorAll('.clickable-row');
    rows.forEach(row => {
        row.addEventListener('click', () => {
            const url = row.getAttribute('data-url');
            if (url) window.location.href = url;
        });
        row.style.cursor = 'pointer';
    });
});



});
