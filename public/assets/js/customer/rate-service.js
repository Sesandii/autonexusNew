// AutoNexus - Rate Service Interaction
document.addEventListener("DOMContentLoaded", () => {
  // ---- STAR RATING ----
  const starsContainer = document.getElementById("ratingStars");
  const ratingInput = document.getElementById("ratingInput");

  if (starsContainer && ratingInput) {
    // Create 5 stars visually left → right
    for (let i = 1; i <= 5; i++) {
      const star = document.createElement("i");
      star.classList.add("fa-solid", "fa-star");
      star.dataset.value = String(i); // "1".."5"
      starsContainer.appendChild(star);
    }

    const stars = Array.from(starsContainer.querySelectorAll("i"));
    let currentRating = Number(ratingInput.value) || 0;

    // Helper: color stars up to "rating"
    function paint(rating) {
      stars.forEach((star) => {
        const value = Number(star.dataset.value);
        star.classList.toggle("active", value <= rating);
      });
    }

    // Initial paint (in case rating comes from server)
    paint(currentRating);

    // Hover preview
    stars.forEach((star) => {
      star.addEventListener("mouseenter", () => {
        const hoverValue = Number(star.dataset.value);
        paint(hoverValue); // temporarily show this rating
      });

      star.addEventListener("click", () => {
        currentRating = Number(star.dataset.value);
        ratingInput.value = String(currentRating);
        paint(currentRating); // lock in this rating
      });
    });

    // When mouse leaves the whole stars area → restore saved rating
    starsContainer.addEventListener("mouseleave", () => {
      paint(currentRating);
    });
  }

  // ---- AUTO-FILL vehicle details when appointment is selected ----
  const appointmentSelect = document.getElementById("appointment");
  const vehicleNumberInput = document.getElementById("vehicleNumber");
  const brandModelInput = document.getElementById("brandModel");
  const serviceDateInput = document.getElementById("serviceDate");

  if (appointmentSelect) {
    appointmentSelect.addEventListener("change", () => {
      const selected = appointmentSelect.options[appointmentSelect.selectedIndex];
      if (vehicleNumberInput) vehicleNumberInput.value = selected.dataset.vehicle || "";
      if (brandModelInput)    brandModelInput.value    = selected.dataset.model   || "";
      if (serviceDateInput) {
        const raw = selected.dataset.date || "";
        if (raw) {
          const d = new Date(raw);
          serviceDateInput.value = isNaN(d.getTime()) ? raw : d.toLocaleDateString("en-GB", { day: "2-digit", month: "short", year: "numeric" });
        } else {
          serviceDateInput.value = "";
        }
      }
    });
  }
});
