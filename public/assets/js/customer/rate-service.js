// AutoNexus - Rate Service Interaction
document.addEventListener("DOMContentLoaded", () => {
  const starsContainer = document.getElementById("ratingStars");
  const ratingInput = document.getElementById("ratingInput");

  if (!starsContainer) return;

  // Create 5 stars
  for (let i = 1; i <= 5; i++) {
    const star = document.createElement("i");
    star.classList.add("fa-solid", "fa-star");
    star.dataset.value = i;
    starsContainer.appendChild(star);
  }

  const stars = starsContainer.querySelectorAll("i");

  // Hover effect: color stars from left to hovered
  stars.forEach(star => {
    star.addEventListener("mouseenter", () => {
      const val = +star.dataset.value;
      stars.forEach(s => s.classList.toggle("hover", +s.dataset.value <= val));
    });
    star.addEventListener("mouseleave", () => {
      stars.forEach(s => s.classList.remove("hover"));
    });
    star.addEventListener("click", () => {
      const val = +star.dataset.value;
      ratingInput.value = val;
      stars.forEach(s => s.classList.toggle("active", +s.dataset.value <= val));
    });
  });
});
