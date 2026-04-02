document.addEventListener('DOMContentLoaded', () => {
    const complaints = document.querySelectorAll('.complaint');
    const searchInput = document.querySelector('input[placeholder="Search complaints..."]');
    const statusFilter = document.querySelector('select:nth-of-type(1)');
    const priorityFilter = document.querySelector('select:nth-of-type(2)');

    // Make complaints clickable
    complaints.forEach(el => {
        el.style.cursor = 'pointer';
        el.addEventListener('click', () => {
            window.location.href = el.getAttribute('data-url');
        });
    });

    // Filter function
    function filterComplaints() {
        const searchText = searchInput.value.toLowerCase();
        const statusText = statusFilter.value.toLowerCase();
        const priorityText = priorityFilter.value.toLowerCase();

        complaints.forEach(comp => {
            const desc = comp.querySelector('p').textContent.toLowerCase();
            const title = comp.querySelector('h3').textContent.toLowerCase();
            const status = comp.getAttribute('data-status').toLowerCase();
            const priority = comp.getAttribute('data-priority').toLowerCase();

            const matchesSearch = desc.includes(searchText) || title.includes(searchText);
            const matchesStatus = statusText === 'all statuses' || status === statusText;
            const matchesPriority = priorityText === 'all priorities' || priority === priorityText;

            comp.style.display = (matchesSearch && matchesStatus && matchesPriority) ? 'block' : 'none';
        });
    }

    // Trigger filter on input/change
    searchInput.addEventListener('input', filterComplaints);
    statusFilter.addEventListener('change', filterComplaints);
    priorityFilter.addEventListener('change', filterComplaints);
});
