document.addEventListener("DOMContentLoaded", () => {
  console.log("✅ scripts.js loaded");

  // --- ELEMENT REFERENCES ---
  const modal = document.getElementById("jobModal");
  const modalTitle = document.getElementById("modalTitle");
  const jobForm = document.getElementById("jobForm");
  const saveBtn = document.getElementById("saveBtn");
  const cancelBtn = document.getElementById("cancelBtn");
  const modalClose = document.getElementById("modalClose");
  const addJobBtn = document.getElementById("addJobBtn");

  const jobIdInput = document.getElementById("job_id");
  const jobIdDisplay = document.getElementById("jobIdDisplay");
  const vehicleSelect = document.getElementById("vehicle");
  const mechanicSelect = document.getElementById("mechanic");
  const titleInput = document.getElementById("job_title");
  const notesInput = document.getElementById("notes");
  const statusSelect = document.getElementById("status");
  const formAction = document.getElementById("form_action");

  if (!modal || !jobForm) {
    console.error("❌ Required elements missing in HTML.");
    return;
  }

  // --- MODAL CONTROL ---
  const openModal = () => {
    modal.style.display = "flex";
    modal.setAttribute("aria-hidden", "false");
  };

  const closeModal = () => {
    modal.style.display = "none";
    modal.setAttribute("aria-hidden", "true");
    jobForm.reset();
    [vehicleSelect, mechanicSelect, titleInput, notesInput, statusSelect].forEach(el => el.disabled = false);
    saveBtn.style.display = "";
    formAction.value = "create_job";
    modalTitle.textContent = "Assign New Job";
    saveBtn.textContent = "Create Job";
  };

  // --- BUTTON HANDLERS ---
  if (addJobBtn) {
    addJobBtn.addEventListener("click", () => {
      console.log("➕ Add Job clicked");
      modalTitle.textContent = "Assign New Job";
      saveBtn.textContent = "Create Job";
      formAction.value = "create_job";
      jobIdDisplay.value = "Auto-generated";
      statusSelect.value = "assigned";
      openModal();
    });
  }

  cancelBtn?.addEventListener("click", closeModal);
  modalClose?.addEventListener("click", closeModal);
  modal.addEventListener("click", e => { if (e.target === modal) closeModal(); });

  // --- DYNAMIC EVENT DELEGATION ---
  document.body.addEventListener("click", (e) => {
    // ==== EDIT BUTTON ====
    if (e.target.classList.contains("edit-btn")) {
      const row = e.target.closest("tr");
      if (!row || !row.dataset.job) {
        console.error("❌ Missing data-job attribute in row.");
        return;
      }

      let job;
      try {
        job = JSON.parse(row.dataset.job);
      } catch (err) {
        console.error("❌ Invalid JSON in data-job:", err);
        return;
      }

      console.log("✏️ Editing job:", job);

      modalTitle.textContent = "Edit Job";
      saveBtn.textContent = "Update Job";
      formAction.value = "update_job";

      jobIdInput.value = job.job_id || "";
      jobIdDisplay.value = job.job_id || "";
      vehicleSelect.value = job.vehicle_id || "";
      mechanicSelect.value = job.assigned_mechanic_id || "";
      titleInput.value = job.job_title || "";
      notesInput.value = job.notes || "";
      statusSelect.value = job.status || "assigned";

      openModal();
    }

    // ==== VIEW BUTTON ====
    if (e.target.classList.contains("view-btn")) {
      const row = e.target.closest("tr");
      if (!row || !row.dataset.job) {
        console.error("❌ Missing data-job attribute in row.");
        return;
      }

      let job;
      try {
        job = JSON.parse(row.dataset.job);
      } catch (err) {
        console.error("❌ Invalid JSON in data-job:", err);
        return;
      }

      console.log("👁 Viewing job:", job);

      modalTitle.textContent = "View Job Details";
      formAction.value = "";
      saveBtn.style.display = "none";

      jobIdDisplay.value = job.job_id || "";
      vehicleSelect.value = job.vehicle_id || "";
      mechanicSelect.value = job.assigned_mechanic_id || "";
      titleInput.value = job.job_title || "";
      notesInput.value = job.notes || "";
      statusSelect.value = job.status || "assigned";

      [vehicleSelect, mechanicSelect, titleInput, notesInput, statusSelect].forEach(el => el.disabled = true);

      openModal();
    }
  });

  // --- CONFIRM UPDATE ---
  jobForm.addEventListener("submit", (e) => {
    if (formAction.value === "update_job") {
      const confirmUpdate = confirm("Do you want to save the changes to this job?");
      if (!confirmUpdate) e.preventDefault();
    }
  });

  console.log("✅ scripts.js initialized successfully");
}); 