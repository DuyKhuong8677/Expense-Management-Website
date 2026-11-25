<?php
/**
 * Báo cáo — TV5. Biến: $year, $month (0 = cả năm), $monthly (12 tháng), $byCategory, $periodTotal,
 * $totalIncome, $totalExpense, $avgExpense
 * Bento Dashboard Style.
 */
$diff = $totalIncome - $totalExpense;
$periodLabel = $month ? 'tháng ' . sprintf('%02d/%d', $month, $year) : 'năm ' . $year;
$chart = [
    'labels'  => array_map(fn ($m) => 'T' . $m['month'], $monthly),
    'income'  => array_column($monthly, 'income'),
    'expense' => array_column($monthly, 'expense'),
    'catLabels' => array_map(fn ($r) => $r['name'], $byCategory),
    'catValues' => array_map(fn ($r) => (int)$r['total'], $byCategory),
];
$csvQuery = $month ? 'thang=' . sprintf('%04d-%02d', $year, $month) : '';
?>
<!-- Bộ chọn kỳ báo cáo -->
<form class="card mb-4" method="get" action="<?= url('bao-cao') ?>" aria-label="Chọn kỳ báo cáo">
    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-6 col-sm-auto">
                <label class="form-label" for="rYear">Năm</label>
                <select class="form-select" id="rYear" name="nam" style="min-width: 120px">
                    <?php for ($y = (int)date('Y'); $y >= (int)date('Y') - 4; $y--): ?>
                        <option value="<?= $y ?>"<?= $y === $year ? ' selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-6 col-sm-auto">
                <label class="form-label" for="rMonth">Biểu đồ tròn theo</label>
                <select class="form-select" id="rMonth" name="thang" style="min-width: 160px">
                    <option value="0">Cả năm</option>
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= $m ?>"<?= $m === $month ? ' selected' : '' ?>>Tháng <?= $m ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-12 col-sm-auto mt-2 mt-sm-0">
                <button class="btn btn-outline-primary w-100" type="submit"><i class="ph ph-magnifying-glass" aria-hidden="true"></i> <span>Xem báo cáo</span></button>
            </div>
        </div>
    </div>
</form>

<!-- 4 Bento KPI Metric Tiles -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="stat-label mb-0"><span>Tổng thu năm</span> <?= $year ?></span>
                    <span class="stat-icon-badge text-success bg-success-subtle"><i class="ph ph-arrow-up-right" aria-hidden="true"></i></span>
                </div>
                <div class="money money-income fs-3 fw-bold mt-1">+<?= money($totalIncome) ?></div>
                <div class="text-muted small mt-2">Toàn bộ thu nhập năm</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="stat-label mb-0"><span>Tổng chi năm</span> <?= $year ?></span>
                    <span class="stat-icon-badge text-danger bg-danger-subtle"><i class="ph ph-arrow-down-right" aria-hidden="true"></i></span>
                </div>
                <div class="money money-expense fs-3 fw-bold mt-1">−<?= money($totalExpense) ?></div>
                <div class="text-muted small mt-2">Toàn bộ chi tiêu năm</div>
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
                <div class="money fs-3 fw-bold mt-1 <?= $diff < 0 ? 'money-expense' : 'money-income' ?>">
                    <?= $diff < 0 ? '−' : '+' ?><?= money(abs($diff)) ?>
                </div>
                <div class="text-muted small mt-2">Dư địa tích lũy</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="stat-label mb-0">Chi TB mỗi tháng</span>
                        <span class="stat-icon-badge"><i class="ph ph-trend-up" aria-hidden="true"></i></span>
                    </div>
                    <div class="money fs-3 fw-bold mt-1"><?= money($avgExpense) ?></div>
                </div>
                <div class="text-muted small mt-2">Tính trên tháng có phát sinh</div>
            </div>
        </div>
    </div>
</div>

<!-- Bento Row: Biểu đồ cột & Biểu đồ danh mục -->
<div class="row g-3 mb-4">
    <div class="col-xl-7">
        <div class="card h-100">
            <div class="card-body d-flex flex-column">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                    <h2 class="h3 mb-0"><span>Thu và chi theo tháng</span> (<?= $year ?>)</h2>
                </div>
                <div class="chart-box chart-box-lg flex-fill">
                    <canvas id="monthlyChart" role="img" aria-label="Biểu đồ cột thu và chi 12 tháng năm <?= $year ?>"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-5">
        <div class="card h-100">
            <div class="card-body d-flex flex-column">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                    <h2 class="h3 mb-0"><span>Chi theo danh mục</span></h2>
                </div>
                <?php if ($byCategory): ?>
                    <div class="chart-box"><canvas id="categoryChart" role="img" aria-label="Biểu đồ tròn chi theo danh mục <?= e($periodLabel) ?>"></canvas></div>
                    <div class="table-responsive mt-3 flex-fill">
                        <table class="table table-sm align-middle mb-0">
                            <tbody>
                            <?php foreach ($byCategory as $r): ?>
                                <tr>
                                    <td class="fw-medium"><?= e($r['name']) ?></td>
                                    <td class="money"><?= money($r['total']) ?></td>
                                    <td class="money text-muted" style="width: 60px;"><?= $periodTotal ? round($r['total'] * 100 / $periodTotal) : 0 ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state my-auto py-5">
                        <div class="empty-icon-wrap mb-3"><i class="ph ph-chart-pie-slice" aria-hidden="true"></i></div>
                        <h3 class="h5 mb-1">Chưa có dữ liệu chi tiêu</h3>
                        <p class="text-muted small mb-0">Không có khoản chi nào được ghi nhận trong <?= e($periodLabel) ?>.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Bento Card: Bảng tổng hợp chi tiết từng tháng -->
<div class="card">
    <div class="card-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 pb-2 border-bottom">
            <h2 class="h3 mb-0">Tổng hợp chi tiết từng tháng</h2>
            <a class="btn btn-sm btn-outline-primary" href="<?= url('giao-dich/xuat-csv' . ($csvQuery ? '?' . $csvQuery : '')) ?>">
                <i class="ph ph-download-simple" aria-hidden="true"></i> Xuất CSV <?= $month ? 'tháng ' . $month : 'tháng này' ?>
            </a>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0 tx-table">
                <thead>
                    <tr>
                        <th scope="col">Tháng</th>
                        <th scope="col" class="money">Tổng thu</th>
                        <th scope="col" class="money">Tổng chi</th>
                        <th scope="col" class="money">Chênh lệch</th>
                        <th scope="col" class="text-end"><span class="visually-hidden">Hành động</span></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($monthly as $m): $d = $m['income'] - $m['expense']; $empty = !$m['income'] && !$m['expense']; ?>
                    <tr<?= $empty ? ' class="text-muted opacity-75"' : '' ?>>
                        <td class="fw-medium">Tháng <?= $m['month'] ?></td>
                        <td class="money<?= $empty ? '' : ' money-income' ?>"><?= $m['income'] ? '+' . money($m['income']) : '–' ?></td>
                        <td class="money<?= $empty ? '' : ' money-expense' ?>"><?= $m['expense'] ? '−' . money($m['expense']) : '–' ?></td>
                        <td class="money fw-semibold<?= $empty ? '' : ($d < 0 ? ' money-expense' : ' money-income') ?>"><?= $empty ? '–' : ($d < 0 ? '−' : '+') . money(abs($d)) ?></td>
                        <td class="text-end">
                            <?php if (!$empty): ?>
                                <a class="small fw-semibold d-inline-flex align-items-center gap-1" href="<?= url('giao-dich?thang=' . sprintf('%04d-%02d', $year, $m['month'])) ?>">
                                    <span>Xem giao dịch</span>
                                    <i class="ph ph-arrow-right" aria-hidden="true"></i>
                                </a>
                            <?php else: ?>
                                <span class="text-muted small">–</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script type="application/json" id="reportData"><?= json_encode($chart, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
