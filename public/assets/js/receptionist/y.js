document.addEventListener("DOMContentLoaded", () => {

    // ── Elements ──────────────────────────────────────────────────────────────
    const phoneInput       = document.getElementById("phone");
    const customerIdInput  = document.getElementById("customer_id");
    const vehicleIdInput   = document.getElementById("vehicle_id");
    const customerNameInput= document.getElementById("customer_name");
    const emailInput       = document.getElementById("email");
    const vehicleContainer = document.getElementById("vehicle-container");
    const vehicleNumberInput = document.getElementById("vehicle_number");
    const appointmentSelect  = document.getElementById("appointment_id");
    const dateField        = document.getElementById("complaint_date");
    const timeField        = document.getElementById("complaint_time");

    // ── Auto-fill today's date & time ─────────────────────────────────────────
    const now = new Date();
    if (dateField) dateField.value = now.toISOString().split("T")[0];
    if (timeField) timeField.value = now.toTimeString().slice(0, 5);

    // ── Phone lookup ──────────────────────────────────────────────────────────
    let debounceTimer;

    phoneInput.addEventListener("keyup", () => {
        clearTimeout(debounceTimer);
        const phone = phoneInput.value.trim();

        if (phone.length < 3) return;

        debounceTimer = setTimeout(() => {
            fetch(`${BASE_URL}/receptionist/complaints/fetchByPhone?phone=${encodeURIComponent(phone)}`)
                .then(res => res.json())
                .then(response => {
                    if (!response.success || !response.data) {
                        clearCustomerFields();
                        return;
                    }

                    const customer = response.data;

                    // Fill customer fields
                    customerNameInput.value = `${customer.first_name} ${customer.last_name}`.trim();
                    emailInput.value        = customer.email ?? "";
                    customerIdInput.value   = customer.customer_id;

                    // Fill vehicle dropdown
                    populateVehicleDropdown(customer.vehicles ?? []);

                    // Fetch appointments for this customer
                    fetchAppointments(customer.customer_id);
                })
                .catch(err => console.error("Phone lookup error:", err));
        }, 400);
    });

    // ── Populate vehicle dropdown ─────────────────────────────────────────────
    function populateVehicleDropdown(vehicles) {
        vehicleContainer.innerHTML = "";

        if (vehicles.length === 0) {
            // No vehicles — show empty readonly input
            const input = document.createElement("input");
            input.type        = "text";
            input.name        = "vehicle";
            input.readOnly    = true;
            input.placeholder = "No vehicles found";
            vehicleContainer.appendChild(input);
            vehicleNumberInput.value = "";
            vehicleIdInput.value     = "";
            return;
        }

        const select = document.createElement("select");
        select.name = "vehicle";

        vehicles.forEach(v => {
            const opt         = document.createElement("option");
            opt.value         = `${v.make ?? ""} ${v.model ?? ""}`.trim();
            opt.textContent   = `${v.make ?? ""} ${v.model ?? ""} — ${v.license_plate ?? ""}`.trim();
            opt.dataset.plate = v.license_plate ?? "";
            opt.dataset.id    = v.vehicle_id;
            select.appendChild(opt);
        });

        // Set initial values from first option
        updateVehicleFields(select);

        // Update on change
        select.addEventListener("change", () => updateVehicleFields(select));

        vehicleContainer.appendChild(select);
    }

    function updateVehicleFields(select) {
        const selected = select.selectedOptions[0];
        vehicleNumberInput.value = selected?.dataset.plate ?? "";
        vehicleIdInput.value     = selected?.dataset.id    ?? "";
    }

    // ── Fetch & populate appointments ─────────────────────────────────────────
    function fetchAppointments(customerId) {
        appointmentSelect.innerHTML = '<option value="">None</option>';

        fetch(`${BASE_URL}/receptionist/complaints/fetchAppointments?customer_id=${encodeURIComponent(customerId)}`)
            .then(res => res.json())
            .then(response => {
                if (!response.success || !response.data?.length) return;

                response.data.forEach(appt => {
                    const opt       = document.createElement("option");
                    opt.value       = appt.appointment_id;
                    opt.textContent = appt.display_text ?? `Appointment #${appt.appointment_id}`;
                    appointmentSelect.appendChild(opt);
                });
            })
            .catch(err => console.error("Appointments fetch error:", err));
    }

    // ── Clear fields on no match ──────────────────────────────────────────────
    function clearCustomerFields() {
        customerNameInput.value  = "";
        emailInput.value         = "";
        customerIdInput.value    = "";
        vehicleIdInput.value     = "";
        vehicleNumberInput.value = "";
        vehicleContainer.innerHTML = '<input type="text" name="vehicle" readonly placeholder="Auto-filled">';
        appointmentSelect.innerHTML = '<option value="">None</option>';
    }

    // ── Form validation on submit ─────────────────────────────────────────────
    document.querySelector("form").addEventListener("submit", (e) => {
        if (!customerIdInput.value) {
            e.preventDefault();
            alert("Please enter a valid phone number to find the customer.");
            return;
        }

        if (!vehicleIdInput.value) {
            e.preventDefault();
            alert("Please select a vehicle.");
        }
    });

});