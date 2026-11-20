<?php
/**
 * Ngân sách — TV4. Biến: $month, $budgets, $categories (chỉ danh mục chi), $usedIds, $totalLimit, $totalSpent
 */
$prev = date('Y-m', strtotime($month . '-01 -1 month'));
$next = date('Y-m', strtotime($month . '-01 +1 month'));
$warnCount = count(array_filter($budgets, fn ($b) => $b['status'] !== 'normal'));
?>
<div class="d-flex flex-wrap align-items-center gap-2 mb-3">
    <a class="btn btn-outline-secondary" href="<?= url('ngan-sach?thang=' . $prev) ?>" aria-label="Tháng trước"><i class="ph ph-caret-left" aria-hidden="true"></i></a>
    <h2 class="h3 mb-0 px-2">Tháng <?= e(date('m/Y', strtotime($month . '-01'))) ?></h2>
    <a class="btn btn-outline-secondary" href="<?= url('ngan-sach?thang=' . $next) ?>" aria-label="Tháng sau"><i class="ph ph-caret-right" aria-hidden="true"></i></a>
    <?php if ($month !== date('Y-m')): ?>
        <a class="ms-2" href="<?= url('ngan-sach') ?>">Về tháng này</a>
    <?php endif; ?>
    <form class="ms-auto" method="post" action="<?= url('ngan-sach/sao-chep') ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="thang" value="<?= e($month) ?>">
        <button class="btn btn-outline-primary" type="submit"><i class="ph ph-copy" aria-hidden="true"></i> Sao chép từ tháng <?= e(date('m/Y', strtotime($prev . '-01'))) ?></button>
    </form>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <?php if ($budgets): ?>
            <div class="card mb-3">
                <div class="card-body d-flex flex-wrap gap-4">
                    <div>
                        <div class="text-muted">Tổng hạn mức</div>
                        <div class="money fs-4 fw-semibold"><?= money($totalLimit) ?></div>
                    </div>
                    <div>
                        <div class="text-muted">Đã chi trong các danh mục này</div>
                        <div class="money money-expense fs-4 fw-semibold">−<?= money($totalSpent) ?></div>
                    </div>
                    <?php if ($warnCount): ?>
                        <div class="alert alert-warning mb-0 align-self-center">
                            <i class="ph ph-warning" aria-hidden="true"></i> <?= $warnCount ?> ngân sách sắp hết hoặc đã vượt
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <?php if (!$budgets): ?>
                    <div class="empty-state">
                        <i class="ph ph-target" aria-hidden="true"></i>
                        <p>Tháng này chưa đặt ngân sách nào. Hãy đặt hạn mức cho những danh mục bạn hay chi nhiều, như Ăn uống hay Mua sắm.</p>
                    </div>
                <?php else: ?>
                    <div class="d-grid gap-4">
                        <?php foreach ($budgets as $b): ?>
                            <div class="d-flex gap-3 align-items-start">
                                <div class="flex-fill"><?php require VIEW_PATH . '/partials/budget-bar.php'; ?></div>
                                <form method="post" action="<?= url('ngan-sach/' . (int)$b['id'] . '/xoa') ?>" data-confirm="Xóa ngân sách <?= e($b['category_name']) ?> tháng này?">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="thang" value="<?= e($month) ?>">
                                    <button class="btn btn-sm btn-outline-secondary" type="submit" aria-label="Xóa ngân sách <?= e($b['category_name']) ?>"><i class="ph ph-trash" aria-hidden="true"></i></button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h2 class="h3">Đặt ngân sách</h2>
                <p class="text-muted">Chọn danh mục đã có ngân sách thì hạn mức cũ sẽ được cập nhật.</p>
                <form method="post" action="<?= url('ngan-sach') ?>" novalidate>
                    <?= csrf_field() ?>
                    <input type="hidden" name="thang" value="<?= e($month) ?>">
                    <div class="mb-3">
                        <label class="form-label" for="bCategory">Danh mục chi</label>
                        <select class="form-select<?= has_error('category_id') ?>" id="bCategory" name="category_id" required>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= (int)$c['id'] ?>"<?= old('category_id') === (string)$c['id'] ? ' selected' : '' ?>>
                                    <?= e($c['name']) ?><?= in_array((int)$c['id'], $usedIds, true) ? ' (đã có)' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?= error_for('category_id') ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="bLimit">Hạn mức trong tháng (₫)</label>
                        <input class="form-control js-money<?= has_error('limit_amount') ?>" id="bLimit" name="limit_amount" value="<?= old('limit_amount') ?>" inputmode="numeric" autocomplete="off" placeholder="VD: 1.500.000" required>
                        <?= error_for('limit_amount') ?>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Lưu ngân sách</button>
                </form>
            </div>
        </div>
    </div>
</div>
