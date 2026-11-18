<?php
/**
 * Tổng quan — TV5. Biến: $totals, $balance, $byCategory, $recent, $budgets
 */
$diff = $totals['income'] - $totals['expense'];
$chart = [
    'labels' => array_map(fn ($r) => $r['name'], $byCategory),
    'values' => array_map(fn ($r) => (int)$r['total'], $byCategory),
];
?>
<p class="text-muted mb-3">Tháng <?= date('m/Y') ?></p>

<div class="row g-3 mb-3">
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100"><div class="card-body">
            <div class="text-muted">Tổng số dư</div>
            <div class="money fs-3 fw-bold <?= $balance < 0 ? 'money-expense' : '' ?>"><?= $balance < 0 ? '−' : '' ?><?= money(abs($balance)) ?></div>
        </div></div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100"><div class="card-body">
            <div class="text-muted">Thu tháng này</div>
            <div class="money money-income fs-3 fw-bold">+<?= money($totals['income']) ?></div>
        </div></div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100"><div class="card-body">
            <div class="text-muted">Chi tháng này</div>
            <div class="money money-expense fs-3 fw-bold">−<?= money($totals['expense']) ?></div>
        </div></div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100"><div class="card-body">
            <div class="text-muted">Chênh lệch</div>
            <div class="money fs-3 fw-bold <?= $diff < 0 ? 'money-expense' : 'money-income' ?>"><?= $diff < 0 ? '−' : '+' ?><?= money(abs($diff)) ?></div>
        </div></div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="h3">Chi theo danh mục</h2>
                <?php if ($byCategory): ?>
                    <div class="chart-box"><canvas id="expenseChart" aria-label="Biểu đồ tròn chi theo danh mục" role="img"></canvas></div>
                    <script type="application/json" id="expenseChartData"><?= json_encode($chart, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
                <?php else: ?>
                    <div class="empty-state"><i class="ph ph-chart-pie-slice" aria-hidden="true"></i><p>Tháng này chưa có khoản chi nào.</p></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <h2 class="h3 mb-0 me-auto">Giao dịch gần nhất</h2>
                    <a href="<?= url('giao-dich') ?>">Xem tất cả</a>
                </div>
                <?php if (!$recent): ?>
                    <div class="empty-state">
                        <i class="ph ph-receipt" aria-hidden="true"></i>
                        <p>Chưa có giao dịch nào.</p>
                        <a class="btn btn-outline-primary" href="<?= url('giao-dich?them=1') ?>">Thêm giao dịch đầu tiên</a>
                    </div>
                <?php else: ?>
                    <ul class="list-unstyled mb-0">
                        <?php foreach ($recent as $r): $income = $r['type'] === 'income'; ?>
                            <li class="d-flex align-items-center gap-3 py-2 border-bottom">
                                <span class="cat-icon"><i class="ph ph-<?= e($r['category_icon']) ?>" aria-hidden="true"></i></span>
                                <div class="flex-fill">
                                    <div class="fw-semibold"><?= e($r['category_name']) ?></div>
                                    <div class="text-muted"><?= e(vn_date($r['trans_date'])) ?><?= $r['note'] ? ' · ' . e($r['note']) : '' ?></div>
                                </div>
                                <div class="money fw-semibold <?= $income ? 'money-income' : 'money-expense' ?>"><?= $income ? '+' : '−' ?><?= money($r['amount']) ?></div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="card mt-3">
    <div class="card-body">
        <div class="d-flex align-items-center mb-3">
            <h2 class="h3 mb-0 me-auto">Ngân sách tháng này</h2>
            <a href="<?= url('ngan-sach') ?>"><?= $budgets ? 'Quản lý ngân sách' : 'Đặt ngân sách' ?></a>
        </div>
        <?php if (!$budgets): ?>
            <p class="text-muted mb-0">Chưa đặt ngân sách nào cho tháng này.</p>
        <?php else: ?>
            <?php
            // Ngân sách đáng chú ý lên đầu: đã vượt, rồi sắp hết, rồi theo phần trăm giảm dần
            $order = ['exceeded' => 0, 'warning' => 1, 'normal' => 2];
            usort($budgets, fn ($a, $b) => [$order[$a['status']], -$a['percent']] <=> [$order[$b['status']], -$b['percent']]);
            ?>
            <div class="row g-4">
                <?php foreach ($budgets as $b): ?>
                    <div class="col-md-6"><?php require VIEW_PATH . '/partials/budget-bar.php'; ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
