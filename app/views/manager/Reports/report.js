document.addEventListener("DOMContentLoaded", () => {

  /* =====================================================
     Report Configuration (KPIs + Filters)
     ===================================================== */

  const reportConfig = {
    "Revenue & Sales Report": {
      kpis: [
        "Total Revenue",
        "Total Invoices",
        "Paid vs Unpaid Amount",
        "Average Invoice Value",
        "Revenue by Service Type"
      ],
      filters: ["payment_status", "service_type"]
    },

    "Service Frequency": {
      kpis: [
        "Total Services Performed",
        "Most Frequent Service",
        "Least Requested Service",
        "Service Demand Trend"
      ],
      filters: ["service_type"]
    },

    "Pending & Overdue Services": {
      kpis: [
        "Total Pending Jobs",
        "Overdue Jobs",
        "Average Delay Time",
        "Critical Delays"
      ],
      filters: ["technician", "priority"]
    },

    "Customer Feedback Summary": {
      kpis: [
        "Average Customer Rating",
        "Total Feedback Count",
        "Positive vs Negative Feedback",
        "Most Common Complaints"
      ],
      filters: ["rating"]
    },

    "Vehicle Service History": {
      kpis: [
        "Total Vehicles Serviced",
        "Average Visits Per Vehicle",
        "Total Cost Per Vehicle",
        "Last Service Date"
      ],
      filters: ["vehicle_type"]
    },

    "Service Completion Times": {
      kpis: [
        "Average Completion Time",
        "Fastest Service Type",
        "Slowest Service Type",
        "Workflow Bottlenecks"
      ],
      filters: ["service_type"]
    },

    "Technician Performance": {
      kpis: [
        "Jobs Completed",
        "Average Job Duration",
        "Utilization Rate",
        "Revenue Per Technician"
      ],
      filters: ["technician"]
    },

    "Appointment & Workload Report": {
      kpis: [
        "Total Appointments",
        "Walk-in Count",
        "Peak Hours",
        "Missed Appointments"
      ],
      filters: ["day"]
    }
  };

  /* =====================================================
     DOM Elements
     ===================================================== */

  const reportBoxes  = document.querySelectorAll(".report-box");
  const kpiContainer = document.getElementById("kpi-container");

  /* =====================================================
     Helpers
     ===================================================== */

  function renderFilters(filters) {
    if (!filters || !filters.length) return "";

    const map = {
      technician: ["All Technicians", "John", "Alex", "Sam"],
      service_type: ["All Services", "Oil Change", "Brake Repair", "Full Service"],
      payment_status: ["All Payments", "Paid", "Unpaid"],
      vehicle_type: ["All Vehicles", "Car", "Bike", "Van"],
      rating: ["All Ratings", "5 Stars", "4 Stars", "3 Stars"],
      priority: ["All Priorities", "High", "Medium", "Low"],
      day: ["All Days", "Weekdays", "Weekends"]
    };

    let html = `<div class="extra-filters"><h4>Additional Filters</h4><div class="filter-row">`;

    filters.forEach(f => {
      html += `<select name="${f}">`;
      map[f].forEach(opt => html += `<option>${opt}</option>`);
      html += `</select>`;
    });

    html += `</div></div>`;
    return html;
  }

  /* =====================================================
     Event Handling
     ===================================================== */

  reportBoxes.forEach(box => {
    box.addEventListener("click", () => {

      reportBoxes.forEach(b => b.classList.remove("active"));
      box.classList.add("active");

      const reportName = box.dataset.report;
      const cfg = reportConfig[reportName];
      if (!cfg) return;

      kpiContainer.innerHTML = `
        <h4>Key Performance Indicators</h4>
        <ul class="kpi-list">
          ${cfg.kpis.map(k => `<li>${k}</li>`).join("")}
        </ul>
        ${renderFilters(cfg.filters)}
        <input type="hidden" name="report_type" value="${reportName}">
      `;
    });
  });

});
