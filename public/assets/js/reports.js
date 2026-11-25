/*
 * Trang Báo cáo — TV5. Biểu đồ cột thu/chi 12 tháng và biểu đồ tròn chi theo danh mục (Chart.js).
 * Chuẩn hóa nhãn trục Y, trạng thái rỗng và thích ứng linh hoạt Dual Theme.
 */
(function () {
    const dataEl = document.getElementById('reportData');
    if (!dataEl || typeof Chart === 'undefined') return;
    const data = JSON.parse(dataEl.textContent);
    const font = { family: 'Inter', size: 12 };

    /** Định dạng số tiền rút gọn trên trục biểu đồ: 0 -> "0 ₫", 1.500.000 -> "1,5 tr" */
    const shortMoney = v => {
        if (!v || v === 0) return '0 ₫';
        const abs = Math.abs(v);
        if (abs >= 1e9) return (v / 1e9).toLocaleString('vi-VN', { maximumFractionDigits: 1 }) + ' tỷ';
        if (abs >= 1e6) return (v / 1e6).toLocaleString('vi-VN', { maximumFractionDigits: 1 }) + ' tr';
        if (abs >= 1e3) return (v / 1e3).toLocaleString('vi-VN') + 'k';
        return new Intl.NumberFormat('vi-VN').format(Math.round(v)) + ' ₫';
    };

    function isDark() {
        return (document.documentElement.getAttribute('data-bs-theme') || localStorage.getItem('theme')) === 'dark';
    }

    const lightPalette = ['#0F766E', '#E11D48', '#0284C7', '#D97706', '#7C3AED', '#059669', '#EA580C', '#64748B'];
    const darkPalette = ['#14B8A6', '#FB7185', '#38BDF8', '#FBBF24', '#A78BFA', '#34D399', '#FB923C', '#94A3B8'];

    const monthlyCanvas = document.getElementById('monthlyChart');
    let monthlyChart = null;
    if (monthlyCanvas) {
        const dark = isDark();
        const maxVal = Math.max(...(data.income || [0]), ...(data.expense || [0]), 0);

        monthlyChart = new Chart(monthlyCanvas, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [
                    { label: 'Thu', data: data.income, backgroundColor: dark ? '#34D399' : '#059669', borderRadius: 4, barPercentage: 0.7 },
                    { label: 'Chi', data: data.expense, backgroundColor: dark ? '#FB7185' : '#E11D48', borderRadius: 4, barPercentage: 0.7 },
                ],
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        suggestedMax: maxVal > 0 ? maxVal * 1.15 : 1000000,
                        ticks: {
                            font,
                            color: dark ? '#94A3B8' : '#64748B',
                            callback: shortMoney,
                            maxTicksLimit: 6,
                            precision: 0,
                        },
                        grid: {
                            color: dark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(15, 23, 42, 0.06)',
                            drawBorder: false,
                        }
                    },
                    x: {
                        ticks: { font, color: dark ? '#94A3B8' : '#64748B' },
                        grid: { display: false }
                    },
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { font, color: dark ? '#F1F5F9' : '#0F172A', boxWidth: 12, padding: 16 }
                    },
                    tooltip: {
                        backgroundColor: dark ? '#1E232D' : '#0F172A',
                        titleColor: '#F8FAFC',
                        bodyColor: '#F8FAFC',
                        borderColor: dark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(15, 23, 42, 0.1)',
                        borderWidth: 1,
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: { label: ctx => ` ${ctx.dataset.label}: ${formatMoney(ctx.parsed.y)}` }
                    },
                },
            },
        });
    }

    const categoryCanvas = document.getElementById('categoryChart');
    let categoryChart = null;
    if (categoryCanvas) {
        const dark = isDark();
        const palette = dark ? darkPalette : lightPalette;
        categoryChart = new Chart(categoryCanvas, {
            type: 'doughnut',
            data: {
                labels: data.catLabels,
                datasets: [{
                    data: data.catValues,
                    backgroundColor: data.catValues.map((_, i) => palette[i % palette.length]),
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
                        labels: { font, color: dark ? '#F1F5F9' : '#0F172A', boxWidth: 12, padding: 14 }
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
    }

    window.addEventListener('themechanged', event => {
        const dark = event.detail.theme === 'dark';
        if (monthlyChart) {
            monthlyChart.data.datasets[0].backgroundColor = dark ? '#34D399' : '#059669';
            monthlyChart.data.datasets[1].backgroundColor = dark ? '#FB7185' : '#E11D48';
            monthlyChart.options.scales.y.ticks.color = dark ? '#94A3B8' : '#64748B';
            monthlyChart.options.scales.y.grid.color = dark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(15, 23, 42, 0.06)';
            monthlyChart.options.scales.x.ticks.color = dark ? '#94A3B8' : '#64748B';
            monthlyChart.options.plugins.legend.labels.color = dark ? '#F1F5F9' : '#0F172A';
            monthlyChart.update();
        }
        if (categoryChart) {
            const palette = dark ? darkPalette : lightPalette;
            categoryChart.data.datasets[0].backgroundColor = data.catValues.map((_, i) => palette[i % palette.length]);
            categoryChart.data.datasets[0].borderColor = dark ? '#161920' : '#FFFFFF';
            categoryChart.options.plugins.legend.labels.color = dark ? '#F1F5F9' : '#0F172A';
            categoryChart.update();
        }
    });
})();
