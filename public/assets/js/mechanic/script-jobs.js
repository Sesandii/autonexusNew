document.addEventListener("DOMContentLoaded", () => {
  const jobGrid = document.getElementById("job-grid");
  if (!jobGrid) {
    console.error("❌ job-grid not found in DOM");
    return;
  }

  // Sample data
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
      eta: "2023-09-14 14:00",
      mechanic: "Alex Perera",
      supervisor: "Nuwan Silva"
    },
    {
      customer: "Jake Paul",
      vehicle: "1994 Nissan R33",
      service: "Engine Diagnostic",
      eta: "2023-09-14 14:00",
      mechanic: "Alex Perera",
      supervisor: "Nuwan Silva"
    }, 
    {
      customer: "Jake Paul",
      vehicle: "1994 Nissan R33",
      service: "Engine Diagnostic",
      eta: "2023-09-14 14:00",
      mechanic: "Alex Perera",
      supervisor: "Nuwan Silva"
    }
  ];

  // Clear old content
  jobGrid.innerHTML = "";

  // Render each job as a card
  jobs.forEach(job => {
    const card = document.createElement("div");
    card.className = "job-card";

    card.innerHTML = `
      <h3>${job.service}</h3>
      <div class="job-info"><span>Customer:</span> ${job.customer}</div>
      <div class="job-info"><span>Vehicle:</span> ${job.vehicle}</div>
      <div class="job-info"><span>ETA:</span> ${job.eta}</div>
      <div class="job-info"><span>Mechanic:</span> ${job.mechanic}</div>
      <div class="job-info"><span>Supervisor:</span> ${job.supervisor}</div>
      <div class="job-actions">
        <button class="view-btn">View</button>
      </div>
    `;

    jobGrid.appendChild(card);
  });
});
