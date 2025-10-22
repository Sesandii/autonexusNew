// Simple search filter
document.querySelector(".filter-search").addEventListener("input", function () {
  let query = this.value.toLowerCase();
  document.querySelectorAll(".card").forEach(card => {
    let text = card.innerText.toLowerCase();
    card.style.display = text.includes(query) ? "block" : "none";
  });
});
