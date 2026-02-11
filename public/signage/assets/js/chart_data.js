document.addEventListener("DOMContentLoaded", function () {
    // PLUGIN: SCALE LINES (SPEEDOMETER STYLE) ---
    const gaugeScalePlugin = {
        id: "gaugeScale",
        afterDatasetsDraw: (chart, args, options) => {
            const { ctx } = chart;
            const meta = chart.getDatasetMeta(0);

            const xCenter = meta.data[0].x;
            const yCenter = meta.data[0].y;
            const outerRadius = meta.data[0].outerRadius;

            const tickGap = 10;
            const tickLenMajor = 12;
            const tickLenMinor = 6;

            ctx.save();

            for (let i = 0; i <= 100; i += 2) {
                const angle = Math.PI + (i / 100) * Math.PI;
                const isMajor = i % 10 === 0;
                const length = isMajor ? tickLenMajor : tickLenMinor;

                const startX =
                    xCenter + Math.cos(angle) * (outerRadius + tickGap);
                const startY =
                    yCenter + Math.sin(angle) * (outerRadius + tickGap);
                const endX =
                    xCenter +
                    Math.cos(angle) * (outerRadius + tickGap + length);
                const endY =
                    yCenter +
                    Math.sin(angle) * (outerRadius + tickGap + length);

                ctx.beginPath();
                ctx.lineWidth = isMajor ? 2 : 1;
                ctx.strokeStyle = "#555";
                ctx.moveTo(startX, startY);
                ctx.lineTo(endX, endY);
                ctx.stroke();

                if (isMajor) {
                    const textPadding = 12;
                    const labelRadius =
                        outerRadius + tickGap + length + textPadding;
                    const labelX = xCenter + Math.cos(angle) * labelRadius;
                    const labelY = yCenter + Math.sin(angle) * labelRadius;

                    ctx.font = "bold 10px Montserrat";
                    ctx.fillStyle = "#333";
                    ctx.textAlign = "center";
                    ctx.textBaseline = "middle";
                    ctx.fillText(i, labelX, labelY);
                }
            }
            ctx.restore();
        },
    };

    // CONFIG COMMON
    const gaugeConfig = {
        type: "doughnut",
        plugins: [gaugeScalePlugin],
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: { top: 50, bottom: 24, left: 30, right: 30 },
            },
            rotation: -90,
            circumference: 180,
            cutout: "75%",
            plugins: {
                legend: { display: false },
                tooltip: { enabled: false },
            },
            elements: {
                arc: { borderWidth: 0, borderRadius: 5 },
            },
        },
    };

    // 1. Chart Pendapatan
    const ctx1 = document.getElementById("gaugePendapatan").getContext("2d");
    window.gaugePendapatan = new Chart(ctx1, {
        ...gaugeConfig,
        data: {
            labels: ["Terisi", "Sisa"],
            datasets: [
                {
                    data: [0, 100], // Default 0 dulu sebelum data masuk
                    backgroundColor: ["#164a41", "#E0E0E0"],
                },
            ],
        },
    });

    // 2. Chart Belanja
    const ctx2 = document.getElementById("gaugeBelanja").getContext("2d");
    window.gaugeBelanja = new Chart(ctx2, {
        ...gaugeConfig,
        data: {
            labels: ["Terisi", "Sisa"],
            datasets: [
                {
                    data: [0, 100], // Default 0 dulu
                    backgroundColor: ["#164a41", "#E0E0E0"],
                },
            ],
        },
    });
});
