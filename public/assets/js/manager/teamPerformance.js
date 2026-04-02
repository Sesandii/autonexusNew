const tableBody = document.getElementById("table-body");

// Sample data, injected dynamically
const data = Array(7).fill().map((_, i) => ({
  id: i + 1,
  name: "David Lee",
  role: "Technician",
  completedJobs: 38,
  avgServiceTime: "86 min",
  returnRate: "6.2%",
  revenue: "$16,200",
  url: "individual.html" //`individual.html?id=${i + 1}` // Dynamic URL with ID
}));

data.forEach(entry => {
  const row = document.createElement("tr");
  row.style.cursor = "pointer";

  // Redirect to individual.html with unique ID
  row.addEventListener("click", () => {
    window.location.href = entry.url;
  });

  row.innerHTML = `
    <td><div class="circle"></div></td>
    <td>${entry.name}</td>
    <td style="color:#6b7280;">${entry.role}</td>
    <td>${entry.completedJobs}</td>
    <td>${entry.avgServiceTime}</td>
    <td>${entry.returnRate}</td>
    <td>${entry.revenue}</td>
  `;

  tableBody.appendChild(row);
});
