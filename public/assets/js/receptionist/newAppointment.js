document.addEventListener('DOMContentLoaded', () => {
    const phoneInput = document.getElementById('phone');
    const customerInput = document.getElementById('customer');
    const customerIdInput = document.getElementById('customer_id');

    const vehicleSelect = document.getElementById('vehicle-select');
    const vehicleNumberInput = document.getElementById('vehicle-number');
    const vehicleIdInput = document.getElementById('vehicle_id');

    const saveButton = document.querySelector('.save-button');
    const serviceInput = document.getElementById('service');
    const dateInput = document.getElementById('Date');
    const timeInput = document.getElementById('Time');
    const statusInput = document.getElementById('status');

    const branchSearchInput = document.getElementById('branch-search');
    const branchIdInput = document.getElementById('branch_id');
    const branchList = document.getElementById('branch-list');

    // Fetch customer by phone
    async function fetchCustomer(phone) {
        try {
            const res = await fetch(`${BASE_URL}/receptionist/appointments/getCustomer?phone=${encodeURIComponent(phone)}`);
            return await res.json();
        } catch (err) {
            console.error(err);
            return null;
        }
    }

    // Update vehicle info inputs
    function updateVehicleInfo() {
        const selectedOption = vehicleSelect.options[vehicleSelect.selectedIndex];
        if (selectedOption && selectedOption.value) {
            vehicleIdInput.value = selectedOption.value;
            vehicleNumberInput.value = selectedOption.dataset.license || '';
        } else {
            vehicleIdInput.value = '';
            vehicleNumberInput.value = '';
        }
    }

    // Handle branch selection from datalist
    branchSearchInput.addEventListener('input', () => {
        const value = branchSearchInput.value;
        for (let i = 0; i < branchList.options.length; i++) {
            if (branchList.options[i].value === value) {
                branchIdInput.value = branchList.options[i].dataset.id;
                break;
            }
        }
    });

    // Phone input change
    phoneInput.addEventListener('change', async () => {
        const phone = phoneInput.value.trim();
        if (!phone) return;

        const customer = await fetchCustomer(phone);

        if (customer && customer.customer_id) {
            customerIdInput.value = customer.customer_id;
            customerInput.value = `${customer.first_name} ${customer.last_name}`;

            // Populate vehicle dropdown
            vehicleSelect.innerHTML = '<option value="">-- Select Vehicle --</option>';

            if (customer.vehicles && customer.vehicles.length > 0) {
                customer.vehicles.forEach(vehicle => {
                    const option = document.createElement('option');
                    option.value = vehicle.vehicle_id;
                    option.textContent = `${vehicle.make} ${vehicle.model}`;
                    option.dataset.license = vehicle.license_plate || vehicle.vehicle_code;
                    vehicleSelect.appendChild(option);
                });

                vehicleSelect.selectedIndex = 1; // select first vehicle
                updateVehicleInfo();
            } else {
                vehicleIdInput.value = '';
                vehicleNumberInput.value = '';
            }
        } else {
            alert('Customer not found.');
            customerIdInput.value = '';
            customerInput.value = '';
            vehicleSelect.innerHTML = '<option value="">-- Select Vehicle --</option>';
            vehicleIdInput.value = '';
            vehicleNumberInput.value = '';
        }
    });

    // Vehicle select change
    vehicleSelect.addEventListener('change', updateVehicleInfo);

    // Save appointment
    saveButton.addEventListener('click', async () => {
        if (!customerIdInput.value) {
            alert('Please enter a valid customer.');
            return;
        }
        if (!vehicleIdInput.value) {
            alert('Please select a vehicle.');
            return;
        }
        if (!serviceInput.value) {
            alert('Please select a service.');
            return;
        }
        if (!branchIdInput.value) {
            alert('Please select a branch from the list.');
            return;
        }
const data = {
    customer_id: customerIdInput.value,
    vehicle_id: vehicleIdInput.value,
    branch_id: branchIdInput.value,
    service_id: serviceInput.value,
    appointment_date: dateInput.value,   // match PHP key
    appointment_time: timeInput.value,   // match PHP key
    status: statusInput.value,
    notes: ''
};


        try {
            const res = await fetch(`${BASE_URL}/receptionist/appointments/save`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams(data)
            });

            const result = await res.json();
            if (result.success) {
                alert('Appointment saved successfully!');
                document.querySelector('form').reset();
                vehicleSelect.innerHTML = '<option value="">-- Select Vehicle --</option>';
                branchIdInput.value = '';
            } else {
                alert('Failed: ' + result.message);
            }
        } catch (err) {
            console.error(err);
            alert('An error occurred while saving the appointment.');
        }
    });
});
