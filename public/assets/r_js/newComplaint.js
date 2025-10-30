// Select elements
const saveBtn = document.querySelector(".save-button");
const cancelBtn = document.querySelector(".cancel-button");

// Input fields
const customerInput = document.getElementById("customer");
const phoneInput = document.getElementById("phone");
const vehicleNumberInput = document.getElementById("vehicle-number");
const vehicleInput = document.getElementById("vehicle");
const serviceInput = document.getElementById("service");
const statusSelect = document.getElementById("status");

// Save button click
saveBtn.addEventListener("click", () => {
  // Check if any field is empty
  if (
    !customerInput.value.trim() ||
    !phoneInput.value.trim() ||
    !vehicleNumberInput.value.trim() ||
    !vehicleInput.value.trim() ||
    !serviceInput.value.trim() ||
    !statusSelect.value
  ) {
    alert("Please fill in all the details.");
    return; // stop further action
  }

  // If all fields are filled, you can add save logic here
  alert("Appointment saved successfully!");
  // Optionally, you can redirect after saving
  // window.location.href = "anotherPage.html";
});

// Cancel button click
cancelBtn.addEventListener("click", () => {
  window.location.href = "complaintsReceptionist.html"; // redirect to another page
});
