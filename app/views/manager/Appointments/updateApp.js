function updateAssignment() {
    const appointmentId = document.getElementById('appointment-id').value;
    const assignedTo = document.getElementById('assigned-to').value;
    const notes = document.getElementById('notes').value; 

    // Create form data
    const formData = new FormData();
    formData.append('appointment_id', appointmentId);
    formData.append('assigned_to', assignedTo);
     formData.append('notes', notes); 

    // Send update request
    fetch(BASE_URL + '/manager/appointments/update', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Appointment updated successfully!');
            window.location.href = `${BASE_URL}/manager/appointments/day?date=${clickedDate}`;
           } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the appointment');
    });
}

// Optional: Add keyboard shortcut for save (Ctrl+S)
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.key === 's') {
        e.preventDefault();
        updateAssignment();
    }
});