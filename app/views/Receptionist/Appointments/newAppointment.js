document.addEventListener("DOMContentLoaded", function () {

    // Buttons
    const saveBtn = document.querySelector(".save-button");
    const cancelBtn = document.querySelector(".cancel-button");

    // Form input fields
    const phoneInput = document.getElementById("phone");
    const customerInput = document.getElementById("customer");
    const vehicleNumberInput = document.getElementById("vehicle-number");
    const vehicleInput = document.getElementById("vehicle");
    const serviceInput = document.getElementById("service");
    const statusSelect = document.getElementById("status");

    console.log("JS Loaded ✔");

    // -----------------------------
    // AUTO-LOAD CUSTOMER BY PHONE
    // -----------------------------

    phoneInput.addEventListener("keyup", function () {
        const phone = phoneInput.value.trim();

        console.log("Typing phone:", phone);

        if (phone.length < 10) return;

        fetch(BASE_URL + "/receptionist/appointments/find-customer", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "phone=" + encodeURIComponent(phone)
        })
        .then(res => res.json())
        .then(data => {
            console.log("Response:", data);

            if (data.status === "found") {

                document.getElementById("customer_id").value = data.customer.customer_id;

                if (data.vehicles.length > 0) {
                    document.getElementById("vehicle_id").value = data.vehicles[0].vehicle_id;
                }

                customerInput.value = data.customer.first_name + " " + data.customer.last_name;

                if (data.vehicles.length > 0) {
                    vehicleNumberInput.value = data.vehicles[0].vehicle_number;
                    vehicleInput.value = data.vehicles[0].brand + " " + data.vehicles[0].model;
                } else {
                    vehicleNumberInput.value = "";
                    vehicleInput.value = "";
                }
            } 
            else if (data.status === "not_found") {
                customerInput.value = "";
                vehicleNumberInput.value = "";
                vehicleInput.value = "";
                alert("No user found for this phone number.");
            }
        })
        .catch(err => console.error("Fetch error:", err));
    });

    // -----------------------------
    // SAVE BUTTON
    // -----------------------------

    saveBtn.addEventListener("click", () => {

        const formData = new URLSearchParams();
        formData.append("customer_id", document.getElementById("customer_id").value);
        formData.append("vehicle_id", document.getElementById("vehicle_id").value);
        formData.append("date", document.getElementById("Date").value);
        formData.append("time", document.getElementById("Time").value);
        formData.append("service", serviceInput.value);
        formData.append("status", statusSelect.value);

        fetch(BASE_URL + "/receptionist/appointments/save", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: formData.toString()
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === "success") {
                alert("Appointment saved!");
                window.location.href = BASE_URL + "/receptionist/appointments";
            } else {
                alert("Error saving appointment.");
            }
        });
    });

}); // END DOMContentLoaded
