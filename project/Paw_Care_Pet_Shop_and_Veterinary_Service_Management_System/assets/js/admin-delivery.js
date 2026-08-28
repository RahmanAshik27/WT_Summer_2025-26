const deliveryChart = document.getElementById("deliveryChart");
const legendColors = document.querySelectorAll(".legend-color");

const partnerColors = [
    "#20d7ff",
    "#f54291",
    "#ff7a17",
    "#8b5cf6",
    "#20c987"
];

function createDeliveryChart() {
    if (!deliveryChart || deliveryPartnerValues.length === 0) return;

    const values = deliveryPartnerValues.map(value => Number(value));
    const total = values.reduce((sum, value) => sum + value, 0);

    legendColors.forEach((color, index) => {
        color.style.background = partnerColors[index % partnerColors.length];
    });

    if (total <= 0) {
        deliveryChart.style.background = "#2c3443";
        return;
    }

    let currentPercent = 0;
    const gradientParts = [];

    values.forEach((value, index) => {
        const percent = (value / total) * 100;
        const start = currentPercent;
        const end = currentPercent + percent;
        const color = partnerColors[index % partnerColors.length];

        gradientParts.push(`${color} ${start}% ${end}%`);
        currentPercent = end;
    });

    deliveryChart.style.background = `conic-gradient(${gradientParts.join(", ")})`;
}

createDeliveryChart();