<?php
/**
 * Tổng quan — TV5. Biến: $totals, $balance, $byCategory, $recent, $budgets
 * Bento Dashboard Style.
 */
$diff = $totals['income'] - $totals['expense'];
$chart = [
    'labels' => array_map(fn ($r) => $r['name'], $byCategory),
    'values' => array_map(fn ($r) => (int)$r['total'], $byCategory),
];
?>
<div class="d-flex align-items-center justify-content-between mb-3">
    <div class="d-inline-flex align-items-center gap-2 text-muted small fw-medium bg-white px-3 py-1 rounded-pill border">
        <i class="ph ph-calendar-blank" aria-hidden="true"></i>
        <span>Tháng <?= date('m/Y') ?></span>
    </div>
</div>

<!-- 4 Bento KPI Metric Tiles -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="stat-label mb-0">Tổng số dư</span>
                    <span class="stat-icon-badge"><i class="ph ph-wallet" aria-hidden="true"></i></span>
                </div>
                <div class="money fs-3 fw-bold <?= $balance < 0 ? 'money-expense' : '' ?>">
                    <?= $balance < 0 ? '−' : '' ?><?= money(abs($balance)) ?>
                </div>
                <div class="text-muted small mt-2">Toàn bộ ví hiện tại</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="stat-label mb-0">Thu tháng này</span>
                    <span class="stat-icon-badge text-success bg-success-subtle"><i class="ph ph-arrow-up-right" aria-hidden="true"></i></span>
                </div>
                <div class="money money-income fs-3 fw-bold">+<?= money($totals['income']) ?></div>
                <div class="text-muted small mt-2">Thu nhập ghi nhận</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="stat-label mb-0">Chi tháng này</span>
                    <span class="stat-icon-badge text-danger bg-danger-subtle"><i class="ph ph-arrow-down-right" aria-hidden="true"></i></span>
                </div>
                <div class="money money-expense fs-3 fw-bold">−<?= money($totals['expense']) ?></div>
                <div class="text-muted small mt-2">Tổng các khoản chi</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="stat-label mb-0">Chênh lệch</span>
                    <span class="stat-icon-badge"><i class="ph ph-scales" aria-hidden="true"></i></span>
                </div>
                <div class="money fs-3 fw-bold <?= $diff < 0 ? 'money-expense' : 'money-income' ?>">
                    <?= $diff < 0 ? '−' : '+' ?><?= money(abs($diff)) ?>
                </div>
                <div class="text-muted small mt-2"><span>Thu trừ chi tháng</span> <?= date('m') ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Bento Row: Hệ sinh thái tiện ích Bento (Lucian #214) & Giao dịch gần đây -->
<div class="row g-3 mb-4">
    <div class="col-xl-5 col-lg-6">
        <?php require VIEW_PATH . '/partials/bento-features.php'; ?>
    </div>
    <div class="col-xl-7 col-lg-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                    <h2 class="h3 mb-0">Giao dịch gần nhất</h2>
                    <a class="small fw-semibold" href="<?= url('giao-dich') ?>"><span>Xem tất cả</span> <i class="ph ph-arrow-right" aria-hidden="true"></i></a>
                </div>
                <?php if (!$recent): ?>
                    <div class="empty-state py-4">
                        <div class="empty-icon-wrap"><i class="ph ph-receipt" aria-hidden="true"></i></div>
                        <p>Chưa có giao dịch nào.</p>
                        <a class="btn btn-outline-primary btn-sm px-3" href="<?= url('giao-dich?them=1') ?>">Thêm giao dịch đầu tiên</a>
                    </div>
                <?php else: ?>
                    <ul class="list-unstyled mb-0">
                        <?php foreach ($recent as $idx => $r): $income = $r['type'] === 'income'; $isLast = ($idx === count($recent) - 1); ?>
                            <li class="d-flex align-items-center gap-3 py-2 <?= $isLast ? '' : 'border-bottom' ?>">
                                <span class="cat-icon"><i class="ph ph-<?= e($r['category_icon']) ?>" aria-hidden="true"></i></span>
                                <div class="flex-fill min-w-0">
                                    <div class="fw-semibold text-truncate"><?= e($r['category_name']) ?></div>
                                    <div class="text-muted small text-truncate"><?= e(vn_date($r['trans_date'])) ?><?= $r['note'] ? ' · ' . e($r['note']) : '' ?></div>
                                </div>
                                <div class="money fw-semibold text-nowrap <?= $income ? 'money-income' : 'money-expense' ?>"><?= $income ? '+' : '−' ?><?= money($r['amount']) ?></div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Bento Row: Biểu đồ danh mục & Ngân sách tháng này -->
<div class="row g-3 mb-4">
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-body d-flex flex-column">
                <h2 class="h3 mb-3">Chi theo danh mục</h2>
                <?php if ($byCategory): ?>
                    <div class="chart-box flex-fill"><canvas id="expenseChart" aria-label="Biểu đồ tròn chi theo danh mục" role="img"></canvas></div>
                    <script type="application/json" id="expenseChartData"><?= json_encode($chart, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
                <?php else: ?>
                    <div class="empty-state my-auto">
                        <div class="empty-icon-wrap"><i class="ph ph-chart-pie-slice" aria-hidden="true"></i></div>
                        <p class="mb-0">Tháng này chưa có khoản chi nào.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                    <h2 class="h3 mb-0">Ngân sách tháng này</h2>
                    <a class="small fw-semibold" href="<?= url('ngan-sach') ?>">
                        <span><?= $budgets ? 'Quản lý ngân sách' : 'Đặt ngân sách' ?></span>
                        <i class="ph ph-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>
                <?php if (!$budgets): ?>
                    <div class="empty-state py-4">
                        <div class="empty-icon-wrap"><i class="ph ph-target" aria-hidden="true"></i></div>
                        <p class="text-muted mb-3">Chưa đặt ngân sách nào cho tháng này.</p>
                        <a class="btn btn-outline-primary btn-sm px-3" href="<?= url('ngan-sach') ?>">Đặt ngân sách ngay</a>
                    </div>
                <?php else: ?>
                    <?php
                    // Ngân sách đáng chú ý lên đầu: đã vượt, rồi sắp hết, rồi theo phần trăm giảm dần
                    $order = ['exceeded' => 0, 'warning' => 1, 'normal' => 2];
                    usort($budgets, fn ($a, $b) => [$order[$a['status']], -$a['percent']] <=> [$order[$b['status']], -$b['percent']]);
                    ?>
                    <div class="row g-3 g-md-4">
                        <?php foreach ($budgets as $b): ?>
                            <div class="col-md-6"><?php require VIEW_PATH . '/partials/budget-bar.php'; ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
