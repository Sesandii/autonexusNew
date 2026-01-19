document.addEventListener("DOMContentLoaded", () => {

    const saveBtn = document.querySelector(".save-button");
    const cancelBtn = document.querySelector(".cancel-button");
    const customerInput = document.querySelector('input[name="customer_name"]');
    const phoneInput = document.querySelector('input[name="phone"]');
    const emailInput = document.querySelector('input[name="email"]');
    const vehicleContainer = document.querySelector('#vehicle-container');
    const vehicleNumberInput = document.querySelector('input[name="vehicle_number"]');
    const statusSelect = document.querySelector('select[name="status"]');

    // Hidden inputs for foreign keys
    const customerIdInput = document.querySelector('#customer_id');
    const userIdInput = document.querySelector('#user_id');
    const vehicleIdInput = document.querySelector('#vehicle_id');

    // Save validation
    saveBtn.addEventListener("click", (e) => {
        const vehicleInput = vehicleContainer.querySelector('[name="vehicle"]');

        if (!customerInput.value.trim() || !phoneInput.value.trim() || !vehicleInput.value.trim() || !vehicleNumberInput.value.trim() || !statusSelect.value.trim() || !customerIdInput.value || !userIdInput.value || !vehicleIdInput.value) {
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
        })
        .catch(err => console.error("Fetch error:", err));
});


    /*function populateVehicleDropdown(vehicles) {
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
        }

        const dropdown = document.createElement("select");
        dropdown.name = "vehicle";
        dropdown.className = "form-control";

        vehicles.forEach(v => {
            const opt = document.createElement("option");
            opt.value = `${v.make ?? ''} ${v.model ?? ''}`.trim();
            opt.textContent = `${v.make ?? ''} ${v.model ?? ''}`.trim();
            opt.dataset.plate = v.license_plate ?? '';
            opt.dataset.vehicleId = v.vehicle_id; // hidden vehicle_id
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
    }*/
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
