<?php /* Trang chủ giới thiệu — TV6 hoàn thiện nội dung, ảnh minh họa (unDraw/DrawKit, định dạng SVG/WebP) */ ?>
<section class="hero">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <h1 class="mb-3">Biết tiền mình đi đâu mỗi tháng</h1>
                <p class="fs-5 text-muted mb-4">Ghi một khoản chi chỉ trong vài giây, đặt ngân sách cho từng danh mục và xem biểu đồ chi tiêu theo tháng. Miễn phí, dùng tốt trên điện thoại.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a class="btn btn-primary btn-lg" href="<?= url('dang-ky') ?>">Bắt đầu miễn phí</a>
                    <a class="btn btn-outline-primary btn-lg" href="<?= url('huong-dan') ?>">Xem hướng dẫn</a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <div class="text-muted">Số dư hiện tại</div>
                        <div class="money money-display mb-3">4.350.000 ₫</div>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="text-muted">Thu tháng này</div>
                                <div class="money money-income fw-semibold fs-4">+8.000.000 ₫</div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted">Chi tháng này</div>
                                <div class="money money-expense fw-semibold fs-4">−3.650.000 ₫</div>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="text-muted mt-2 mb-0">Số liệu minh họa</p>
            </div>
        </div>
    </div>
</section>

<section class="pb-5">
    <div class="container">
        <h2 class="mb-4">Ba việc website làm giúp bạn</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-icon"><i class="ph ph-lightning" aria-hidden="true"></i></div>
                <h3>Ghi thu chi nhanh</h3>
                <p class="text-muted">Thêm giao dịch trong tối đa 3 thao tác, chọn ví và danh mục có sẵn.</p>
            </div>
            <div class="col-md-4">
                <div class="feature-icon"><i class="ph ph-target" aria-hidden="true"></i></div>
                <h3>Đặt ngân sách</h3>
                <p class="text-muted">Đặt hạn mức cho từng danh mục, được cảnh báo khi chi tới 80% và khi vượt.</p>
            </div>
            <div class="col-md-4">
                <div class="feature-icon"><i class="ph ph-chart-pie-slice" aria-hidden="true"></i></div>
                <h3>Biểu đồ dễ hiểu</h3>
                <p class="text-muted">Xem tỷ lệ chi theo danh mục và so sánh thu chi qua từng tháng.</p>
            </div>
        </div>
    </div>
</section>
