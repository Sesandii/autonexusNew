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

  const SERVICE_RED = {
    backgroundColor: "rgba(220, 38, 38, 0.78)",
    borderColor: "#b91c1c",
    pointBackgroundColor: "#dc2626",
    pointBorderColor: "#991b1b",
  };
  const REVENUE_RED = {
    backgroundColor: "rgba(220, 38, 38, 0.78)",
    borderColor: "#b91c1c",
    pointBackgroundColor: "#dc2626",
    pointBorderColor: "#991b1b",
  };
  const APPOINTMENTS_RED = {
    backgroundColor: "rgba(220, 38, 38, 0.78)",
    borderColor: "#b91c1c",
    pointBackgroundColor: "#dc2626",
    pointBorderColor: "#991b1b",
  };

  function redPalette(count) {
    const shades = [
      "rgba(220, 38, 38, 0.90)",
      "rgba(185, 28, 28, 0.88)",
      "rgba(239, 68, 68, 0.86)",
      "rgba(153, 27, 27, 0.85)",
      "rgba(248, 113, 113, 0.84)",
      "rgba(127, 29, 29, 0.84)",
    ];
    return Array.from({ length: count }, (_, i) => shades[i % shades.length]);
  }

  function makeChart(canvasId, type, rows, style = null) {
    const el = document.getElementById(canvasId);
    if (!el) return null;

    const { labels, values } = toChartXY(rows);
    const dataset = { label: "Value", data: values };

    if (style) {
      if (type === "bar") {
        dataset.backgroundColor = style.backgroundColor;
        dataset.borderColor = style.borderColor;
        dataset.borderWidth = 1;
      }

      if (type === "line") {
        dataset.borderColor = style.borderColor;
        dataset.backgroundColor = "rgba(220, 38, 38, 0.18)";
        dataset.pointBackgroundColor = style.pointBackgroundColor;
        dataset.pointBorderColor = style.pointBorderColor;
        dataset.tension = 0.3;
        dataset.fill = true;
      }

      if (type === "doughnut") {
        dataset.backgroundColor = redPalette(values.length || 1);
        dataset.borderColor = "#ffffff";
        dataset.borderWidth = 2;
      }
    }

    return new Chart(el, {
      type,
      data: {
        labels,
        datasets: [dataset],
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

  makeChart("chartTopServices", "bar", data.service?.topServices || [], SERVICE_RED);
  makeChart("chartWeekdayDemand", "bar", data.service?.weekdayDemand || [], SERVICE_RED);
  makeChart("chartMostRebookedServices", "bar", data.service?.mostRebookedServices || [], SERVICE_RED);

  makeChart("chartRevenueByBranch", "bar", data.revenue?.byBranch || [], REVENUE_RED);
  makeChart("chartPaymentMethodBreakdown", "doughnut", data.revenue?.paymentMethodBreakdown || [], REVENUE_RED);
  makeChart("chartPaymentStatusBreakdown", "doughnut", data.revenue?.paymentStatusBreakdown || [], REVENUE_RED);

  makeDoughnut("chartApptStatus", data.appointments?.status || []);
  makeChart("chartApptByHour", "bar", data.appointments?.byHour || [], APPOINTMENTS_RED);

  makeChart("chartBranchCompleted", "bar", data.branches?.completed || [], SERVICE_RED);
  makeChart("chartBranchServiceCoverage", "bar", data.branches?.serviceCoverageMatrix || [], SERVICE_RED);

  makeChart("chartJobsPerMechanic", "bar", data.staff?.jobsPerMechanic || [], SERVICE_RED);
  makeChart("chartSubmittedByManagers", "bar", data.staff?.submittedByManagers || [], SERVICE_RED);
  makeChart("chartAvgJobsPerDayPerMechanic", "bar", data.staff?.avgJobsPerDayPerMechanic || [], SERVICE_RED);

  makeDoughnut("chartRatingDist", data.feedback?.ratingDist || []);

  makeBar("chartApprovalStatus", data.approval?.statusCounts || []);

  makeDoughnut("chartComplaintPriorityAnalysis", data.complaints?.priorityAnalysis || []);
})();