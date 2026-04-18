document.addEventListener("DOMContentLoaded", () => {

    const saveBtn = document.querySelector(".save-button");
    const cancelBtn = document.querySelector(".cancel-button");
    const customerInput = document.querySelector('input[name="customer_name"]');
    const phoneInput = document.querySelector('input[name="phone"]');
    const emailInput = document.querySelector('input[name="email"]');
    const vehicleContainer = document.querySelector('#vehicle-container');
    const vehicleNumberInput = document.querySelector('input[name="vehicle_number"]');
    const statusSelect = document.querySelector('select[name="status"]');
    const appointmentSelect = document.getElementById('appointment_id');

    // Hidden inputs for foreign keys
    const customerIdInput = document.querySelector('#customer_id');

    const vehicleIdInput = document.querySelector('#vehicle_id');

    // Save validation
    saveBtn.addEventListener("click", (e) => {
        const vehicleInput = vehicleContainer.querySelector('[name="vehicle"]');

        if (!customerInput.value.trim() || !phoneInput.value.trim() || !vehicleInput.value.trim() || !vehicleNumberInput.value.trim() || !statusSelect.value.trim() || !customerIdInput.value || !vehicleIdInput.value) {
            alert("Please fill in all the details.");
            e.preventDefault();
            return;
        }

        alert("Complaint saved successfully!");
    });

    // Cancel
    cancelBtn.addEventListener("click", () => {
        window.location.href = BASE_URL + "/receptionist/complaints";
    });

    // Auto-fill Date & Time
    const dateField = document.querySelector('input[name="complaint_date"]');
    const timeField = document.querySelector('input[name="complaint_time"]');
    const now = new Date();
    if (dateField) dateField.value = now.toISOString().split("T")[0];
    if (timeField) timeField.value = now.toTimeString().slice(0,5);

    // Phone lookup
    phoneInput.addEventListener("keyup", () => {
        const phone = phoneInput.value.trim();
        if (phone.length < 3) return;

        fetch(`${BASE_URL}/receptionist/complaints/fetchByPhone?phone=${encodeURIComponent(phone)}`)
            .then(res => res.json())
            .then(response => {
                if (!response.success || !response.data) return;

                const customer = response.data;

                customerInput.value = `${customer.first_name} ${customer.last_name}`.trim();
                emailInput.value = customer.email ?? "";
                customerIdInput.value = customer.customer_id;

                populateVehicleDropdown(customer.vehicles ?? []);
                
                // Fetch and populate appointments
                fetchAppointments(customer.customer_id);
            })
            .catch(err => console.error("Fetch error:", err));
    });

    // Function to fetch appointments
    function fetchAppointments(customerId) {
        fetch(`${BASE_URL}/receptionist/complaints/fetchAppointments?customer_id=${customerId}`)
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    populateAppointmentDropdown(response.data);
                }
            })
            .catch(err => console.error("Error fetching appointments:", err));
    }

    // Function to populate appointment dropdown
    function populateAppointmentDropdown(appointments) {
    if (!appointmentSelect) return;

    appointmentSelect.innerHTML = '<option value="">None</option>';

    if (!appointments || appointments.length === 0) return;

    appointments.forEach(app => {
        const option = document.createElement('option');
        option.value = app.appointment_id;
        option.textContent = app.display_text;
        appointmentSelect.appendChild(option);
    });
}

    function populateVehicleDropdown(vehicles) {
        vehicleContainer.innerHTML = "";

        if (vehicles.length === 0) {
            const input = document.createElement("input");
            input.type = "text";
            input.name = "vehicle";
            input.className = "form-control";
            vehicleContainer.appendChild(input);
            vehicleNumberInput.value = "";
            vehicleIdInput.value = "";
            return;
        } else {
            const dropdown = document.createElement("select");
            dropdown.name = "vehicle";
            dropdown.className = "form-control";

            vehicles.forEach(v => {
                const opt = document.createElement("option");
                opt.value = `${v.make ?? ''} ${v.model ?? ''}`.trim();
                opt.textContent = `${v.make ?? ''} ${v.model ?? ''}`.trim();
                opt.dataset.plate = v.license_plate ?? '';
                opt.dataset.vehicleId = v.vehicle_id;
                dropdown.appendChild(opt);
            });

            dropdown.selectedIndex = 0;
            vehicleNumberInput.value = dropdown.selectedOptions[0].dataset.plate;
            vehicleIdInput.value = dropdown.selectedOptions[0].dataset.vehicleId;

            dropdown.addEventListener("change", () => {
                vehicleNumberInput.value = dropdown.selectedOptions[0].dataset.plate;
                vehicleIdInput.value = dropdown.selectedOptions[0].dataset.vehicleId;
            });

            vehicleContainer.appendChild(dropdown);
        }
    }

});