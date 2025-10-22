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
