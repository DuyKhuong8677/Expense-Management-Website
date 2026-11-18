<?php
/**
 * Báo cáo — TV5. Biến: $year, $month (0 = cả năm), $monthly (12 tháng), $byCategory, $periodTotal,
 * $totalIncome, $totalExpense, $avgExpense
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
<form class="d-flex flex-wrap align-items-end gap-2 mb-3" method="get" action="<?= url('bao-cao') ?>" aria-label="Chọn kỳ báo cáo">
    <div>
        <label class="form-label" for="rYear">Năm</label>
        <select class="form-select" id="rYear" name="nam" style="min-width: 120px">
            <?php for ($y = (int)date('Y'); $y >= (int)date('Y') - 4; $y--): ?>
                <option value="<?= $y ?>"<?= $y === $year ? ' selected' : '' ?>><?= $y ?></option>
            <?php endfor; ?>
        </select>
    </div>
    <div>
        <label class="form-label" for="rMonth">Biểu đồ tròn theo</label>
        <select class="form-select" id="rMonth" name="thang" style="min-width: 160px">
            <option value="0">Cả năm</option>
            <?php for ($m = 1; $m <= 12; $m++): ?>
                <option value="<?= $m ?>"<?= $m === $month ? ' selected' : '' ?>>Tháng <?= $m ?></option>
            <?php endfor; ?>
        </select>
    </div>
    <button class="btn btn-outline-primary" type="submit">Xem</button>
</form>

<div class="row g-3 mb-3">
    <div class="col-sm-6 col-xl-3"><div class="card h-100"><div class="card-body">
        <div class="text-muted">Tổng thu năm <?= $year ?></div>
        <div class="money money-income fs-3 fw-bold">+<?= money($totalIncome) ?></div>
    </div></div></div>
    <div class="col-sm-6 col-xl-3"><div class="card h-100"><div class="card-body">
        <div class="text-muted">Tổng chi năm <?= $year ?></div>
        <div class="money money-expense fs-3 fw-bold">−<?= money($totalExpense) ?></div>
    </div></div></div>
    <div class="col-sm-6 col-xl-3"><div class="card h-100"><div class="card-body">
        <div class="text-muted">Chênh lệch</div>
        <div class="money fs-3 fw-bold <?= $diff < 0 ? 'money-expense' : 'money-income' ?>"><?= $diff < 0 ? '−' : '+' ?><?= money(abs($diff)) ?></div>
    </div></div></div>
    <div class="col-sm-6 col-xl-3"><div class="card h-100"><div class="card-body">
        <div class="text-muted">Chi trung bình mỗi tháng</div>
        <div class="money fs-3 fw-bold"><?= money($avgExpense) ?></div>
        <div class="text-muted small-14">Tính trên các tháng có giao dịch</div>
    </div></div></div>
</div>

<div class="row g-3 mb-3">
    <div class="col-xl-7">
        <div class="card h-100"><div class="card-body">
            <h2 class="h3">Thu và chi theo tháng, năm <?= $year ?></h2>
            <div class="chart-box chart-box-lg"><canvas id="monthlyChart" role="img" aria-label="Biểu đồ cột thu và chi 12 tháng năm <?= $year ?>"></canvas></div>
        </div></div>
    </div>
    <div class="col-xl-5">
        <div class="card h-100"><div class="card-body">
            <h2 class="h3">Chi theo danh mục, <?= e($periodLabel) ?></h2>
            <?php if ($byCategory): ?>
                <div class="chart-box"><canvas id="categoryChart" role="img" aria-label="Biểu đồ tròn chi theo danh mục <?= e($periodLabel) ?>"></canvas></div>
                <table class="table table-sm mt-3 mb-0">
                    <tbody>
                    <?php foreach ($byCategory as $r): ?>
                        <tr>
                            <td><?= e($r['name']) ?></td>
                            <td class="money"><?= money($r['total']) ?></td>
                            <td class="money text-muted"><?= $periodTotal ? round($r['total'] * 100 / $periodTotal) : 0 ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state"><i class="ph ph-chart-pie-slice" aria-hidden="true"></i><p>Không có khoản chi nào trong <?= e($periodLabel) ?>.</p></div>
            <?php endif; ?>
        </div></div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
            <h2 class="h3 mb-0 me-auto">Tổng hợp từng tháng</h2>
            <a class="btn btn-outline-primary" href="<?= url('giao-dich/xuat-csv' . ($csvQuery ? '?' . $csvQuery : '')) ?>">
                <i class="ph ph-download-simple" aria-hidden="true"></i> Xuất CSV <?= $month ? 'tháng ' . $month : 'tháng này' ?>
            </a>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0 tx-table">
                <thead><tr><th scope="col">Tháng</th><th scope="col" class="money">Thu</th><th scope="col" class="money">Chi</th><th scope="col" class="money">Chênh lệch</th><th scope="col"><span class="visually-hidden">Xem</span></th></tr></thead>
                <tbody>
                <?php foreach ($monthly as $m): $d = $m['income'] - $m['expense']; $empty = !$m['income'] && !$m['expense']; ?>
                    <tr<?= $empty ? ' class="text-muted"' : '' ?>>
                        <td>Tháng <?= $m['month'] ?></td>
                        <td class="money<?= $empty ? '' : ' money-income' ?>"><?= $m['income'] ? '+' . money($m['income']) : '–' ?></td>
                        <td class="money<?= $empty ? '' : ' money-expense' ?>"><?= $m['expense'] ? '−' . money($m['expense']) : '–' ?></td>
                        <td class="money fw-semibold<?= $empty ? '' : ($d < 0 ? ' money-expense' : ' money-income') ?>"><?= $empty ? '–' : ($d < 0 ? '−' : '+') . money(abs($d)) ?></td>
                        <td class="text-end"><?php if (!$empty): ?><a href="<?= url('giao-dich?thang=' . sprintf('%04d-%02d', $year, $m['month'])) ?>">Xem giao dịch</a><?php endif; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script type="application/json" id="reportData"><?= json_encode($chart, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
