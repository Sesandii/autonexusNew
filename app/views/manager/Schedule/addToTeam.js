  // 🔁 Redirect function (reusable)
function redirectToPage() {
  window.location.href = "Schedule.html"; // Replace with your target page
}

// 🚪 Handle Save button click
document.querySelector('.save-button').addEventListener('click', function () {
  // Optional: Add validation here before redirecting
  redirectToPage();
});

// ❌ Handle Cancel button click
document.querySelector('.cancel-button').addEventListener('click', function () {
  redirectToPage();
});

// ❌ Handle Close (×) button click
document.querySelector('.close-button').addEventListener('click', function () {
  redirectToPage();
});
  
  // Optional: consistent color from name
  function stringToColor(str) {
    let hash = 0;
    for (let i = 0; i < str.length; i++) {
      hash = str.charCodeAt(i) + ((hash << 5) - hash);
    }
    let color = '#';
    for (let i = 0; i < 3; i++) {
      const value = (hash >> (i * 8)) & 0xFF;
      color += ('00' + value.toString(16)).slice(-2);
    }
    return color;
  }

  document.getElementById("teamForm").addEventListener("submit", function(e) {
    e.preventDefault();

    const name = document.getElementById("name").value.trim();
    if (!name) return;

    const initial = name.charAt(0).toUpperCase();
    const color = stringToColor(name); // consistent color

    const circle = document.createElement("div");
    circle.className = "profile-circle";
    circle.style.backgroundColor = color;
    circle.textContent = initial;

    document.getElementById("teamList").appendChild(circle);

    // Optional: reset form
    this.reset();
  });


