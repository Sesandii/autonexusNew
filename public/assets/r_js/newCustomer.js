document.addEventListener("DOMContentLoaded", () => {

  const saveBtn = document.querySelector(".save-button");
  const cancelBtn = document.querySelector(".cancel-button");

  // Save button validation
  if (saveBtn) {
    saveBtn.addEventListener("click", () => {
      const requiredFields = [
        document.getElementById("customer"),
        document.getElementById("phone"),
        document.getElementById("vehicle-number"),
        document.getElementById("vehicle"),
        document.getElementById("service"),
        document.getElementById("status"),
      ];

      for (let field of requiredFields) {
        if (!field || !field.value.trim()) {
          alert("Please fill in all the details.");
          return;
        }
      }

      alert("Appointment saved successfully!");
    });
  }

  // Cancel button redirect
  if (cancelBtn) {
    cancelBtn.addEventListener("click", () => {
      window.location.href = `${BASE_URL}/receptionist/customers`;
    });
  }

  // -------------------------------
  // Vehicle Add / Remove
  // -------------------------------

  const addVehicleBtn = document.getElementById("add-vehicle");
  const vehiclesContainer = document.getElementById("vehicles-container");

  let vehicleIndex = 1;

  if (addVehicleBtn && vehiclesContainer) {
    addVehicleBtn.addEventListener("click", () => {

      const div = document.createElement("div");
      div.classList.add("vehicle-entry");

      div.innerHTML = `
        <input type="text" name="vehicles[${vehicleIndex}][license_plate]" placeholder="License Plate" required>
        <input type="text" name="vehicles[${vehicleIndex}][make]" placeholder="Make">
        <input type="text" name="vehicles[${vehicleIndex}][model]" placeholder="Model">
        <input type="text" name="vehicles[${vehicleIndex}][year]" placeholder="Year">
        <input type="text" name="vehicles[${vehicleIndex}][color]" placeholder="Color">
        <button type="button" class="remove-vehicle">❌</button>
      `;

      vehicleIndex++;
      vehiclesContainer.appendChild(div);

      // Add remove handler
      div.querySelector(".remove-vehicle").addEventListener("click", () => {
        div.remove();
      });

    });

    // Existing delete buttons (first row)
    vehiclesContainer.querySelectorAll(".remove-vehicle").forEach(btn => {
      btn.addEventListener("click", e => e.target.parentElement.remove());
    });
  }

});
