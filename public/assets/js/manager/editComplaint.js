document.addEventListener("DOMContentLoaded", () => {

    const customerInput     = document.getElementById("customer_name");
    const phoneInput        = document.getElementById("phone");
    const emailInput        = document.getElementById("email");
    const vehicleContainer  = document.getElementById("vehicle-container");
    const vehicleNumberInput= document.getElementById("vehicle_number");
    const vehicleIdInput    = document.getElementById("vehicle_id");

    // ← now comes from the PHP view, not inline PHP in JS
    const complaintVehicle  = COMPLAINT_VEHICLE;

    function populateVehicleDropdown(vehicles) {
        vehicleContainer.innerHTML = "";

        if (vehicles.length === 0) {
            const input = document.createElement("input");
            input.type = "text";
            vehicleContainer.appendChild(input);
            vehicleNumberInput.value = "";
            vehicleIdInput.value = "";
            return;
        }

        const dropdown = document.createElement("select");

        vehicles.forEach(v => {
            const opt       = document.createElement("option");
            opt.value       = `${v.make} ${v.model}`;
            opt.textContent = `${v.make} ${v.model}`;
            opt.dataset.plate = v.license_plate;
            opt.dataset.id    = v.vehicle_id;

            if (opt.value === complaintVehicle) {
                opt.selected = true;
                vehicleNumberInput.value = v.license_plate;
                vehicleIdInput.value     = v.vehicle_id;
            }

            dropdown.appendChild(opt);
        });

        dropdown.addEventListener("change", () => {
            const selected           = dropdown.selectedOptions[0];
            vehicleNumberInput.value = selected.dataset.plate;
            vehicleIdInput.value     = selected.dataset.id;
        });

        vehicleContainer.appendChild(dropdown);
    }

    function fetchVehiclesByPhone(phone) {
        fetch(`${BASE_URL}/manager/complaints/fetch-by-phone?phone=${encodeURIComponent(phone)}`)
            .then(res => res.json())
            .then(data => {
                if (!data.success || !data.data) return;
                const customer = data.data;

                customerInput.value = `${customer.first_name ?? ''} ${customer.last_name ?? ''}`.trim();
                emailInput.value    = customer.email ?? "";

                populateVehicleDropdown(customer.vehicles ?? []);
            })
            .catch(err => console.error("Fetch error:", err));
    }

    // Load vehicles on page load using pre-filled phone
    if (COMPLAINT_PHONE) fetchVehiclesByPhone(COMPLAINT_PHONE);

    // Reload if phone is manually changed
    phoneInput.addEventListener("blur", () => {
        const phone = phoneInput.value.trim();
        if (phone) fetchVehiclesByPhone(phone);
    });

});