console.log("Dropdowns found:", document.querySelectorAll('.assign-supervisor').length);
document.addEventListener("DOMContentLoaded", () => {
    // Update header based on clicked date
    const headerEl = document.getElementById("appointments-header");
    const urlParams = new URLSearchParams(window.location.search);
    const clickedDate = urlParams.get("date");

    let displayDate;
    if (clickedDate) {
        const dateObj = new Date(clickedDate);
        displayDate = dateObj.toLocaleDateString("en-US", { year: "numeric", month: "long", day: "numeric" });
    } else {
        const today = new Date();
        displayDate = today.toLocaleDateString("en-US", { year: "numeric", month: "long", day: "numeric" });
    }

    if (headerEl) {
        headerEl.textContent = `Appointments - ${displayDate}`;
    }


});