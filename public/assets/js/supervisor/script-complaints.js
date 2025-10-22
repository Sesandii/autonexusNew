document.addEventListener("DOMContentLoaded", () => {
    const statusFilter = document.getElementById("statusFilter");
    const priorityFilter = document.getElementById("priorityFilter");
    const search = document.getElementById("search");
    const complaints = document.querySelectorAll(".complaint-card");
  
    function filterComplaints() {
      const statusVal = statusFilter.value;
      const priorityVal = priorityFilter.value;
      const searchVal = search.value.toLowerCase();
  
      complaints.forEach(card => {
        const status = card.dataset.status;
        const priority = card.dataset.priority;
        const text = card.textContent.toLowerCase();
  
        const matchesStatus = (statusVal === "All" || status === statusVal);
        const matchesPriority = (priorityVal === "All" || priority === priorityVal);
        const matchesSearch = text.includes(searchVal);
  
        if (matchesStatus && matchesPriority && matchesSearch) {
          card.style.display = "block";
        } else {
          card.style.display = "none";
        }
      });
    }
  
    [statusFilter, priorityFilter, search].forEach(el => {
      el.addEventListener("input", filterComplaints);
    });
  });
  