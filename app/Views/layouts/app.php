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
    <!-- Nền mờ khi mở menu trên điện thoại -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- Floating Glassy Sidebar V5 -->
    <aside class="sidebar glassy-sidebar" id="appSidebar" aria-label="Menu chính">
        <!-- Nút tròn thu gọn / mở rộng menu (< / >) -->
        <button class="sidebar-toggle-btn" id="sidebarToggleBtn" type="button" aria-expanded="true" aria-label="Thu gọn menu" title="Thu gọn menu (Ctrl + B)">
            <i class="ph ph-caret-left" aria-hidden="true"></i>
        </button>

        <!-- Phần đầu: Logo & Tiêu đề thương hiệu -->
        <div class="sidebar-header">
            <a class="brand" href="<?= url('tong-quan') ?>" title="Quản lý chi tiêu" data-tooltip="Quản lý chi tiêu">
                <span class="brand-logo"><i class="ph ph-wallet" aria-hidden="true"></i></span>
                <div class="brand-details">
                    <span class="brand-title">Quản lý chi tiêu</span>
                    <span class="brand-subtitle">Thu chi cá nhân</span>
                </div>
            </a>
        </div>

        <!-- Thanh tìm kiếm nhanh menu -->
        <div class="sidebar-search">
            <div class="search-input-wrap">
                <i class="ph ph-magnifying-glass search-icon" aria-hidden="true"></i>
                <input type="text" class="sidebar-search-input" id="sidebarSearchInput" placeholder="Tìm nhanh trang..." aria-label="Tìm nhanh trang" autocomplete="off">
                <button class="search-clear-btn d-none" id="sidebarSearchClear" type="button" aria-label="Xóa tìm kiếm">
                    <i class="ph ph-x" aria-hidden="true"></i>
                </button>
            </div>
            <button class="search-collapsed-btn" id="sidebarSearchCollapsedBtn" type="button" aria-label="Tìm kiếm trang" title="Tìm nhanh trang" data-tooltip="Tìm nhanh trang">
                <i class="ph ph-magnifying-glass" aria-hidden="true"></i>
            </button>
        </div>

        <!-- Thân Sidebar: Danh sách các phân hệ -->
        <div class="sidebar-body" id="sidebarNavBody">
            <div class="nav-section">
                <span class="nav-group">CHÍNH</span>
                <nav class="nav flex-column gap-1">
                    <a class="nav-link<?= active('tong-quan') ?>" href="<?= url('tong-quan') ?>" data-tooltip="Tổng quan">
                        <i class="ph ph-squares-four" aria-hidden="true"></i>
                        <span class="nav-label">Tổng quan</span>
                    </a>
                    <a class="nav-link<?= active('giao-dich') ?>" href="<?= url('giao-dich') ?>" data-tooltip="Giao dịch">
                        <i class="ph ph-list-bullets" aria-hidden="true"></i>
                        <span class="nav-label">Giao dịch</span>
                    </a>
                    <a class="nav-link<?= active('vi') ?>" href="<?= url('vi') ?>" data-tooltip="Ví tiền">
                        <i class="ph ph-wallet" aria-hidden="true"></i>
                        <span class="nav-label">Ví tiền</span>
                    </a>
                    <a class="nav-link<?= active('danh-muc') ?>" href="<?= url('danh-muc') ?>" data-tooltip="Danh mục">
                        <i class="ph ph-tag" aria-hidden="true"></i>
                        <span class="nav-label">Danh mục</span>
                    </a>
                    <a class="nav-link<?= active('ngan-sach') ?>" href="<?= url('ngan-sach') ?>" data-tooltip="Ngân sách">
                        <i class="ph ph-target" aria-hidden="true"></i>
                        <span class="nav-label">Ngân sách</span>
                        <span class="nav-dot" aria-hidden="true"></span>
                    </a>
                    <a class="nav-link<?= active('bao-cao') ?>" href="<?= url('bao-cao') ?>" data-tooltip="Báo cáo">
                        <i class="ph ph-chart-pie-slice" aria-hidden="true"></i>
                        <span class="nav-label">Báo cáo</span>
                    </a>
                </nav>
            </div>

            <?php if (Auth::isAdmin()): ?>
                <div class="nav-section">
                    <span class="nav-group">QUẢN TRỊ</span>
                    <nav class="nav flex-column gap-1">
                        <a class="nav-link<?= active('admin') ?>" href="<?= url('admin') ?>" data-tooltip="Trang quản trị">
                            <i class="ph ph-shield-check" aria-hidden="true"></i>
                            <span class="nav-label">Trang quản trị</span>
                        </a>
                    </nav>
                </div>
            <?php endif; ?>

            <div class="nav-section mt-auto">
                <span class="nav-group">HỖ TRỢ</span>
                <nav class="nav flex-column gap-1">
                    <a class="nav-link<?= active('huong-dan') ?>" href="<?= url('huong-dan') ?>" data-tooltip="Hướng dẫn">
                        <i class="ph ph-question" aria-hidden="true"></i>
                        <span class="nav-label">Hướng dẫn</span>
                    </a>
                    <a class="nav-link<?= active('lien-he') ?>" href="<?= url('lien-he') ?>" data-tooltip="Góp ý">
                        <i class="ph ph-chat-circle-text" aria-hidden="true"></i>
                        <span class="nav-label">Góp ý</span>
                    </a>
                </nav>
            </div>

            <div class="sidebar-search-empty d-none p-2 text-center text-muted small">
                Không tìm thấy trang phù hợp
            </div>
        </div>

        <!-- Chân Sidebar: thẻ hồ sơ (đổi giao diện và menu tài khoản nằm trên header) -->
        <div class="sidebar-footer">
            <!-- Thẻ hồ sơ cá nhân với avatar + online status dot -->
            <div class="sidebar-user-card" id="sidebarUserCard" data-tooltip="<?= e($user['full_name']) ?> (<?= e($user['email']) ?>)">
                <div class="user-avatar-wrap">
                    <span class="avatar"><?= e($initial) ?></span>
                    <span class="online-status-dot" title="Đang hoạt động"></span>
                </div>
                <div class="user-details min-w-0">
                    <div class="user-name text-truncate"><?= e($user['full_name']) ?></div>
                    <div class="user-role text-muted text-truncate"><?= Auth::isAdmin() ? 'Quản trị viên' : 'Thành viên' ?></div>
                </div>
            </div>
        </div>
    </aside>

    <!-- Nhãn nổi thông minh khi thu gọn menu (Floating Glass Tooltip - Lucian UX/UI) -->
    <div class="sidebar-floating-tooltip" id="sidebarFloatingTooltip" role="tooltip" aria-hidden="true">
        <span class="tooltip-label"></span>
    </div>

    <div class="app-main">
        <header class="app-header">
            <!-- Nút hamburger mở menu trên điện thoại -->
            <button class="btn btn-outline-secondary btn-icon-sm d-lg-none me-2" id="sidebarMobileToggle" type="button" aria-label="Mở menu điều hướng">
                <i class="ph ph-list" aria-hidden="true"></i>
            </button>

            <h1 id="appPageHeaderTitle"><?= e($pageTitle) ?></h1>

            <div class="d-flex align-items-center gap-2 gap-sm-3">
                <!-- Nút chính duy nhất trên màn hình (CL-06). TV3 gắn hộp thoại thêm giao dịch vào nút này. -->
                <a class="btn btn-primary" href="<?= url('giao-dich?them=1') ?>" id="btnAddTransaction">
                    <i class="ph ph-plus" aria-hidden="true"></i><span class="btn-add-label"> Thêm giao dịch</span>
                </a>

                <!-- Công tắc bầu trời Ngày / Đêm Compact (Lucian UX/UI Prompt #021) -->
                <button class="u021-header-sw theme-toggle-btn" id="themeToggleBtn" type="button" role="switch" aria-checked="false" aria-label="Chuyển chế độ Ngày / Đêm" title="Đổi giao diện Ngày / Đêm (Lucian #021)">
                    <span class="u021-h-stars" aria-hidden="true"><i></i><i></i><i></i></span>
                    <span class="u021-h-clouds" aria-hidden="true"><i></i><i></i></span>
                    <span class="u021-h-knob" aria-hidden="true">
                        <span class="u021-h-moon"><i></i><i></i></span>
                    </span>
                </button>

                <div class="dropdown">
                    <button class="btn p-0 border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Tài khoản">
                        <span class="avatar"><?= e($initial) ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li class="px-3 py-2 border-bottom mb-1">
                            <div class="fw-semibold text-truncate" style="max-width: 180px;"><?= e($user['full_name']) ?></div>
                            <div class="text-muted small text-truncate" style="max-width: 180px;"><?= e($user['email']) ?></div>
                        </li>
                        <li><a class="dropdown-item" href="<?= url('tai-khoan') ?>"><i class="ph ph-user-circle" aria-hidden="true"></i> <span>Tài khoản</span></a></li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <form method="post" action="<?= url('dang-xuat') ?>">
                                <?= csrf_field() ?>
                                <button class="dropdown-item text-danger" type="submit"><i class="ph ph-sign-out" aria-hidden="true"></i> <span>Đăng xuất</span></button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <main class="app-content">
            <?php require VIEW_PATH . '/partials/flash.php'; ?>
            <?= $content ?>
        </main>
    </div>

    <!-- Lucian UX/UI Prompt #041 · Notched bottom tab bar (Mobile) -->
    <?php require VIEW_PATH . '/partials/bottom-nav.php'; ?>
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
