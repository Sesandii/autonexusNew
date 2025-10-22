const jobs = [
  {
    customer: "John Smith",
    vehicle: "2018 Toyota Camry",
    service: "Oil Change & Tire Rotation",
    eta: "2023-11-15 15:30",
    mechanic: "Mike Johnson",
    supervisor: "Sarah Williams"
  },
  {
    customer: "Jake Paul",
    vehicle: "1994 Nissan R33",
    service: "Engine Diagnostic",
    eta: "2023-09-14 18:50",
    mechanic: "Robert Brown",
    supervisor: "James Thompson"
  },
  {
    customer: "Jake Paul",
    vehicle: "1994 Nissan R33",
    service: "Engine Diagnostic",
    eta: "2023-09-14 18:50",
    mechanic: "Robert Brown",
    supervisor: "James Thompson"
  },
  {
    customer: "Jake Paul",
    vehicle: "1994 Nissan R33",
    service: "Engine Diagnostic",
    eta: "2023-09-14 18:50",
    mechanic: "Robert Brown",
    supervisor: "James Thompson"
  },
  {
    customer: "Emily Davis",
    vehicle: "2020 Honda Civic",
    service: "Brake Replacement",
    eta: "2023-11-15 17:00",
    mechanic: "Robert Brown",
    supervisor: "Sarah Williams"
  },
  {
    customer: "Michael Wilson",
    vehicle: "2019 Ford F-150",
    service: "Engine Diagnostic",
    eta: "2023-11-16 10:15",
    mechanic: "David Martinez",
    supervisor: "James Thompson"
  },
  {
    customer: "Jessica Taylor",
    vehicle: "2021 Chevrolet Equinox",
    service: "Transmission Service",
    eta: "2023-11-16 13:45",
    mechanic: "Mike Johnson",
    supervisor: "James Thompson"
  },
  {
    customer: "Robert Anderson",
    vehicle: "2017 BMW 3 Series",
    service: "Full Service",
    eta: "2023-11-17 09:30",
    mechanic: "Robert Brown",
    supervisor: "Sarah Williams"
  }
];

const tableBody = document.getElementById("job-table-body");

jobs.forEach(job => {
  const row = document.createElement("tr");

  row.innerHTML = `
    <td>${job.customer}</td>
    <td>${job.vehicle}</td>
    <td>${job.service}</td>
    <td>${job.eta}</td>
    <td>${job.mechanic}</td>
    <td>${job.supervisor}</td>
    <td><button class="view-btn">View</button></td>
  `;

  tableBody.appendChild(row);
});
