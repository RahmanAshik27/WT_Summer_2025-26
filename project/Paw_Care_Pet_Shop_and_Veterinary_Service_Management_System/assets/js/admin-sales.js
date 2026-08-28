const salesChart = document.getElementById("salesChart");
const totalSalesCount = document.getElementById("totalSalesCount");
const legendDots = document.querySelectorAll(".legend-dot");

const chartColors = [
    "#ff7a17",
    "#18d77b",
    "#ffd21f",
    "#3fa7ff",
    "#c86cff",
    "#ff5d6c",
    "#00c9c9",
    "#b5df4e",
    "#ff9f43",
    "#6c7cff"
];

function createDonutChart() {
    if (!salesChart || salesCategoryValues.length === 0) return;

    const total = salesCategoryValues.reduce((sum, value) => sum + Number(value), 0);

    if (total <= 0) {
        salesChart.style.background = "#313846";
        if (totalSalesCount) totalSalesCount.textContent = "0";
        return;
    }

    let currentPercent = 0;
    const gradientParts = [];

    salesCategoryValues.forEach((value, index) => {
        const percent = (Number(value) / total) * 100;
        const start = currentPercent;
        const end = currentPercent + percent;
        const color = chartColors[index % chartColors.length];

        gradientParts.push(`${color} ${start}% ${end}%`);
        currentPercent = end;
    });

    salesChart.style.background = `conic-gradient(${gradientParts.join(", ")})`;

    if (totalSalesCount) totalSalesCount.textContent = total;

    legendDots.forEach((dot, index) => {
        dot.style.background = chartColors[index % chartColors.length];
    });
}

createDonutChart();