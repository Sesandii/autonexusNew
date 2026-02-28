document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("jobSearch");
    const serviceFilter = document.getElementById("serviceFilter");
    const mechanicFilter = document.getElementById("mechanicFilter");
    const statusFilter = document.getElementById("statusFilter");
    const jobCards = document.querySelectorAll(".job-card");

    function filterJobs() {
        const searchText = searchInput.value.toLowerCase().trim();
        const serviceValue = serviceFilter.value;
        const mechanicValue = mechanicFilter.value;
        const statusValue = statusFilter.value;

        jobCards.forEach(card => {
            const workOrder = card.dataset.workorder;
            const service = card.dataset.service;
            const mechanic = card.dataset.mechanic;
            const status = card.dataset.status;

            const matchesSearch =
                workOrder.includes(searchText) ||
                service.includes(searchText);

            const matchesService =
                serviceValue === "" || service === serviceValue;

            const matchesMechanic =
                mechanicValue === "" || mechanic === mechanicValue;

            const matchesStatus =
                statusValue === "" || status === statusValue;

            card.style.display =
                (matchesSearch && matchesService && matchesMechanic && matchesStatus)
                ? "block"
                : "none";
        });
    }

    searchInput.addEventListener("keyup", filterJobs);
    serviceFilter.addEventListener("change", filterJobs);
    mechanicFilter.addEventListener("change", filterJobs);
    statusFilter.addEventListener("change", filterJobs);
});
