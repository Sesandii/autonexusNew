document.addEventListener("DOMContentLoaded", () => {
  const reportBoxes = document.querySelectorAll(".report-box");
  const form = document.getElementById("report-form");
  const resultsSection = document.getElementById("report-results");
  const resultsTable = document.getElementById("results-table");
  
  // Make sure BASE_URL exists
  const BASE_URL = window.BASE_URL;
  if (!BASE_URL) {
    console.error("BASE_URL is not defined!");
    return;
  }

  // Helper to format JS Date to yyyy-mm-dd
  const formatDate = (d) => d.toISOString().split('T')[0];

  // Load filters when report type is clicked
  reportBoxes.forEach(box => {
    box.addEventListener("click", async () => {
      reportBoxes.forEach(b => b.classList.remove("active"));
      box.classList.add("active");

      const reportName = box.dataset.report;

      // Map report display name to report_type
      const reportMap = {
        "Revenue & Sales Report": "revenue",
        "Pending & Overdue Services": "pending_services",
        "Service Completion Times": "service_times"
        /*"Technician Performance": "mechanic_performance",
        "Customer Feedback Summary": "feedback",
        "Vehicle Service History": "vehicle_history",
        "Appointment & Workload Report": "appointment_workload"*/
      };

      const reportType = reportMap[reportName] || "";

      if (!reportType) {
        form.innerHTML = `<p>No filters for this report.</p>`;
        return;
      }

      form.innerHTML = `<p>Loading filters...</p>`;

      try {
        const res = await fetch(`${BASE_URL}/manager/reports/getFilters?report=${reportType}`);
        if (!res.ok) throw new Error("Failed to fetch filters");
        const data = await res.json();

        let html = `<input type="hidden" name="report_type" value="${reportType}">`;

        // ----------------------
        // Revenue report filters
        if (reportType === "revenue" && data.services) {
          html += `<label>Service Type</label>
                   <select name="service_type">
                     <option value="">All Services</option>`;
          data.services.forEach(s => {
            html += `<option value="${s.service_id}">${s.name}</option>`;
          });
          html += `</select>`;

          html += `<label>Metrics</label>
                   <div class="checkbox-group">
                     <label><input type="checkbox" name="metrics[]" value="total_revenue" checked> Total Revenue</label>
                     <label><input type="checkbox" name="metrics[]" value="invoice_count"> Invoice Count</label>
                   </div>`;
        }

        // Mechanic performance
       /* if (reportType === "mechanic_performance" && data.mechanics) {
          html += `<label>Select Mechanic</label>
                   <select name="mechanic_id">
                     <option value="">All Mechanics</option>`;
          data.mechanics.forEach(m => {
            html += `<option value="${m.mechanic_id}">${m.name}</option>`;
          });
          html += `</select>`;

          html += `<label>Metrics</label>
                   <div class="checkbox-group">
                     <label><input type="checkbox" name="metrics[]" value="jobs_done" checked> Jobs Completed</label>
                     <label><input type="checkbox" name="metrics[]" value="avg_time"> Average Job Time</label>
                     <label><input type="checkbox" name="metrics[]" value="revenue"> Revenue Contribution</label>
                   </div>`;
        }
*/
        // Pending services
        if (reportType === "pending_services" && data.statuses) {
          html += `<label>Status</label><div class="checkbox-group">`;
          data.statuses.forEach(s => {
            html += `<label><input type="checkbox" name="status[]" value="${s}" checked> ${s.charAt(0).toUpperCase() + s.slice(1)}</label>`;
          });
          html += `</div>`;
        }

        if (reportType === "service_times") {
          html = `<input type="hidden" name="report_type" value="${reportType}">`;
        }

        // ----------------------
        // Add date range inputs (JS-generated)
        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);

        html += `
          <label>From Date</label>
          <input type="date" name="from_date" value="${formatDate(firstDay)}" required>

          <label>To Date</label>
          <input type="date" name="to_date" value="${formatDate(today)}" required>

          <div class="actions">
            <button type="reset" class="cancel">Cancel</button>
            <button type="submit" class="generate">📑 Generate Report</button>
          </div>
        `;

        form.innerHTML = html;

      } catch (err) {
        console.error(err);
        form.innerHTML = `<p style="color:red;">Failed to load filters.</p>`;
      }
    });
  });

  // ----------------------
  // Handle form submission
  /*form.addEventListener("submit", async (e) => {
    e.preventDefault();
    const formData = new FormData(form);

    try {
      const res = await fetch(`${BASE_URL}/manager/reports/generate`, {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
      });

      if (!res.ok) throw new Error("Network error");

      const html = await res.text();
      resultsTable.innerHTML = html;
      resultsSection.style.display = "block";
      window.scrollTo({ top: resultsSection.offsetTop, behavior: "smooth" });

    } catch (err) {
      console.error(err);
      resultsTable.innerHTML = `<p style="color:red;">Failed to generate report.</p>`;
      resultsSection.style.display = "block";
    }
  });*/
});
