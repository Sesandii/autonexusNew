document.addEventListener("DOMContentLoaded", () => {
    const customerInput = document.getElementById("customer_name");
    const phoneInput = document.getElementById("phone");
    const emailInput = document.getElementById("email");
    const vehicleContainer = document.getElementById("vehicle-container");
    const vehicleNumberInput = document.getElementById("vehicle_number");
    const vehicleIdInput = document.getElementById("vehicle_id");
    const customerIdInput = document.getElementById("customer_id");
    const userIdInput = document.getElementById("user_id");
    const saveBtn = document.querySelector(".save-button");
    const cancelBtn = document.querySelector(".cancel-button");
    const statusSelect = document.getElementById("status");

    const complaintVehicleId = vehicleContainer.dataset.complaintVehicleId || "";
    const complaintVehicleNumber = vehicleContainer.dataset.complaintVehicleNumber || "";

    function populateVehicleDropdown(vehicles, preselectedId = "") {
        vehicleContainer.innerHTML = "";

        if (!vehicles.length) {
            const input = document.createElement("input");
            input.type = "text";
            input.name = "vehicle";
            input.className = "form-control";
            input.value = complaintVehicleNumber;
            vehicleContainer.appendChild(input);
            vehicleNumberInput.value = complaintVehicleNumber;
            vehicleIdInput.value = "";
            return;
        }

        const dropdown = document.createElement("select");
        dropdown.name = "vehicle_id"; // correct field
        dropdown.className = "form-control";

        vehicles.forEach(v => {
            const opt = document.createElement("option");
            opt.value = `${v.make} ${v.model}`;
            opt.textContent = `${v.make} ${v.model}`;
            opt.dataset.plate = v.license_plate;
            opt.dataset.vehicleId = v.vehicle_id;

            if (v.vehicle_id == preselectedId) {
                opt.selected = true;
                vehicleNumberInput.value = v.license_plate;
                vehicleIdInput.value = v.vehicle_id;
            }

            dropdown.appendChild(opt);
        });

        dropdown.addEventListener("change", () => {
            const selected = dropdown.selectedOptions[0];
            vehicleNumberInput.value = selected.dataset.plate || "";
            vehicleIdInput.value = selected.dataset.vehicleId || "";
        });

        vehicleContainer.appendChild(dropdown);

        if (!preselectedId && dropdown.options.length > 0) {
            const first = dropdown.options[0];
            first.selected = true;
            vehicleNumberInput.value = first.dataset.plate;
            vehicleIdInput.value = first.dataset.vehicleId;
        }
    }

    function fetchCustomerByPhone(phone) {
        if (!phone) return;

        fetch(`${BASE_URL}/receptionist/complaints/fetchByPhone?phone=${encodeURIComponent(phone)}`)
            .then(res => res.json())
            .then(data => {
                if (!data.success || !data.data) return;

                const customer = data.data;

                customerInput.value = `${customer.first_name ?? ""} ${customer.last_name ?? ""}`.trim();
                emailInput.value = customer.email ?? "";
                customerIdInput.value = customer.customer_id;
                userIdInput.value = customer.user_id;

                populateVehicleDropdown(customer.vehicles ?? [], complaintVehicleId);
            })
            .catch(err => console.error("Fetch error:", err));
    }

    fetchCustomerByPhone(phoneInput.value.trim());
    phoneInput.addEventListener("blur", () => {
        fetchCustomerByPhone(phoneInput.value.trim());
    });

    saveBtn.addEventListener("click", (e) => {
        const vehicleInput = vehicleContainer.querySelector("[name='vehicle']");
        if (
            !customerInput.value.trim() ||
            !phoneInput.value.trim() ||
            !vehicleInput.value.trim() ||
            !vehicleNumberInput.value.trim() ||
            !statusSelect.value
        ) {
            alert("Please fill in all the details.");
            e.preventDefault();
        }
    });

    cancelBtn.addEventListener("click", (e) => {
        e.preventDefault();
        window.location.href = `${BASE_URL}/receptionist/complaints`;
    });
});
