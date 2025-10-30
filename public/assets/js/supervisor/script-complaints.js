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
    vehicle: "2018 Ford F‑150",
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

function renderComplaints(filterStatus = "All") {
  const container = document.getElementById("complaintsList");
  container.innerHTML = "";

  const filtered = complaintsData.filter(c => filterStatus === "All" || c.status === filterStatus);

  filtered.forEach(c => {
    const card = document.createElement("div");
    
    container.appendChild(card);
  });
}

document.getElementById("statusFilter").addEventListener("change", function() {
  renderComplaints(this.value);
});

// Initial render:
renderComplaints();


