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

  // (Optional) You can add more JS here later for appointment <select> auto-fill if needed.
});
