document.querySelectorAll('.clickable-row').forEach(row => {
    row.addEventListener('click', () => {
        const id = row.dataset.customerId;
        window.location.href = `${BASE_URL}/receptionist/customers/${id}`;
    });
});
