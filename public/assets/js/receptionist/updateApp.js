// Select elements
const saveBtn = document.querySelector(".save-button");
const cancelBtn = document.querySelector(".cancel-button");

// Cancel button
cancelBtn.addEventListener("click", () => {
    window.location.href = `${BASE_URL}/receptionist/appointments`;
});

// Save button
saveBtn.addEventListener("click", () => {

    // Get values
    const customer = document.getElementById("customer").value.trim();
    const phone = document.getElementById("phone").value.trim();
    const vehicleNumber = document.getElementById("vehicle-number").value.trim();
    const vehicle = document.getElementById("vehicle").value.trim();
    const service = document.getElementById("service").value;
    const branch = document.getElementById("branch").value;
    const date = document.getElementById("Date").value;
    const time = document.getElementById("Time").value;
    const status = document.getElementById("status").value;
    const notes = document.getElementById("notes").value;
    const assignedTo = document.getElementById("assigned_to").value;

    // Validation
    if (!customer || !phone || !vehicleNumber || !vehicle || !service || !branch || !date || !time || !status) {
        alert("Please fill in all required details.");
        return;
    }

    const data = new FormData();
    data.append("appointment_id", saveBtn.dataset.id);
    data.append("service_id", service);
    data.append("branch_id", branch);
    data.append("appointment_date", date);
    data.append("appointment_time", time);
    data.append("status", status);
    data.append("notes", notes);
    data.append("assigned_to", assignedTo);

    fetch(`${BASE_URL}/receptionist/appointments/update`, {
        method: "POST",
        body: data
    })
    .then(res => res.json())
    .then(res => {
        alert(res.message);

        if (res.success) {
            // Redirect back to day view with selected date
            window.location.href = `${BASE_URL}/receptionist/appointments/day?date=${date}`;
        }
    })
    .catch(err => {
        console.error("Update error:", err);
        alert("Something went wrong while updating.");
    });
});