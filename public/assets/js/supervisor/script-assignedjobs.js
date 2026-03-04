document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("jobSearch");
    const serviceFilter = document.getElementById("serviceFilter");
    const mechanicFilter = document.getElementById("mechanicFilter");
    const statusFilter = document.getElementById("statusFilter");
    const jobCards = document.querySelectorAll(".job-card");
<<<<<<< HEAD
=======
    const resetBtn = document.getElementById("resetFilters");
>>>>>>> bc21bfd776db2147cd644a47aeb727bb8ca3d276

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
<<<<<<< HEAD
=======
    
  
>>>>>>> bc21bfd776db2147cd644a47aeb727bb8ca3d276

    searchInput.addEventListener("keyup", filterJobs);
    serviceFilter.addEventListener("change", filterJobs);
    mechanicFilter.addEventListener("change", filterJobs);
    statusFilter.addEventListener("change", filterJobs);
<<<<<<< HEAD
=======

    resetBtn.addEventListener("click", function () {
        searchInput.value = "";
        serviceFilter.value = "";
        mechanicFilter.value = "";
        statusFilter.value = "";
        filterJobs(); // ✅ correct function name
    });
    
>>>>>>> bc21bfd776db2147cd644a47aeb727bb8ca3d276
});
