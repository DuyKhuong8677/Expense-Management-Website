<?php /* Trang chủ giới thiệu */ ?>
<section class="hero">
    <div class="container">
        <div class="row align-items-center g-4 g-lg-5">
            <div class="col-lg-6 text-center text-lg-start">
                <h1 class="mb-3">Biết tiền mình đi đâu mỗi tháng</h1>
                <p class="fs-5 text-muted mb-4 mx-auto mx-lg-0 readable">Ghi một khoản chi chỉ trong vài giây, đặt ngân sách cho từng danh mục và theo dõi biểu đồ chi tiêu trực quan. Hoàn toàn miễn phí, tối ưu cho điện thoại.</p>
                <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start">
                    <a class="btn btn-primary btn-lg" href="<?= url('dang-ky') ?>">
                        <span>Bắt đầu miễn phí</span>
                        <i class="ph ph-arrow-right" aria-hidden="true"></i>
                    </a>
                    <a class="btn btn-outline-primary btn-lg" href="<?= url('huong-dan') ?>">
                        <i class="ph ph-book-open" aria-hidden="true"></i>
                        <span>Xem hướng dẫn</span>
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card shadow-md border">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="stat-label mb-0">Số dư hiện tại</span>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 small">Tháng này</span>
                        </div>
                        <div class="money money-display mb-4">4.350.000 ₫</div>
                        <div class="row g-3 pt-3 border-top">
                            <div class="col-6 border-end">
                                <div class="stat-label">Thu tháng này</div>
                                <div class="money money-income fw-bold fs-4 mt-1">+8.000.000 ₫</div>
                            </div>
                            <div class="col-6 ps-3">
                                <div class="stat-label">Chi tháng này</div>
                                <div class="money money-expense fw-bold fs-4 mt-1">−3.650.000 ₫</div>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="text-muted small mt-2 mb-0 text-center text-lg-start fst-italic">Số liệu minh họa giao diện</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-white border-top border-bottom">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="mb-2">Ba việc website làm giúp bạn</h2>
            <p class="text-muted mx-auto" style="max-width: 50ch;">Thiết kế tinh gọn, tập trung vào những tính năng cốt lõi giúp bạn kiểm soát tài chính cá nhân hiệu quả.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 bg-transparent">
                    <div class="card-body p-0">
                        <div class="feature-icon"><i class="ph ph-lightning" aria-hidden="true"></i></div>
                        <h3 class="h4 mb-2">Ghi thu chi nhanh</h3>
                        <p class="text-muted mb-0">Thêm giao dịch trong tối đa 3 thao tác, chọn ví và danh mục có sẵn mà không tốn thời gian nhập liệu rườm rà.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 bg-transparent">
                    <div class="card-body p-0">
                        <div class="feature-icon"><i class="ph ph-target" aria-hidden="true"></i></div>
                        <h3 class="h4 mb-2">Đặt ngân sách</h3>
                        <p class="text-muted mb-0">Đặt hạn mức cho từng danh mục, tự động cảnh báo khi chi chạm mốc 80% và thông báo rõ ràng khi vượt ngưỡng.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 bg-transparent">
                    <div class="card-body p-0">
                        <div class="feature-icon"><i class="ph ph-chart-pie-slice" aria-hidden="true"></i></div>
                        <h3 class="h4 mb-2">Biểu đồ dễ hiểu</h3>
                        <p class="text-muted mb-0">Xem tỷ lệ chi theo danh mục và so sánh thu chi qua từng tháng bằng biểu đồ trực quan, hỗ trợ xuất file CSV.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Giới thiệu tính năng tương tác Living Bento Grid (Lucian UX/UI Prompt #214) -->
<section class="py-5 border-bottom">
    <div class="container">
        <div class="row align-items-center g-4 g-lg-5">
            <div class="col-lg-6 text-center text-lg-start">
                <div class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 mb-2 fw-semibold">TÍNH NĂNG</div>
                <h2 class="mb-3">Mọi khoản thu chi trong một nơi</h2>
                <p class="text-muted fs-6 mb-4">Đặt ngân sách cho từng danh mục, tìm giao dịch theo ghi chú, xem biểu đồ thu chi theo tháng, quản lý nhiều ví và chuyển tiền giữa các ví. Mật khẩu được mã hóa, dữ liệu của ai chỉ người đó xem được.</p>
                <div class="d-flex flex-wrap gap-2 justify-content-center justify-content-lg-start text-muted small">
                    <span class="d-inline-flex align-items-center gap-1"><i class="ph ph-check-circle text-success" aria-hidden="true"></i> Ngân sách tháng</span>
                    <span class="d-inline-flex align-items-center gap-1 ms-3"><i class="ph ph-check-circle text-success" aria-hidden="true"></i> Tìm kiếm nhanh</span>
                    <span class="d-inline-flex align-items-center gap-1 ms-3"><i class="ph ph-check-circle text-success" aria-hidden="true"></i> Nhiều ví tiền</span>
                </div>
            </div>
            <div class="col-lg-6 d-flex justify-content-center">
                <?php require VIEW_PATH . '/partials/bento-features.php'; ?>
            </div>
        </div>
    </div>
</section>
