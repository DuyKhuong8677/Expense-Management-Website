<?php
/** Phần <head> dùng chung cho mọi layout. Biến: $pageTitle, $metaDescription (tùy chọn), $noindex */
?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?> | <?= e(config('app.name')) ?></title>
<meta name="description" content="<?= e($metaDescription ?? 'Website quản lý chi tiêu cá nhân: ghi thu chi, đặt ngân sách, xem báo cáo theo tháng.') ?>">
<?php if (!empty($noindex)): ?>
<meta name="robots" content="noindex, nofollow">
<?php endif; ?>
<meta name="theme-color" content="#0B0C0E">
<meta name="csrf-token" content="<?= e(App\Core\Csrf::token()) ?>">
<link rel="icon" href="<?= asset('img/favicon.svg') ?>" type="image/svg+xml">

<!-- Ngăn chặn hiện tượng nhấp nháy giao diện (FOUC) khi tải trang -->
<script>
    (function () {
        const saved = localStorage.getItem('theme');
        const theme = saved || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        document.documentElement.setAttribute('data-bs-theme', theme);
        document.documentElement.setAttribute('data-theme', theme);

        const metaTheme = document.querySelector('meta[name="theme-color"]');
        if (metaTheme) {
            metaTheme.setAttribute('content', theme === 'dark' ? '#0F1115' : '#F6F8FA');
        }

        if (localStorage.getItem('sidebar_collapsed') === 'true' && window.innerWidth >= 992) {
            document.documentElement.classList.add('sidebar-is-collapsed');
        }
    })();
</script>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.1/src/regular/style.css" rel="stylesheet">
<link href="<?= asset('css/variables.css') ?>" rel="stylesheet">
<link href="<?= asset('css/app.css') ?>" rel="stylesheet">

<script>
    window.APP = {
        basePath: <?= json_encode(BASE_PATH) ?>,
        csrf: <?= json_encode(App\Core\Csrf::token()) ?>
    };
</script>
