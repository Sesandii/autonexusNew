function search() {
  const vehicle = document.getElementById("vehicleNumber").value;
  const from = document.getElementById("fromDate").value;
  const to = document.getElementById("toDate").value;

  const result = document.getElementById("resultText");

  if (vehicle) {
    result.innerHTML = `<p>Showing history for vehicle <strong>${vehicle}</strong> from <strong>${from || 'N/A'}</strong> to <strong>${to || 'N/A'}</strong>.</p>`;
  } else {
    result.innerHTML = `<a href="#">Search for a vehicle to view its history.</a>`;
  }
}
