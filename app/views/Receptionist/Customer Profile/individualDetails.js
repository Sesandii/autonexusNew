const tabs = document.querySelectorAll(".tab-item");
const contents = document.querySelectorAll(".tab-content");

tabs.forEach(tab => {
  tab.addEventListener("click", () => {
    // remove active classes
    tabs.forEach(t => t.classList.remove("active"));
    contents.forEach(c => c.classList.remove("active"));

    // activate clicked tab
    tab.classList.add("active");
    document.getElementById(tab.dataset.tab).classList.add("active");
  });
});
