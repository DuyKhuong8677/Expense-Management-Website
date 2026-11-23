<?php
/**
 * Trang chờ cho chức năng đang phát triển / admin / tài khoản.
 * Biến: $title, $owner, $branch, $tasks.
 * Bento Dashboard Style kết hợp Lucian-UXUI.
 */
?>
<div class="row g-3">
    <div class="col-lg-8">
        <!-- Bento Tile: Thông tin phân hệ -->
        <div class="card mb-3">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="empty-icon-wrap mb-0" style="width: 48px; height: 48px; font-size: 24px;">
                            <i class="ph ph-hammer" aria-hidden="true"></i>
                        </div>
                        <div>
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle mb-1">Đang hoàn thiện</span>
                            <h2 class="h3 mb-0"><?= e($title) ?></h2>
                        </div>
                    </div>
                </div>
                <p class="text-muted mb-3">
                    Phân hệ này đang được cấu hình và kiểm thử giao diện theo tiêu chuẩn Bento Fintech.
                </p>
                <div class="d-flex flex-wrap gap-2 pt-2 border-top">
                    <span class="badge bg-light text-secondary border px-2 py-1">
                        <i class="ph ph-user me-1" aria-hidden="true"></i><span>Phụ trách:</span> <strong><?= e($owner) ?></strong>
                    </span>
                    <span class="badge bg-light text-secondary border px-2 py-1">
                        <i class="ph ph-git-branch me-1" aria-hidden="true"></i><span>Nhánh:</span> <code><?= e($branch) ?></code>
                    </span>
                </div>
            </div>
        </div>

        <!-- Bento Card: Danh sách hạng mục công việc -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                    <h3 class="h4 mb-0">Hạng mục công việc</h3>
                    <span class="badge bg-light text-secondary border px-2 py-1"><span class="task-count"><?= count($tasks) ?></span> <span>nhiệm vụ</span></span>
                </div>
                <div class="d-grid gap-2">
                    <?php foreach ($tasks as $idx => $task): ?>
                        <div class="d-flex align-items-center gap-3 p-2 rounded border bg-body-tertiary">
                            <span class="stat-icon-badge" style="width: 28px; height: 28px; font-size: 14px;">
                                <i class="ph ph-check" aria-hidden="true"></i>
                            </span>
                            <span class="fw-medium flex-fill"><?= e($task) ?></span>
                            <span class="badge bg-light text-secondary border small">Đang xử lý</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card sticky-lg-top" style="top: calc(var(--header-h) + 16px);">
            <div class="card-body">
                <h3 class="h4 mb-2">Thao tác nhanh</h3>
                <p class="text-muted small mb-3">Chuyển hướng đến các phân hệ quản lý tài chính đang hoạt động ổn định.</p>
                <div class="d-grid gap-2">
                    <a class="btn btn-outline-primary justify-content-start" href="<?= url('tong-quan') ?>">
                        <i class="ph ph-squares-four" aria-hidden="true"></i>
                        <span>Về Tổng quan</span>
                    </a>
                    <a class="btn btn-outline-secondary justify-content-start" href="<?= url('giao-dich') ?>">
                        <i class="ph ph-receipt" aria-hidden="true"></i>
                        <span>Sổ giao dịch</span>
                    </a>
                    <a class="btn btn-outline-secondary justify-content-start" href="<?= url('bao-cao') ?>">
                        <i class="ph ph-chart-donut" aria-hidden="true"></i>
                        <span>Báo cáo & Thống kê</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
