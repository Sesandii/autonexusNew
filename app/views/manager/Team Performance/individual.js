const monthPicker = document.getElementById("month-picker");

  monthPicker.addEventListener("change", function () {
    const selectedMonth = this.value; // Format: YYYY-MM
    console.log("Selected Month:", selectedMonth);
    // Do something with the selected month
  });

