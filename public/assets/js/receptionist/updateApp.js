// Branch change warning
const branchSelect    = document.querySelector('select[name="branch_id"]');
const originalBranch  = document.getElementById('original-branch')?.value;
const branchWarning   = document.getElementById('branch-warning');

if (branchSelect && branchWarning) {
    branchSelect.addEventListener('change', () => {
        branchWarning.style.display =
            branchSelect.value !== originalBranch ? 'block' : 'none';
    });
}

// Form submit via fetch
const form = document.getElementById('updateForm');

form.addEventListener('submit', (e) => {
    e.preventDefault();

    const data = new FormData(form); // ✅ grabs ALL named inputs automatically

    fetch(`${BASE_URL}/receptionist/appointments/update`, {
        method: 'POST',
        body: data
    })
    .then(res => res.json())
    .then(res => {
        alert(res.message);

        if (res.success) {
            const date = form.querySelector('[name="appointment_date"]').value;
            window.location.href = `${BASE_URL}/receptionist/appointments/day?date=${date}`;
        }
    })
    .catch(err => {
        console.error('Update error:', err);
        alert('Something went wrong while updating.');
    });
});