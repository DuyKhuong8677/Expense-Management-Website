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
    <nav class="container d-flex align-items-center gap-3 py-3" aria-label="Menu">
        <a class="brand me-auto" href="<?= url($loggedIn ? 'tong-quan' : '') ?>"><i class="ph ph-wallet" aria-hidden="true"></i> Quản lý chi tiêu</a>
        <a class="d-none d-sm-inline text-decoration-none" href="<?= url('huong-dan') ?>">Hướng dẫn</a>
        <?php if ($loggedIn): ?>
            <a class="btn btn-primary" href="<?= url('tong-quan') ?>">Vào trang tổng quan</a>
        <?php else: ?>
            <a class="btn btn-outline-primary" href="<?= url('dang-nhap') ?>">Đăng nhập</a>
            <a class="btn btn-primary" href="<?= url('dang-ky') ?>">Đăng ký</a>
        <?php endif; ?>
    </nav>
</header>

<main class="flex-grow-1">
    <?php if (empty($hideFlash)): ?>
        <div class="container pt-3"><?php require VIEW_PATH . '/partials/flash.php'; ?></div>
    <?php endif; ?>
    <?= $content ?>
</main>

<footer class="public-footer">
    <div class="container d-flex flex-wrap gap-3 justify-content-between">
        <span>© <?= date('Y') ?> Nhóm 4 – Lập trình website, UTH</span>
        <span class="d-flex gap-3">
            <a href="<?= url('huong-dan') ?>">Hướng dẫn</a>
            <a href="<?= url('lien-he') ?>">Liên hệ, góp ý</a>
        </span>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
<script src="<?= asset('js/app.js') ?>" defer></script>
</body>
</html>
<?php clear_form_state(); ?>
