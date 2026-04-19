function updateAssignment() {
    const appointmentId = document.getElementById('appointment-id').value;
    const assignedTo = document.getElementById('assigned-to').value;
    const notes = document.getElementById('notes').value;

    const formData = new FormData();
    formData.append('appointment_id', appointmentId);
    formData.append('assigned_to', assignedTo);
    formData.append('notes', notes);

    fetch(BASE_URL + '/manager/appointments/update', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Appointment updated successfully!');
            window.location.href = `${BASE_URL}/manager/appointments`;
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the appointment');
    });
}