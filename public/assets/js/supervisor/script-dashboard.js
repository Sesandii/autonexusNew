document.addEventListener("DOMContentLoaded", function () {
    const userName = document.getElementById("user-name");
    const dropdown = document.getElementById("dropdown");

    userName.addEventListener("click", function () {
      dropdown.classList.toggle("hidden");
    });

    // Close dropdown when clicking outside
    document.addEventListener("click", function (event) {
      if (!userName.contains(event.target) && !dropdown.contains(event.target)) {
        dropdown.classList.add("hidden");
      }
    });
  });

  document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById("search");
    const resultsContainer = document.getElementById("search-results");
  
    // Define searchable items (name + id of section)
    const searchItems = [
      { name: "Complaints Section", target: "complaints-sec" }
    ];
  
    // Show results on typing
    searchInput.addEventListener("input", () => {
      const query = searchInput.value.toLowerCase();
      resultsContainer.innerHTML = "";
  
      if (!query) {
        resultsContainer.classList.add("hidden");
        return;
      }
  
      const matches = searchItems.filter(item => item.name.toLowerCase().includes(query));
  
      if (matches.length > 0) {
        matches.forEach(item => {
          const li = document.createElement("li");
          li.textContent = item.name;
          li.addEventListener("click", () => {
            // Scroll to section
            document.getElementById(item.target).scrollIntoView({ behavior: "smooth" });
            resultsContainer.classList.add("hidden");
            searchInput.value = "";
          });
          resultsContainer.appendChild(li);
        });
        resultsContainer.classList.remove("hidden");
      } else {
        resultsContainer.classList.add("hidden");
      }
    });
  
    // Hide dropdown if click outside
    document.addEventListener("click", (e) => {
      if (!e.target.closest(".search-wrapper")) {
        resultsContainer.classList.add("hidden");
      }
    });
  });
