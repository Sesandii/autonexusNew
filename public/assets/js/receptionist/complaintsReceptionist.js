   console.log("BASE_URL =", BASE_URL);
   // Filter function

    // Initialize everything when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        // Make complaints clickable
        document.querySelectorAll('.complaint').forEach(item => {
            item.style.cursor = 'pointer';
            item.addEventListener('click', (e) => {
                // Don't navigate if clicking on delete link or its children
                if (e.target.closest('.actions') || e.target.closest('a')) {
                    return;
                }
                const url = item.getAttribute('data-url');
                if (url) {
                    window.location.href = url;
                }
            });
        });
        
        // Allow pressing Enter in search input
        const searchInput = document.getElementById('search-input');
        if (searchInput) {
            searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    filterComplaints();
                }
            });
        }
    });
