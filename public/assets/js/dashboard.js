/*
 * Trang Tổng quan — TV5. Vẽ biểu đồ tròn chi theo danh mục bằng Chart.js.
 * Chuẩn Bento Dashboard, tương thích mượt mà với Dual Theme.
 */
(function () {
    const canvas = document.getElementById('expenseChart');
    const dataEl = document.getElementById('expenseChartData');
    if (!canvas || !dataEl || typeof Chart === 'undefined') return;

    const data = JSON.parse(dataEl.textContent);
    const lightPalette = ['#0F766E', '#E11D48', '#0284C7', '#D97706', '#7C3AED', '#059669', '#EA580C', '#64748B'];
    const darkPalette = ['#14B8A6', '#FB7185', '#38BDF8', '#FBBF24', '#A78BFA', '#34D399', '#FB923C', '#94A3B8'];

    function isDark() {
        return (document.documentElement.getAttribute('data-bs-theme') || localStorage.getItem('theme')) === 'dark';
    }

    const dark = isDark();
    const palette = dark ? darkPalette : lightPalette;

    const chart = new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels: data.labels,
            datasets: [{
                data: data.values,
                backgroundColor: data.values.map((_, i) => palette[i % palette.length]),
                borderWidth: 2,
                borderColor: dark ? '#161920' : '#FFFFFF',
            }],
        },
        options: {
            maintainAspectRatio: false,
            cutout: '62%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: dark ? '#F1F5F9' : '#0F172A',
                        font: { family: 'Inter', size: 13 },
                        boxWidth: 12,
                        padding: 14
                    }
                },
                tooltip: {
                    backgroundColor: dark ? '#1E232D' : '#0F172A',
                    titleColor: '#F8FAFC',
                    bodyColor: '#F8FAFC',
                    borderColor: dark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(15, 23, 42, 0.1)',
                    borderWidth: 1,
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: { label: ctx => ` ${ctx.label}: ${formatMoney(ctx.parsed)}` }
                },
            },
        },
    });

    window.addEventListener('themechanged', event => {
        const isDarkTheme = event.detail.theme === 'dark';
        const updatedPalette = isDarkTheme ? darkPalette : lightPalette;
        chart.data.datasets[0].backgroundColor = data.values.map((_, i) => updatedPalette[i % updatedPalette.length]);
        chart.data.datasets[0].borderColor = isDarkTheme ? '#161920' : '#FFFFFF';
        chart.options.plugins.legend.labels.color = isDarkTheme ? '#F1F5F9' : '#0F172A';
        chart.update();
    });
})();
