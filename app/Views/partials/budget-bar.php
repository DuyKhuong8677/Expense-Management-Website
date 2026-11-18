<?php
/**
 * Một dòng ngân sách có thanh tiến độ. Dùng ở trang Ngân sách và trang Tổng quan.
 * Biến: $b (một phần tử của Budget::forMonth)
 */
$labels = ['normal' => 'Trong hạn mức', 'warning' => 'Sắp hết hạn mức', 'exceeded' => 'Đã vượt hạn mức'];
$width = min(100, $b['percent']);
?>
<div class="budget-row budget-<?= e($b['status']) ?>">
    <div class="d-flex align-items-center gap-2 mb-2">
        <span class="cat-icon"><i class="ph ph-<?= e($b['category_icon']) ?>" aria-hidden="true"></i></span>
        <span class="fw-semibold me-auto"><?= e($b['category_name']) ?></span>
        <span class="budget-badge"><?= e($labels[$b['status']]) ?></span>
    </div>
    <div class="progress" role="progressbar" aria-label="Đã dùng <?= (int)$b['percent'] ?>% ngân sách <?= e($b['category_name']) ?>"
         aria-valuenow="<?= (int)$b['percent'] ?>" aria-valuemin="0" aria-valuemax="100">
        <div class="progress-bar" style="width: <?= (int)$width ?>%"></div>
    </div>
    <div class="d-flex flex-wrap justify-content-between mt-2 text-muted">
        <span><span class="money"><?= money($b['spent']) ?></span> / <span class="money"><?= money($b['limit_amount']) ?></span> (<?= (int)$b['percent'] ?>%)</span>
        <?php if ($b['remaining'] >= 0): ?>
            <span>Còn lại <span class="money"><?= money($b['remaining']) ?></span></span>
        <?php else: ?>
            <span class="money-expense fw-semibold">Vượt <span class="money"><?= money(-$b['remaining']) ?></span></span>
        <?php endif; ?>
    </div>
</div>
