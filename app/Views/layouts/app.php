<?php
/**
 * Khung trang sau khi đăng nhập: thanh bên (máy tính) / thanh dưới (điện thoại) + header + nội dung.
 * Biến: $content (HTML của view), $pageTitle, $scripts (mảng file JS riêng của trang, tùy chọn).
 */
use App\Core\Auth;

$user = Auth::user();
$initial = mb_strtoupper(mb_substr($user['full_name'] ?? '?', 0, 1));
$noindex = true;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?php require VIEW_PATH . '/partials/head.php'; ?>
</head>
<body>
<div class="app-shell">
    <aside class="sidebar" aria-label="Menu chính">
        <a class="brand" href="<?= url('tong-quan') ?>"><i class="ph ph-wallet" aria-hidden="true"></i> Quản lý chi tiêu</a>

        <nav class="nav flex-column gap-1">
            <a class="nav-link<?= active('tong-quan') ?>" href="<?= url('tong-quan') ?>"><i class="ph ph-squares-four" aria-hidden="true"></i> Tổng quan</a>
            <a class="nav-link<?= active('giao-dich') ?>" href="<?= url('giao-dich') ?>"><i class="ph ph-list-bullets" aria-hidden="true"></i> Giao dịch</a>
            <a class="nav-link<?= active('vi') ?>" href="<?= url('vi') ?>"><i class="ph ph-wallet" aria-hidden="true"></i> Ví tiền</a>
            <a class="nav-link<?= active('danh-muc') ?>" href="<?= url('danh-muc') ?>"><i class="ph ph-tag" aria-hidden="true"></i> Danh mục</a>
            <a class="nav-link<?= active('ngan-sach') ?>" href="<?= url('ngan-sach') ?>"><i class="ph ph-target" aria-hidden="true"></i> Ngân sách</a>
            <a class="nav-link<?= active('bao-cao') ?>" href="<?= url('bao-cao') ?>"><i class="ph ph-chart-pie-slice" aria-hidden="true"></i> Báo cáo</a>
        </nav>

        <?php if (Auth::isAdmin()): ?>
            <div class="nav-group">Quản trị</div>
            <nav class="nav flex-column">
                <a class="nav-link<?= active('admin') ?>" href="<?= url('admin') ?>"><i class="ph ph-shield-check" aria-hidden="true"></i> Trang quản trị</a>
            </nav>
        <?php endif; ?>

        <div class="mt-auto">
            <a class="nav-link<?= active('huong-dan') ?>" href="<?= url('huong-dan') ?>"><i class="ph ph-question" aria-hidden="true"></i> Hướng dẫn</a>
            <a class="nav-link<?= active('lien-he') ?>" href="<?= url('lien-he') ?>"><i class="ph ph-chat-circle-text" aria-hidden="true"></i> Góp ý</a>
        </div>
    </aside>

    <div class="app-main">
        <header class="app-header">
            <h1><?= e($pageTitle) ?></h1>

            <!-- Nút chính duy nhất trên màn hình (CL-06). TV3 gắn hộp thoại thêm giao dịch vào nút này. -->
            <a class="btn btn-primary" href="<?= url('giao-dich?them=1') ?>" id="btnAddTransaction">
                <i class="ph ph-plus" aria-hidden="true"></i><span class="btn-add-label"> Thêm giao dịch</span>
            </a>

            <div class="dropdown">
                <button class="btn p-0 border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Tài khoản">
                    <span class="avatar"><?= e($initial) ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><span class="dropdown-item-text fw-semibold"><?= e($user['full_name']) ?></span></li>
                    <li><span class="dropdown-item-text text-muted"><?= e($user['email']) ?></span></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="<?= url('tai-khoan') ?>"><i class="ph ph-user-circle" aria-hidden="true"></i> Tài khoản</a></li>
                    <li>
                        <form method="post" action="<?= url('dang-xuat') ?>">
                            <?= csrf_field() ?>
                            <button class="dropdown-item" type="submit"><i class="ph ph-sign-out" aria-hidden="true"></i> Đăng xuất</button>
                        </form>
                    </li>
                </ul>
            </div>
        </header>

        <main class="app-content">
            <?php require VIEW_PATH . '/partials/flash.php'; ?>
            <?= $content ?>
        </main>
    </div>

    <nav class="bottom-nav" aria-label="Menu chính">
        <a class="<?= trim(active('tong-quan')) ?>" href="<?= url('tong-quan') ?>"><i class="ph ph-squares-four" aria-hidden="true"></i>Tổng quan</a>
        <a class="<?= trim(active('giao-dich')) ?>" href="<?= url('giao-dich') ?>"><i class="ph ph-list-bullets" aria-hidden="true"></i>Giao dịch</a>
        <a class="<?= trim(active('ngan-sach')) ?>" href="<?= url('ngan-sach') ?>"><i class="ph ph-target" aria-hidden="true"></i>Ngân sách</a>
        <a class="<?= trim(active('bao-cao')) ?>" href="<?= url('bao-cao') ?>"><i class="ph ph-chart-pie-slice" aria-hidden="true"></i>Báo cáo</a>
        <a class="<?= trim(active('vi')) ?>" href="<?= url('vi') ?>"><i class="ph ph-wallet" aria-hidden="true"></i>Ví</a>
    </nav>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
<?php if (!empty($chartJs)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.js" defer></script>
<?php endif; ?>
<script src="<?= asset('js/app.js') ?>" defer></script>
<?php foreach ($scripts ?? [] as $script): ?>
<script src="<?= asset($script) ?>" defer></script>
<?php endforeach; ?>
</body>
</html>
<?php clear_form_state(); ?>
