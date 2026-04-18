document.addEventListener("DOMContentLoaded", () => {
  const chips = Array.from(document.querySelectorAll(".service-chip"));
  const grids = Array.from(document.querySelectorAll(".service-grid"));
  const headings = Array.from(document.querySelectorAll(".service-category"));
  const servicesRoot = document.getElementById("servicesRoot");

  function applyFilter(category) {
    chips.forEach((chip) => {
      chip.classList.toggle("active", chip.dataset.filter === category);
    });

    grids.forEach((grid) => {
      const match = category === "all" || grid.dataset.cat === category;
      grid.style.display = match ? "grid" : "none";
    });

    headings.forEach((heading) => {
      const match = category === "all" || heading.dataset.cat === category;
      heading.style.display = match ? "block" : "none";
    });

    if (servicesRoot) {
      servicesRoot.scrollIntoView({ behavior: "smooth", block: "start" });
    }
  }

  chips.forEach((chip) => {
    chip.addEventListener("click", () => {
      const target = chip.dataset.filter || "all";
      chips.forEach((c) => c.setAttribute("aria-selected", "false"));
      chip.setAttribute("aria-selected", "true");
      applyFilter(target);
    });
  });

  applyFilter("all");
});
