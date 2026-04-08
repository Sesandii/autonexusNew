(function () {
  const data = window.REPORTS_DATA || {};
  const filters = window.REPORTS_FILTERS || {};
  const BASE = window.REPORTS_BASE || "";

  function toChartXY(rows) {
    return {
      labels: (rows || []).map((r) => r.label),
      values: (rows || []).map((r) => Number(r.value || 0)),
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
    el.setAttribute("target", "_blank");
  }

  function bindExports(csvId, pdfId, key) {
    bindLink(csvId, exportUrlCsv(key));
    bindLink(pdfId, exportUrlPdf(key));
  }

  document.querySelectorAll(".tab-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      document.querySelectorAll(".tab-btn").forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");

      const tab = btn.dataset.tab;
      document.querySelectorAll(".tab-content").forEach((c) => (c.style.display = "none"));
      const target = document.getElementById("tab-" + tab);
      if (target) target.style.display = "block";
    });
  });

  const kpis = document.getElementById("kpis");
  if (kpis) {
    const avgCompletion = Number(data.service?.avgCompletionMins ?? 0);
    const avgInvoice = Number(data.revenue?.avgInvoice ?? 0);
    const avgApprovalHours = Number(data.approval?.avgApprovalHours ?? 0);
    const avgRevenuePerAppointment = Number(data.revenue?.avgRevenuePerAppointment ?? 0);
    const avgRevenuePerCustomer = Number(data.revenue?.avgRevenuePerCustomer ?? 0);
    const avgWaitingMins = Number(data.service?.avgWaitingMins ?? 0);
    const feedbackResponseTurnaround = Number(data.feedback?.feedbackResponseTurnaround ?? 0);

    kpis.innerHTML = `
      <div class="kpi"><div class="label">Avg Completion Time</div><div class="value">${avgCompletion.toFixed(0)} mins</div></div>
      <div class="kpi"><div class="label">Avg Waiting Time</div><div class="value">${avgWaitingMins.toFixed(0)} mins</div></div>
      <div class="kpi"><div class="label">Avg Invoice Value</div><div class="value">${avgInvoice.toFixed(2)}</div></div>
      <div class="kpi"><div class="label">Avg Revenue / Appointment</div><div class="value">${avgRevenuePerAppointment.toFixed(2)}</div></div>
      <div class="kpi"><div class="label">Avg Revenue / Customer</div><div class="value">${avgRevenuePerCustomer.toFixed(2)}</div></div>
      <div class="kpi"><div class="label">Avg Approval Time</div><div class="value">${avgApprovalHours.toFixed(1)} hrs</div></div>
      <div class="kpi"><div class="label">Feedback Response Time</div><div class="value">${feedbackResponseTurnaround.toFixed(1)} hrs</div></div>
    `;
  }

  function makeChart(canvasId, type, rows) {
    const el = document.getElementById(canvasId);
    if (!el) return null;

    const { labels, values } = toChartXY(rows);
    return new Chart(el, {
      type,
      data: {
        labels,
        datasets: [{ label: "Value", data: values }],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
      },
    });
  }

  function makeBar(id, rows) { return makeChart(id, "bar", rows); }
  function makeLine(id, rows) { return makeChart(id, "line", rows); }
  function makeDoughnut(id, rows) { return makeChart(id, "doughnut", rows); }

  makeBar("chartTopServices", data.service?.topServices || []);
  makeLine("chartServiceTrend", data.service?.trend || []);
  makeDoughnut("chartServiceTypeDist", data.service?.typeDist || []);
  makeBar("chartWeekdayDemand", data.service?.weekdayDemand || []);
  makeLine("chartSeasonalDemand", data.service?.seasonalDemand || []);
  makeBar("chartTurnaroundByBranch", data.service?.turnaroundByBranch || []);
  makeDoughnut("chartRepeatCustomerFrequency", data.service?.repeatCustomerFrequency || []);
  makeBar("chartMostRebookedServices", data.service?.mostRebookedServices || []);

  makeLine("chartRevenueTrend", data.revenue?.trend || []);
  makeLine("chartCostTrend", data.revenue?.costTrend || []);
  makeLine("chartProfitTrend", data.revenue?.profitTrend || []);
  makeBar("chartRevenueByBranch", data.revenue?.byBranch || []);
  makeDoughnut("chartRevenueByServiceType", data.revenue?.byServiceType || []);
  makeBar("chartUnpaidInvoiceAging", data.revenue?.unpaidInvoiceAging || []);
  makeDoughnut("chartPaymentMethodBreakdown", data.revenue?.paymentMethodBreakdown || []);
  makeDoughnut("chartPaymentStatusBreakdown", data.revenue?.paymentStatusBreakdown || []);
  makeBar("chartBranchPaymentCollection", data.revenue?.branchPaymentCollectionPerformance || []);

  makeDoughnut("chartApptStatus", data.appointments?.status || []);
  makeBar("chartApptByHour", data.appointments?.byHour || []);
  makeLine("chartApptTrend", data.appointments?.trend || []);
  makeLine("chartCancellationTrend", data.appointments?.cancellationTrend || []);

  makeBar("chartBranchCompleted", data.branches?.completed || []);
  makeBar("chartBranchRating", data.branches?.avgRating || []);
  makeBar("chartBranchCapacityUtilization", data.branches?.capacityUtilization || []);
  makeBar("chartBranchStaffingVsWorkload", data.branches?.staffingVsWorkload || []);
  makeBar("chartBranchServiceCoverage", data.branches?.serviceCoverageMatrix || []);
  makeBar("chartBranchComplaintRate", data.branches?.complaintRate || []);
  makeBar("chartBranchApprovalRejectionRate", data.branches?.approvalRejectionRate || []);
  makeBar("chartBranchQualityScore", data.branches?.qualityScore || []);
  makeBar("chartUnderperformingBranches", data.branches?.underperformingBranches || []);

  makeBar("chartJobsPerMechanic", data.staff?.jobsPerMechanic || []);
  makeBar("chartSubmittedByManagers", data.staff?.submittedByManagers || []);
  makeBar("chartManagerApprovalDecisions", data.staff?.managerApprovalDecisions || []);
  makeBar("chartMechanicQualityOutcomes", data.staff?.mechanicQualityOutcomes || []);
  makeBar("chartStaffComplaintAssociation", data.staff?.staffComplaintAssociation || []);
  makeBar("chartAvgJobsPerDayPerMechanic", data.staff?.avgJobsPerDayPerMechanic || []);
  makeBar("chartDelayedWorkOrdersByMechanic", data.staff?.delayedWorkOrdersByMechanic || []);

  makeDoughnut("chartRatingDist", data.feedback?.ratingDist || []);
  makeLine("chartFeedbackTrend", data.feedback?.trend || []);
  makeBar("chartLowestRated", data.feedback?.lowestRated || []);
  makeBar("chartBranchRatingTrend", data.feedback?.branchRatingTrend || []);
  makeBar("chartRatingByServiceType", data.feedback?.ratingByServiceType || []);
  makeBar("chartMostPraisedServices", data.feedback?.mostPraisedServices || []);
  makeBar("chartRepeatNegativeCustomers", data.feedback?.repeatNegativeFeedbackCustomers || []);

  makeBar("chartApprovalStatus", data.approval?.statusCounts || []);

  makeLine("chartComplaintTrend", data.complaints?.trend || []);
  makeLine("chartComplaintResolutionTrend", data.complaints?.resolutionTrend || []);
  makeBar("chartComplaintClosureRate", data.complaints?.closureRateByBranch || []);
  makeDoughnut("chartComplaintPriorityAnalysis", data.complaints?.priorityAnalysis || []);
  makeBar("chartMostComplainedServices", data.complaints?.mostComplainedServices || []);
  makeBar("chartMostComplainedBranches", data.complaints?.mostComplainedBranches || []);
  makeBar("chartMostComplainedStaff", data.complaints?.mostComplainedStaff || []);
  makeLine("chartSlaBreachTrend", data.complaints?.slaBreachTrend || []);
})();