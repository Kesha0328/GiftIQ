// Chart.js - Local Version (for GiftIQ Admin Dashboard)
document.addEventListener("DOMContentLoaded", () => {
  const ctxDaily = document.getElementById("dailyChart")?.getContext("2d");
  const ctxMonthly = document.getElementById("monthlyChart")?.getContext("2d");
  const ctxTop = document.getElementById("topProductsChart")?.getContext("2d");

  window.dailyChart = new Chart(ctxDaily, {
    type: "line",
    data: { labels: [], datasets: [{ label: "Revenue (₹)", data: [], borderColor: "#e87c7c", backgroundColor: "rgba(232,124,124,0.2)", tension: 0.3, fill: true }] },
    options: { responsive: true, animation: { duration: 800 } }
  });

  window.monthlyChart = new Chart(ctxMonthly, {
    type: "bar",
    data: { labels: [], datasets: [{ label: "Monthly Revenue (₹)", data: [], backgroundColor: "rgba(255,193,158,0.6)", borderColor: "#f7bfa5", borderWidth: 2 }] },
    options: { responsive: true, animation: { duration: 800 } }
  });

  window.productChart = new Chart(ctxTop, {
    type: "pie",
    data: { labels: [], datasets: [{ data: [], backgroundColor: ["#ffb6b9","#f6eec7","#bbded6","#8ac6d1","#f9d56e"] }] },
    options: { responsive: true, animation: { duration: 800 } }
  });
});
