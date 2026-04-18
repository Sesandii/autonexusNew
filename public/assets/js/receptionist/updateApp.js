document.addEventListener('DOMContentLoaded', () => {
    const updateForm = document.getElementById('updateForm');
    const branchSelect = document.getElementById('branch-id');
    const originalBranch = document.getElementById('original-branch').value;
    const branchWarning = document.getElementById('branch-warning');
    const cancelBtn = document.querySelector('.cancel-button');

    // Show warning when branch is changed
    branchSelect.addEventListener('change', () => {
        if (branchSelect.value !== originalBranch) {
            branchWarning.classList.add('show');
        } else {
            branchWarning.classList.remove('show');
        }
    });

    // Handle form submission
    updateForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(updateForm);
        
        // Validate required fields
        const requiredFields = ['service_id', 'branch_id', 'appointment_date', 'appointment_time'];
        let isValid = true;
        let errorMsg = '';

        requiredFields.forEach(field => {
            if (!formData.get(field)) {
                isValid = false;
                errorMsg += `- ${field.replace('_', ' ')} is required\n`;
            }
        });

        // Validate date is not in the past
        const selectedDate = new Date(formData.get('appointment_date'));
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (selectedDate < today) {
            isValid = false;
            errorMsg += '- Appointment date cannot be in the past\n';
        }

        if (!isValid) {
            alert('Please correct the following:\n' + errorMsg);
            return;
        }

        // Confirm if branch is being changed
        if (branchSelect.value !== originalBranch) {
            const confirmChange = confirm(
                'You are changing the branch for this appointment.\n\n' +
                'This will:\n' +
                '• Reset the assigned supervisor\n' +
                '• Set the status to "Requested"\n\n' +
                'Are you sure you want to continue?'
            );
            
            if (!confirmChange) {
                return;
            }
        }

        try {
            const response = await fetch(`${BASE_URL}/receptionist/appointments/update`, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                alert(result.message || 'Appointment updated successfully!');
                // Redirect to the day view with the updated date
                window.location.href = `${BASE_URL}/receptionist/appointments/day?date=${formData.get('appointment_date')}`;
            } else {
                alert('Failed to update appointment: ' + (result.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Update error:', error);
            alert('An error occurred while updating the appointment. Please try again.');
        }
    });

    // Cancel button
    cancelBtn.addEventListener('click', () => {
        if (confirm('Are you sure you want to cancel? Any unsaved changes will be lost.')) {
            const appointmentDate = document.getElementById('appointment-date').value;
            window.location.href = `${BASE_URL}/receptionist/appointments/day?date=${appointmentDate}`;
        }
    });
});