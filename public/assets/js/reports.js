/*
 * Trang Báo cáo — TV5. Biểu đồ cột thu/chi 12 tháng và biểu đồ tròn chi theo danh mục (Chart.js).
 * Dữ liệu do PHP in sẵn trong <script type="application/json" id="reportData">.
 */
(function () {
    const dataEl = document.getElementById('reportData');
    if (!dataEl || typeof Chart === 'undefined') return;
    const data = JSON.parse(dataEl.textContent);
    const font = { family: 'Inter', size: 14 };
    const shortMoney = v => v >= 1e6 ? (v / 1e6).toLocaleString('vi-VN', { maximumFractionDigits: 1 }) + ' tr' : (v / 1e3).toLocaleString('vi-VN') + 'k';

    const monthly = document.getElementById('monthlyChart');
    if (monthly) {
        new Chart(monthly, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [
                    { label: 'Thu', data: data.income, backgroundColor: '#2F6F5E', borderRadius: 4 },
                    { label: 'Chi', data: data.expense, backgroundColor: '#E5484D', borderRadius: 4 },
                ],
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, ticks: { font, callback: shortMoney }, grid: { color: '#E6EBEA' } },
                    x: { ticks: { font }, grid: { display: false } },
                },
                plugins: {
                    legend: { position: 'bottom', labels: { font, boxWidth: 12 } },
                    tooltip: { callbacks: { label: ctx => ` ${ctx.dataset.label}: ${formatMoney(ctx.parsed.y)}` } },
                },
            },
        });
    }

    const category = document.getElementById('categoryChart');
    if (category) {
        const palette = ['#2F6F5E', '#E5484D', '#1D5FA8', '#D9A21B', '#7A5AF8', '#0E9384', '#C4320A', '#5F676A'];
        new Chart(category, {
            type: 'doughnut',
            data: {
                labels: data.catLabels,
                datasets: [{ data: data.catValues, backgroundColor: data.catValues.map((_, i) => palette[i % palette.length]), borderWidth: 2, borderColor: '#FFFFFF' }],
            },
            options: {
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: { position: 'bottom', labels: { font, boxWidth: 12 } },
                    tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${formatMoney(ctx.parsed)}` } },
                },
            },
        });
    }
})();
