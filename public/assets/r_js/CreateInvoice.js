// ========== BUTTONS ==========
const saveBtn = document.querySelector(".save-button");
const cancelBtn = document.querySelector(".cancel-button");

// ========== SAVE BUTTON VALIDATION ==========
saveBtn.addEventListener("click", () => {
    const customerInput = document.querySelector('input[name="customer_name"]');
    const phoneInput = document.querySelector('input[name="phone"]');
    const vehicleNumberInput = document.querySelector('input[name="vehicle_number"]');
    const vehicleInput = document.querySelector('[name="vehicle"]');
    const serviceInput = document.querySelector('[name="service"]');

    if (
        !customerInput.value.trim() ||
        !phoneInput.value.trim() ||
        !vehicleNumberInput.value.trim() ||
        !vehicleInput.value ||
        !serviceInput.value
    ) {
        alert("Please fill in all the details.");
        return;
    }

    alert("Invoice generated successfully!");
});

// ========== CANCEL BUTTON ==========
cancelBtn.addEventListener("click", () => {
    window.location.href = BASE_URL + "/receptionist/complaints";
});

// =============== Auto Fill Date & Time ==================
document.addEventListener("DOMContentLoaded", () => {
    const dateField = document.querySelector('input[name="complaint_date"]');
    const timeField = document.querySelector('input[name="complaint_time"]');
    const now = new Date();

    if (dateField) dateField.value = now.toISOString().split("T")[0];
    if (timeField) timeField.value = now.toTimeString().slice(0,5);

    setupPhoneLookup();
    setupServiceTypeChange();
    setupServiceChange();
});

// =============== Fetch Customer by Phone ==================
function setupPhoneLookup() {
    const phoneField = document.querySelector('input[name="phone"]');

    phoneField.addEventListener("keyup", () => {
        const phone = phoneField.value.trim();
        if (phone.length < 3) return;

        fetch(`${BASE_URL}/receptionist/billing/getCustomerData?phone=${encodeURIComponent(phone)}`)
            .then(res => res.json())
            .then(data => {
                if (data.error) return;

                // Fill name and email
                document.querySelector('input[name="customer_name"]').value =
                    `${data.first_name} ${data.last_name}`;
                document.querySelector('input[name="email"]').value = data.email;

                // Fill vehicles
                populateVehicleDropdown(data.vehicles);
            });
    });
}

// =============== Vehicle Dropdown ==================
function populateVehicleDropdown(vehicles) {
    const container = document.querySelector('#vehicle-container');
    const numberField = document.querySelector('input[name="vehicle_number"]');

    if (!container) return;

    // Clear previous content
    container.innerHTML = "";

    if (vehicles.length === 0) {
        const input = document.createElement("input");
        input.type = "text";
        input.name = "vehicle";
        input.className = "form-control";
        container.appendChild(input);
        numberField.value = "";
        return;
    }

    // Create select dropdown
    const dropdown = document.createElement("select");
    dropdown.name = "vehicle";
    dropdown.className = "form-control";

    vehicles.forEach(v => {
        const opt = document.createElement("option");
        opt.value = v.vehicle_id;
        opt.textContent = `${v.make} ${v.model}`;
        opt.dataset.plate = v.license_plate;
        dropdown.appendChild(opt);
    });

    // Autofill license plate on change
    dropdown.addEventListener("change", () => {
        numberField.value = dropdown.selectedOptions[0].dataset.plate;
    });

    container.appendChild(dropdown);

    // Set first vehicle as default
    dropdown.selectedIndex = 0;
    numberField.value = dropdown.selectedOptions[0].dataset.plate;
}

// =============== Service Type → Services or Packages ==================
function setupServiceTypeChange() {
    const typeField = document.querySelector('select[name="service_type"]');
    if (!typeField) return;

    typeField.addEventListener("change", () => {
        const typeId = typeField.value;

        if (typeId == 9) { // Packages
            fetch(`${BASE_URL}/receptionist/billing/getPackages`)
                .then(res => res.json())
                .then(data => {
                    populatePackagesDropdown(data);
                });
        } else { // Normal services
            fetch(`${BASE_URL}/api/services?type_id=${typeId}`)
                .then(res => res.json())
                .then(data => {
                    populateServicesDropdown(data);
                });
        }
    });
}

// =============== Populate Normal Services ==================
function populateServicesDropdown(services) {
    const serviceField = document.querySelector('select[name="service"]');
    const priceField = document.querySelector('input[name="Price"]');

    serviceField.innerHTML = "";

    services.forEach(s => {
        const opt = document.createElement("option");
        opt.value = s.service_id;
        opt.textContent = s.name;
        opt.dataset.price = s.default_price;
        serviceField.appendChild(opt);
    });

    if (services.length > 0) {
        priceField.value = services[0].default_price;
    }
}

// =============== Populate Packages ==================
function populatePackagesDropdown(packages) {
    const serviceField = document.querySelector('select[name="service"]');
    const priceField = document.querySelector('input[name="Price"]');

    serviceField.innerHTML = "";

    packages.forEach(pkg => {
        const opt = document.createElement("option");
        opt.value = pkg.package_code;
        opt.textContent = pkg.name;
        opt.dataset.price = pkg.total_price;
        serviceField.appendChild(opt);
    });

    if (packages.length > 0) {
        priceField.value = packages[0].total_price;
    }
}

// =============== Update Price on Service/Package Change ==================
function setupServiceChange() {
    const serviceField = document.querySelector('select[name="service"]');
    const priceField = document.querySelector('input[name="Price"]');

    if (!serviceField) return;

    serviceField.addEventListener("change", () => {
        const selected = serviceField.selectedOptions[0];
        if (!selected) return;

        priceField.value = selected.dataset.price ?? "";
    });
}
