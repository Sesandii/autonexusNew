(function () {
  const data = window.REPORTS_DATA || {};
  const filters = window.REPORTS_FILTERS || {};
  const BASE = window.REPORTS_BASE || "";

  function toChartXY(rows) {
    return {
      labels: rows.map(r => r.label),
      values: rows.map(r => Number(r.value || 0))
    };
  }

  function exportUrlCsv(key) {
    const p = new URLSearchParams({
      key,
      from: filters.from || "",
      to: filters.to || "",
      branch_id: filters.branch_id || "",
      group: filters.group || "month",
    });
    return `${BASE}/admin/admin-viewreports/export?${p.toString()}`;
  }

  function exportUrlPdf(key) {
    const p = new URLSearchParams({
      key,
      from: filters.from || "",
      to: filters.to || "",
      branch_id: filters.branch_id || "",
      group: filters.group || "month",
    });
    return `${BASE}/admin/admin-viewreports/export-pdf?${p.toString()}`;
  }

  function bindLink(id, url) {
    const el = document.getElementById(id);
    if (!el) return;
    el.href = url;
    el.setAttribute("target", "_blank"); // optional: open download in new tab
  }

  function bindExports(csvId, pdfId, key) {
    bindLink(csvId, exportUrlCsv(key));
    bindLink(pdfId, exportUrlPdf(key));
  }

  // Tabs
  document.querySelectorAll(".tab-btn").forEach(btn => {
    btn.addEventListener("click", () => {
      document.querySelectorAll(".tab-btn").forEach(b => b.classList.remove("active"));
      btn.classList.add("active");

      const tab = btn.dataset.tab;
      document.querySelectorAll(".tab-content").forEach(c => (c.style.display = "none"));
      const target = document.getElementById("tab-" + tab);
      if (target) target.style.display = "block";
    });
  });

  // KPIs
  const kpis = document.getElementById("kpis");
  if (kpis) {
    const avgCompletion = data.service?.avgCompletionMins ?? 0;
    const avgInvoice = data.revenue?.avgInvoice ?? 0;
    const avgApprovalHours = data.approval?.avgApprovalHours ?? 0;

    kpis.innerHTML = `
      <div class="kpi"><div class="label">Avg Completion Time</div><div class="value">${Number(avgCompletion).toFixed(0)} mins</div></div>
      <div class="kpi"><div class="label">Avg Invoice Value</div><div class="value">${Number(avgInvoice).toFixed(2)}</div></div>
      <div class="kpi"><div class="label">Avg Approval Time</div><div class="value">${Number(avgApprovalHours).toFixed(1)} hrs</div></div>
    `;
  }

  // Chart helpers
  function makeBar(canvas, rows) {
    const { labels, values } = toChartXY(rows);
    return new Chart(canvas, {
      type: "bar",
      data: { labels, datasets: [{ label: "Value", data: values }] },
      options: { responsive: true, maintainAspectRatio: false }
    });
  }

  function makeLine(canvas, rows) {
    const { labels, values } = toChartXY(rows);
    return new Chart(canvas, {
      type: "line",
      data: { labels, datasets: [{ label: "Value", data: values, fill: false }] },
      options: { responsive: true, maintainAspectRatio: false }
    });
  }

  function makeDoughnut(canvas, rows) {
    const { labels, values } = toChartXY(rows);
    return new Chart(canvas, {
      type: "doughnut",
      data: { labels, datasets: [{ label: "Value", data: values }] },
      options: { responsive: true, maintainAspectRatio: false }
    });
  }

  // SERVICE
  const top = document.getElementById("chartTopServices");
  if (top) {
    makeBar(top, data.service?.topServices || []);
    makeLine(document.getElementById("chartServiceTrend"), data.service?.trend || []);
    makeDoughnut(document.getElementById("chartServiceTypeDist"), data.service?.typeDist || []);

    bindExports("exportTopServices", "exportTopServicesPdf", "topServices");
    bindExports("exportServiceTrend", "exportServiceTrendPdf", "serviceTrend");
    bindExports("exportServiceTypeDist", "exportServiceTypeDistPdf", "serviceTypeDistribution");
  }

  // REVENUE
  const rev = document.getElementById("chartRevenueTrend");
  if (rev) {
    makeLine(rev, data.revenue?.trend || []);
    makeBar(document.getElementById("chartRevenueByBranch"), data.revenue?.byBranch || []);
    makeDoughnut(document.getElementById("chartRevenueByServiceType"), data.revenue?.byServiceType || []);

    bindExports("exportRevenueTrend", "exportRevenueTrendPdf", "revenueTrend");
    bindExports("exportRevenueByBranch", "exportRevenueByBranchPdf", "revenueByBranch");
    bindExports("exportRevenueByServiceType", "exportRevenueByServiceTypePdf", "revenueByServiceType");
  }

  // APPOINTMENTS
  const ap = document.getElementById("chartApptStatus");
  if (ap) {
    makeDoughnut(ap, data.appointments?.status || []);
    makeBar(document.getElementById("chartApptByHour"), data.appointments?.byHour || []);
    makeLine(document.getElementById("chartApptTrend"), data.appointments?.trend || []);

    bindExports("exportApptStatus", "exportApptStatusPdf", "appointmentStatusCounts");
    bindExports("exportApptByHour", "exportApptByHourPdf", "appointmentsByHour");
    bindExports("exportApptTrend", "exportApptTrendPdf", "appointmentsTrend");
  }

  // BRANCHES
  const bc = document.getElementById("chartBranchCompleted");
  if (bc) {
    makeBar(bc, data.branches?.completed || []);
    makeBar(document.getElementById("chartBranchRating"), data.branches?.avgRating || []);

    bindExports("exportBranchCompleted", "exportBranchCompletedPdf", "branchCompletedServices");
    bindExports("exportBranchRating", "exportBranchRatingPdf", "branchAvgRating");
  }

  // STAFF
  const jm = document.getElementById("chartJobsPerMechanic");
  if (jm) {
    makeBar(jm, data.staff?.jobsPerMechanic || []);
    makeBar(document.getElementById("chartSubmittedByManagers"), data.staff?.submittedByManagers || []);

    bindExports("exportJobsPerMechanic", "exportJobsPerMechanicPdf", "jobsPerMechanic");
    bindExports("exportSubmittedByManagers", "exportSubmittedByManagersPdf", "servicesSubmittedByManagers");
  }

  // FEEDBACK
  const rd = document.getElementById("chartRatingDist");
  if (rd) {
    makeDoughnut(rd, data.feedback?.ratingDist || []);
    makeLine(document.getElementById("chartFeedbackTrend"), data.feedback?.trend || []);
    makeBar(document.getElementById("chartLowestRated"), data.feedback?.lowestRated || []);

    bindExports("exportRatingDist", "exportRatingDistPdf", "ratingDistribution");
    bindExports("exportFeedbackTrend", "exportFeedbackTrendPdf", "feedbackTrend");
    bindExports("exportLowestRated", "exportLowestRatedPdf", "lowestRatedServices");
  }

  // APPROVAL
  const as = document.getElementById("chartApprovalStatus");
  if (as) {
    makeBar(as, data.approval?.statusCounts || []);
    bindExports("exportApprovalStatus", "exportApprovalStatusPdf", "approvalStatusCounts");
  }
})();
