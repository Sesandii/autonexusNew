const complaintsData = [
  {
    title: "Engine noise after service",
    desc: "Customer reports unusual engine noise after the 30,000 mile service was completed yesterday.",
    customer: "James Wilson",
    vehicle: "2019 Honda Civic",
    date: "Jul 28, 2025",
    assigned: "Mike Johnson",
    status: "Open"
  },
  {
    title: "AC not cooling properly",
    desc: "Customer complains that the air conditioning is not cooling effectively after the recent AC service.",
    customer: "Tom Hawk",
    vehicle: "2018 Ford F-150",
    date: "Jul 26, 2025",
    assigned: "Mike Johnson",
    status: "In Progress"
  },
  {
    title: "Brake squeaking",
    desc: "Customer reports loud squeaking noise when braking. Brakes were replaced at our shop 2 weeks ago.",
    customer: "Lisa Chen",
    vehicle: "2020 Toyota Premio",
    date: "Jul 25, 2025",
    assigned: "Mike Johnson",
    status: "Resolved"
  },
  {
    title: "Tire pressure warning",
    desc: "Customer reports tire pressure warning light comes on intermittently since last service.",
    customer: "Lisa Chen",
    vehicle: "2022 Nissan Leaf",
    date: "Jul 25, 2025",
    assigned: "Mike Johnson",
    status: "Resolved"
  }
];

function getStatusClass(status) {
  switch (status) {
    case "Open":
      return "status-open";
    case "In Progress":
      return "status-inprogress";
    case "Resolved":
      return "status-resolved";
    default:
      return "";
  }
}

function renderComplaints(filterStatus = "All") {
  const container = document.getElementById("complaintsList");
  container.innerHTML = "";

  const filtered = complaintsData.filter(
    c => filterStatus === "All" || c.status === filterStatus
  );

  if (filtered.length === 0) {
    container.innerHTML = "<p>No complaints found.</p>";
    return;
  }

  filtered.forEach(c => {
    const card = document.createElement("div");
    card.classList.add("complaint-card");

    card.innerHTML = `
      <div class="title">
        <img src="/autonexus/public/assets/img/Complaints.png" class="complaint-icon" alt="icon">
        ${c.title}
      </div>
      <div class="desc">${c.desc}</div>
      <div class="meta">
        <span><strong>Customer:</strong> ${c.customer}</span>
        <span><strong>Vehicle:</strong> ${c.vehicle}</span>
        <span><strong>Date:</strong> ${c.date}</span>
      </div>
      <div class="assigned"><strong>Assigned:</strong> ${c.assigned}</div>
      <div class="status-badge ${getStatusClass(c.status)}">${c.status}</div>
    `;

    container.appendChild(card);
  });
}

document.getElementById("statusFilter").addEventListener("change", function() {
  renderComplaints(this.value);
});

// Initial render
renderComplaints();
