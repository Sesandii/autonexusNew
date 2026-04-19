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

    // ✅ Detect edit mode (you must pass appointment_id in URL when editing)
    const urlParams = new URLSearchParams(window.location.search);
    const appointmentId = urlParams.get('appointment_id');

    // ----------------------------
    // Fetch customer by phone
    // ----------------------------
    async function fetchCustomer(phone) {
        try {
            const res = await fetch(`${BASE_URL}/receptionist/appointments/getCustomer?phone=${encodeURIComponent(phone)}`);
            return await res.json();
        } catch (err) {
            console.error(err);
            return null;
        }
    }

    // ----------------------------
    // Update vehicle info
    // ----------------------------
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

    // ----------------------------
    // Branch selection
    // ----------------------------
    branchSearchInput.addEventListener('input', () => {
        const value = branchSearchInput.value;

        for (let i = 0; i < branchList.options.length; i++) {
            if (branchList.options[i].value === value) {
                branchIdInput.value = branchList.options[i].dataset.id;
                break;
            }
        }
    });

    // ----------------------------
    // Phone lookup
    // ----------------------------
    phoneInput.addEventListener('change', async () => {

        const phone = phoneInput.value.trim();
        if (!phone) return;

        const customer = await fetchCustomer(phone);

        if (customer && customer.customer_id) {

            customerIdInput.value = customer.customer_id;
            customerInput.value = `${customer.first_name} ${customer.last_name}`;

            // reset vehicles
            vehicleSelect.innerHTML = '<option value="">-- Select Vehicle --</option>';

            if (customer.vehicles && customer.vehicles.length > 0) {
                customer.vehicles.forEach(vehicle => {
                    const option = document.createElement('option');
                    option.value = vehicle.vehicle_id;
                    option.textContent = `${vehicle.make} ${vehicle.model}`;
                    option.dataset.license = vehicle.license_plate || vehicle.vehicle_code;
                    vehicleSelect.appendChild(option);
                });

                vehicleSelect.selectedIndex = 1;
                updateVehicleInfo();
            }

        } else {
            alert('Customer not found');

            customerIdInput.value = '';
            customerInput.value = '';
            vehicleSelect.innerHTML = '<option value="">-- Select Vehicle --</option>';
            vehicleIdInput.value = '';
            vehicleNumberInput.value = '';
        }
    });

    // ----------------------------
    // Vehicle change
    // ----------------------------
    vehicleSelect.addEventListener('change', updateVehicleInfo);

    // ----------------------------
    // Save / Update appointment
    // ----------------------------
    saveButton.addEventListener('click', async () => {

        if (!customerIdInput.value) return alert('Please enter a valid customer.');
        if (!vehicleIdInput.value) return alert('Please select a vehicle.');
        if (!serviceInput.value) return alert('Please select a service.');
        if (!branchIdInput.value) return alert('Please select a branch.');
        if (!dateInput.value) return alert('Please select a date.');
        if (!timeInput.value) return alert('Please select a time.');

        const data = {
            customer_id: customerIdInput.value,
            vehicle_id: vehicleIdInput.value,
            branch_id: branchIdInput.value,
            service_id: serviceInput.value,
            appointment_date: dateInput.value,
            appointment_time: timeInput.value,
            status: statusInput.value,
            notes: ''
        };

        // ✅ If editing, add ID + use update endpoint
        let url = `${BASE_URL}/receptionist/appointments/save`;

        if (appointmentId) {
            data.appointment_id = appointmentId;
            url = `${BASE_URL}/receptionist/appointments/update`;
        }

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams(data)
            });

            const result = await res.json();

            if (result.success) {

                alert(result.message || 'Success');

                // ✅ IMPORTANT: redirect after success
                window.location.href = "/autonexus/receptionist/appointments";

            } else {
                alert('Failed: ' + result.message);
            }

        } catch (err) {
            console.error(err);
            alert('Error processing appointment');
        }
    });

});