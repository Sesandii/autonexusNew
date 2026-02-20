document.addEventListener("DOMContentLoaded", () => {
  const BASE_URL = "<?= BASE_URL ?>";

  const customerInput = document.getElementById("customer_name");
  const phoneInput = document.getElementById("phone");
  const emailInput = document.getElementById("email");
  const vehicleContainer = document.getElementById("vehicle-container");
  const vehicleNumberInput = document.getElementById("vehicle_number");

  const complaintVehicle = "<?= htmlspecialchars($complaint['vehicle']) ?>"; // pre-selected vehicle

  // Function to populate vehicles
  function populateVehicleDropdown(vehicles) {
    vehicleContainer.innerHTML = "";

    if (vehicles.length === 0) {
      const input = document.createElement("input");
      input.type = "text";
      input.name = "vehicle";
      input.className = "form-control";
      vehicleContainer.appendChild(input);
      vehicleNumberInput.value = "";
      return;
    }

    const dropdown = document.createElement("select");
    dropdown.name = "vehicle";
    dropdown.className = "form-control";

    vehicles.forEach(v => {
      const opt = document.createElement("option");
      opt.value = `${v.make} ${v.model}`;
      opt.textContent = `${v.make} ${v.model}`;
      opt.dataset.plate = v.license_plate;

      if (opt.value === complaintVehicle) {
        opt.selected = true; // pre-select complaint vehicle
        vehicleNumberInput.value = v.license_plate;
      }

      dropdown.appendChild(opt);
    });

    dropdown.addEventListener("change", () => {
      vehicleNumberInput.value = dropdown.selectedOptions[0].dataset.plate;
    });

    vehicleContainer.appendChild(dropdown);
  }

  // Fetch customer vehicles on page load using phone
  const phone = phoneInput.value.trim();
  if (phone) {
    fetch(`${BASE_URL}/receptionist/complaints/fetch-by-phone?phone=${encodeURIComponent(phone)}`)
      .then(res => res.json())
      .then(data => {
        if (!data.success || !data.data) return;
        const customer = data.data;

        customerInput.value = `${customer.first_name ?? ''} ${customer.last_name ?? ''}`.trim();
        emailInput.value = customer.email ?? "";

        populateVehicleDropdown(customer.vehicles ?? []);
      })
      .catch(err => console.error("Fetch error:", err));
  }

  // Optional: update vehicles if phone changes
  phoneInput.addEventListener("blur", () => {
    const phone = phoneInput.value.trim();
    if (!phone) return;

    fetch(`${BASE_URL}/receptionist/complaints/fetch-by-phone?phone=${encodeURIComponent(phone)}`)
      .then(res => res.json())
      .then(data => {
        if (!data.success || !data.data) return;
        const customer = data.data;

        customerInput.value = `${customer.first_name ?? ''} ${customer.last_name ?? ''}`.trim();
        emailInput.value = customer.email ?? "";

        populateVehicleDropdown(customer.vehicles ?? []);
      })
      .catch(err => console.error("Fetch error:", err));
  });
});
