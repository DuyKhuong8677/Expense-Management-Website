<?php
/**
 * Khung trang công khai: trang chủ, đăng nhập, đăng ký, hướng dẫn, liên hệ, trang lỗi.
 */
use App\Core\Auth;

$loggedIn = Auth::check();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?php require VIEW_PATH . '/partials/head.php'; ?>
</head>
<body class="d-flex flex-column min-vh-100">
<header class="public-header">
    <nav class="container d-flex align-items-center gap-2 gap-sm-3 py-3" aria-label="Menu">
        <a class="brand me-auto" href="<?= url($loggedIn ? 'tong-quan' : '') ?>">
            <i class="ph ph-wallet" aria-hidden="true"></i>
            <span>Quản lý chi tiêu</span>
        </a>
        <a class="d-none d-md-inline text-muted me-2" href="<?= url('huong-dan') ?>">Hướng dẫn</a>

        <!-- Lucian UX/UI Prompt #021 · MIT License (c) 2026 Seokwoooo · https://lucianlabs.dev/uxui-prompt/#021 -->
        <!-- Công tắc bầu trời Ngày / Đêm Compact (Lucian UX/UI Prompt #021) -->
        <button class="u021-header-sw theme-toggle-btn me-1" id="themeToggleBtn" type="button" role="switch" aria-checked="false" aria-label="Chuyển chế độ Ngày / Đêm" title="Đổi giao diện Ngày / Đêm (Lucian #021)">
            <span class="u021-h-stars" aria-hidden="true"><i></i><i></i><i></i></span>
            <span class="u021-h-clouds" aria-hidden="true"><i></i><i></i></span>
            <span class="u021-h-knob" aria-hidden="true">
                <span class="u021-h-moon"><i></i><i></i></span>
            </span>
        </button>

        <?php if ($loggedIn): ?>
            <a class="btn btn-primary" href="<?= url('tong-quan') ?>"><i class="ph ph-squares-four" aria-hidden="true"></i> <span>Vào trang tổng quan</span></a>
        <?php else: ?>
            <a class="btn btn-outline-primary btn-sm btn-sm-md" href="<?= url('dang-nhap') ?>">Đăng nhập</a>
            <a class="btn btn-primary btn-sm btn-sm-md" href="<?= url('dang-ky') ?>">Đăng ký</a>
        <?php endif; ?>
    </nav>
</header>

<main class="flex-grow-1">
    <?php if (empty($hideFlash)): ?>
        <div class="container pt-3"><?php require VIEW_PATH . '/partials/flash.php'; ?></div>
    <?php endif; ?>
    <?= $content ?>
</main>

<footer class="public-footer mt-auto">
    <div class="container d-flex flex-column flex-sm-row gap-3 justify-content-between align-items-center text-center text-sm-start">
        <span>© <?= date('Y') ?> Quản lý chi tiêu · UTH</span>
        <div class="d-flex gap-3">
            <a href="<?= url('huong-dan') ?>" class="text-muted">Hướng dẫn</a>
            <span class="text-muted opacity-50">·</span>
            <a href="<?= url('lien-he') ?>" class="text-muted">Liên hệ, góp ý</a>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
<script src="<?= asset('js/app.js') ?>" defer></script>
</body>
</html>
<?php clear_form_state(); ?>
