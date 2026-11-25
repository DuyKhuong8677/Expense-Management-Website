<?php
/**
 * Ngân sách — TV4. Biến: $month, $budgets, $categories (chỉ danh mục chi), $usedIds, $totalLimit, $totalSpent
 * Bento Dashboard Style.
 */
$prev = date('Y-m', strtotime($month . '-01 -1 month'));
$next = date('Y-m', strtotime($month . '-01 +1 month'));
$warnCount = count(array_filter($budgets, fn ($b) => $b['status'] !== 'normal'));
?>
<div class="d-flex flex-wrap align-items-center gap-2 mb-3">
    <div class="btn-group" role="group" aria-label="Chuyển tháng">
        <a class="btn btn-outline-secondary btn-sm" href="<?= url('ngan-sach?thang=' . $prev) ?>" aria-label="Tháng trước" title="Tháng trước"><i class="ph ph-caret-left" aria-hidden="true"></i></a>
        <a class="btn btn-outline-secondary btn-sm" href="<?= url('ngan-sach?thang=' . $next) ?>" aria-label="Tháng sau" title="Tháng sau"><i class="ph ph-caret-right" aria-hidden="true"></i></a>
    </div>
    <h2 class="h3 mb-0 px-2">Tháng <?= e(date('m/Y', strtotime($month . '-01'))) ?></h2>
    <?php if ($month !== date('Y-m')): ?>
        <a class="btn btn-sm btn-outline-primary ms-auto ms-sm-2" href="<?= url('ngan-sach') ?>">Về tháng này</a>
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
            <!-- Bento Stat Tile for Budgets -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row g-3 align-items-center">
                        <div class="col-sm-4">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="stat-label mb-0">Tổng hạn mức</span>
                            </div>
                            <div class="money fs-4 fw-semibold"><?= money($totalLimit) ?></div>
                        </div>
                        <div class="col-sm-5 border-sm-start ps-sm-3">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="stat-label mb-0">Đã chi (danh mục này)</span>
                            </div>
                            <div class="money money-expense fs-4 fw-semibold">−<?= money($totalSpent) ?></div>
                        </div>
                        <?php if ($warnCount): ?>
                            <div class="col-sm-3">
                                <div class="alert alert-warning py-2 px-3 mb-0 small d-flex align-items-center gap-2 border">
                                    <i class="ph ph-warning flex-shrink-0" aria-hidden="true"></i>
                                    <span><strong><?= $warnCount ?></strong> <span>cần chú ý</span></span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                    <h2 class="h3 mb-0">Danh sách ngân sách</h2>
                    <?php if ($budgets): ?>
                        <span class="badge bg-light text-secondary border px-2 py-1"><?= count($budgets) ?> <span>danh mục</span></span>
                    <?php endif; ?>
                </div>

                <?php if (!$budgets): ?>
                    <div class="empty-state py-5">
                        <div class="empty-icon-wrap mb-3"><i class="ph ph-target" aria-hidden="true"></i></div>
                        <h3 class="h5 mb-1">Chưa thiết lập ngân sách</h3>
                        <p class="text-muted small mb-0">Tháng này chưa đặt ngân sách nào. Hãy đặt hạn mức cho những danh mục bạn hay chi nhiều như Ăn uống hay Mua sắm.</p>
                    </div>
                <?php else: ?>
                    <div class="d-grid gap-3">
                        <?php foreach ($budgets as $idx => $b): $isLast = ($idx === count($budgets) - 1); ?>
                            <div class="d-flex gap-3 align-items-start <?= $isLast ? '' : 'pb-3 border-bottom' ?>">
                                <div class="flex-fill min-w-0"><?php require VIEW_PATH . '/partials/budget-bar.php'; ?></div>
                                <form method="post" action="<?= url('ngan-sach/' . (int)$b['id'] . '/xoa') ?>" data-confirm="Xóa ngân sách <?= e($b['category_name']) ?> tháng này?">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="thang" value="<?= e($month) ?>">
                                    <button class="btn btn-sm btn-outline-secondary btn-icon-sm mt-1" type="submit" aria-label="Xóa ngân sách <?= e($b['category_name']) ?>" title="Xóa">
                                        <i class="ph ph-trash" aria-hidden="true"></i>
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card sticky-lg-top" style="top: calc(var(--header-h) + 16px);">
            <div class="card-body">
                <h2 class="h3 mb-1">Đặt ngân sách</h2>
                <p class="text-muted small mb-3">Chọn danh mục đã có ngân sách thì hạn mức cũ sẽ được cập nhật.</p>
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
                    <button class="btn btn-primary w-100 py-2" type="submit">Lưu ngân sách</button>
                </form>
            </div>
        </div>
    </div>
</div>
