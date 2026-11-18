/*
 * Trang Tổng quan — TV5. Vẽ biểu đồ tròn chi theo danh mục bằng Chart.js.
 * Dữ liệu do PHP in sẵn vào thẻ <script type="application/json" id="expenseChartData">.
 */
(function () {
    const canvas = document.getElementById('expenseChart');
    const dataEl = document.getElementById('expenseChartData');
    if (!canvas || !dataEl || typeof Chart === 'undefined') return;

    const data = JSON.parse(dataEl.textContent);
    // Bảng màu biểu đồ: xanh chủ đạo trước, các màu sau phân biệt rõ với nhau
    const palette = ['#2F6F5E', '#E5484D', '#1D5FA8', '#D9A21B', '#7A5AF8', '#0E9384', '#C4320A', '#5F676A'];

    new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels: data.labels,
            datasets: [{
                data: data.values,
                backgroundColor: data.values.map((_, i) => palette[i % palette.length]),
                borderWidth: 2,
                borderColor: '#FFFFFF',
            }],
        },
        options: {
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: { position: 'bottom', labels: { font: { family: 'Inter', size: 14 }, boxWidth: 12 } },
                tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${formatMoney(ctx.parsed)}` } },
            },
        },
    });
})();
