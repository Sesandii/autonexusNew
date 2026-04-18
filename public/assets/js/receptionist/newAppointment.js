document.addEventListener('DOMContentLoaded', () => {
    const phoneInput = document.getElementById('phone');
    const customerInput = document.querySelector('input[name="customer_name"]');
    const customerIdInput = document.getElementById('customer_id');
    
    const vehicleSelect = document.getElementById('vehicle-select');
    const vehicleNumberInput = document.getElementById('vehicle_number');
    const vehicleIdInput = document.getElementById('vehicle_id');
    const vehicleInput = document.querySelector('input[name="vehicle"]');
    
    const appointmentSelect = document.getElementById('appointment_id');
    
    const cancelButton = document.querySelector('.cancel-button');
    const dateInput = document.querySelector('input[name="complaint_date"]');
    const timeInput = document.querySelector('input[name="complaint_time"]');
    
    // Set default date to today
    if (dateInput && !dateInput.value) {
        const today = new Date().toISOString().split('T')[0];
        dateInput.value = today;
    }
    
    // Set default time
    if (timeInput && !timeInput.value) {
        timeInput.value = '09:00';
    }
    
    // Fetch customer by phone
    async function fetchCustomer(phone) {
        try {
            const res = await fetch(`${BASE_URL}/receptionist/complaints/fetchByPhone?phone=${encodeURIComponent(phone)}`);
            return await res.json();
        } catch (err) {
            console.error(err);
            return null;
        }
    }
    
    // Fetch appointments for customer
    async function fetchAppointments(customerId) {
        try {
            const res = await fetch(`${BASE_URL}/receptionist/complaints/fetchAppointments?customer_id=${customerId}`);
            return await res.json();
        } catch (err) {
            console.error('Error fetching appointments:', err);
            return { success: false, data: [] };
        }
    }
    
    // Populate appointment dropdown
    function populateAppointmentDropdown(appointments) {
        if (!appointmentSelect) return;
        
        // Clear existing options except "None"
        appointmentSelect.innerHTML = '<option value="">None</option>';
        
        if (!appointments || appointments.length === 0) {
            return;
        }
        
        // Add appointment options
        appointments.forEach(appointment => {
            const option = document.createElement('option');
            option.value = appointment.appointment_id;
            option.textContent = `${appointment.appointment_date} ${appointment.appointment_time || ''} - ${appointment.service_type || 'Appointment'}`;
            appointmentSelect.appendChild(option);
        });
    }
    
    // Update vehicle info inputs
    function updateVehicleInfo() {
        const selectedOption = vehicleSelect.options[vehicleSelect.selectedIndex];
        if (selectedOption && selectedOption.value) {
            vehicleIdInput.value = selectedOption.value;
            vehicleNumberInput.value = selectedOption.dataset.license || '';
            vehicleInput.value = selectedOption.textContent;
        } else {
            vehicleIdInput.value = '';
            vehicleNumberInput.value = '';
            vehicleInput.value = '';
        }
    }
    
    // Phone input change
    phoneInput.addEventListener('change', async () => {
        const phone = phoneInput.value.trim();
        if (!phone) return;
        
        const result = await fetchCustomer(phone);
        
        if (result.success && result.data) {
            const customer = result.data;
            customerIdInput.value = customer.customer_id;
            customerInput.value = `${customer.first_name} ${customer.last_name}`;
            document.querySelector('input[name="email"]').value = customer.email || '';
            
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
            } else {
                vehicleIdInput.value = '';
                vehicleNumberInput.value = '';
                vehicleInput.value = '';
            }
            
            // Fetch and populate appointments
            const appointmentsResult = await fetchAppointments(customer.customer_id);
            if (appointmentsResult.success) {
                populateAppointmentDropdown(appointmentsResult.data);
            }
        } else {
            alert('Customer not found.');
            customerIdInput.value = '';
            customerInput.value = '';
            document.querySelector('input[name="email"]').value = '';
            vehicleSelect.innerHTML = '<option value="">-- Select Vehicle --</option>';
            vehicleIdInput.value = '';
            vehicleNumberInput.value = '';
            vehicleInput.value = '';
            populateAppointmentDropdown([]);
        }
    });
    
    vehicleSelect.addEventListener('change', updateVehicleInfo);
    
    // Cancel button
    cancelButton.addEventListener('click', () => {
        window.history.back();
    });
});