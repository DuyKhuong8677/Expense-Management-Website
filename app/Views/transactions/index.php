<?php
/**
 * Danh sách giao dịch — TV3.
 * Biến: $month, $filters, $result (rows, total, page, pages), $totals, $categories, $wallets
 * Bento Dashboard Style.
 */
$rows = $result['rows'];
$query = array_filter([
    'thang' => $month, 'loai' => $filters['type'], 'danh_muc' => $filters['category_id'] ?: '',
    'vi' => $filters['wallet_id'] ?: '', 'q' => $filters['q'],
], fn ($v) => $v !== '' && $v !== null);
$pageUrl = fn (int $p) => url('giao-dich?' . http_build_query($query + ['trang' => $p]));
$back = 'giao-dich?' . http_build_query($query + ['trang' => $result['page']]);
?>

<!-- Bộ lọc: gửi bằng phương thức GET, tham số hiện trên URL nên có thể lưu/chia sẻ link -->
<form class="card mb-3" method="get" action="<?= url('giao-dich') ?>" aria-label="Lọc giao dịch">
    <div class="card-body">
        <div class="row g-3 align-items-end">
            <div class="col-6 col-md-3 col-xl-2">
                <label class="form-label" for="fMonth">Tháng</label>
                <input class="form-control" type="month" id="fMonth" name="thang" value="<?= e($month) ?>">
            </div>
            <div class="col-6 col-md-3 col-xl-2">
                <label class="form-label" for="fType">Loại</label>
                <select class="form-select" id="fType" name="loai">
                    <option value="">Tất cả</option>
                    <option value="expense"<?= $filters['type'] === 'expense' ? ' selected' : '' ?>>Chi</option>
                    <option value="income"<?= $filters['type'] === 'income' ? ' selected' : '' ?>>Thu</option>
                </select>
            </div>
            <div class="col-6 col-md-3 col-xl-2">
                <label class="form-label" for="fCategory">Danh mục</label>
                <select class="form-select" id="fCategory" name="danh_muc">
                    <option value="">Tất cả</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= (int)$c['id'] ?>"<?= (int)$filters['category_id'] === (int)$c['id'] ? ' selected' : '' ?>>
                            <?= e($c['name']) ?> (<?= $c['type'] === 'income' ? 'thu' : 'chi' ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-3 col-xl-2">
                <label class="form-label" for="fWallet">Ví</label>
                <select class="form-select" id="fWallet" name="vi">
                    <option value="">Tất cả</option>
                    <?php foreach ($wallets as $w): ?>
                        <option value="<?= (int)$w['id'] ?>"<?= (int)$filters['wallet_id'] === (int)$w['id'] ? ' selected' : '' ?>><?= e($w['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-8 col-xl-2">
                <label class="form-label" for="fQ">Tìm ghi chú</label>
                <input class="form-control" type="search" id="fQ" name="q" value="<?= e($filters['q']) ?>" placeholder="VD: cơm trưa">
            </div>
            <div class="col-12 col-md-4 col-xl-2 d-flex gap-2">
                <button class="btn btn-outline-primary flex-fill" type="submit"><i class="ph ph-funnel" aria-hidden="true"></i> <span>Lọc</span></button>
                <a class="btn btn-outline-secondary" href="<?= url('giao-dich') ?>" aria-label="Bỏ lọc" title="Bỏ lọc"><i class="ph ph-arrow-counter-clockwise" aria-hidden="true"></i></a>
            </div>
        </div>
    </div>
</form>

<!-- Bento KPI Tiles -->
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="card h-100">
            <div class="card-body d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="stat-label mb-0">Tổng thu</span>
                    <span class="stat-icon-badge text-success bg-success-subtle"><i class="ph ph-arrow-up-right" aria-hidden="true"></i></span>
                </div>
                <div class="money money-income fs-4 fw-semibold mt-1">+<?= money($totals['income']) ?></div>
                <div class="text-muted small mt-2">Tổng thu theo bộ lọc</div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card h-100">
            <div class="card-body d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="stat-label mb-0">Tổng chi</span>
                    <span class="stat-icon-badge text-danger bg-danger-subtle"><i class="ph ph-arrow-down-right" aria-hidden="true"></i></span>
                </div>
                <div class="money money-expense fs-4 fw-semibold mt-1">−<?= money($totals['expense']) ?></div>
                <div class="text-muted small mt-2">Tổng chi theo bộ lọc</div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card h-100">
            <div class="card-body d-flex flex-column justify-content-between">
                <?php $diff = $totals['income'] - $totals['expense']; ?>
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="stat-label mb-0">Chênh lệch</span>
                    <span class="stat-icon-badge"><i class="ph ph-scales" aria-hidden="true"></i></span>
                </div>
                <div class="money fs-4 fw-semibold mt-1 <?= $diff < 0 ? 'money-expense' : 'money-income' ?>">
                    <?= $diff < 0 ? '−' : '+' ?><?= money(abs($diff)) ?>
                </div>
                <div class="text-muted small mt-2">Hiệu số thu và chi</div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 pb-2 border-bottom">
            <div class="d-flex align-items-center gap-2">
                <h2 class="h3 mb-0">Tháng <?= e(date('m/Y', strtotime($month . '-01'))) ?></h2>
                <span class="badge bg-light text-secondary border px-2 py-1"><span class="tx-count"><?= (int)$result['total'] ?></span> <span>giao dịch</span></span>
            </div>
            <?php if ($rows): ?>
                <a class="btn btn-sm btn-outline-primary" href="<?= e(url('giao-dich/xuat-csv?' . http_build_query($query))) ?>">
                    <i class="ph ph-download-simple" aria-hidden="true"></i> <span>Xuất CSV</span>
                </a>
            <?php endif; ?>
        </div>

        <?php if (!$rows): ?>
            <div class="empty-state py-5">
                <div class="empty-icon-wrap mb-3"><i class="ph ph-receipt" aria-hidden="true"></i></div>
                <h3 class="h5 mb-1">Chưa có giao dịch nào</h3>
                <p class="text-muted small mb-3">Không tìm thấy giao dịch nào phù hợp với bộ lọc hiện tại.</p>
                <button class="btn btn-primary js-open-add px-4" type="button"><i class="ph ph-plus" aria-hidden="true"></i> <span>Thêm giao dịch đầu tiên</span></button>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table align-middle mb-0 tx-table">
                    <thead>
                        <tr>
                            <th scope="col" style="min-width: 110px;">Ngày</th>
                            <th scope="col" style="min-width: 160px;">Danh mục</th>
                            <th scope="col" style="min-width: 180px;">Ghi chú</th>
                            <th scope="col" style="min-width: 130px;">Ví</th>
                            <th scope="col" class="money" style="min-width: 140px;">Số tiền</th>
                            <th scope="col" class="text-end" style="width: 90px;"><span class="visually-hidden">Thao tác</span></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($rows as $r): $income = $r['type'] === 'income'; ?>
                        <tr>
                            <td class="text-nowrap text-secondary"><?= e(vn_date($r['trans_date'])) ?></td>
                            <td class="text-nowrap">
                                <span class="cat-icon"><i class="ph ph-<?= e($r['category_icon']) ?>" aria-hidden="true"></i></span>
                                <span class="fw-medium"><?= e($r['category_name']) ?></span>
                            </td>
                            <td class="text-muted text-break"><?= e($r['note'] ?? '') ?></td>
                            <td class="text-nowrap text-secondary"><?= e($r['wallet_name']) ?></td>
                            <td class="money fw-semibold <?= $income ? 'money-income' : 'money-expense' ?>">
                                <?= $income ? '+' : '−' ?><?= money($r['amount']) ?>
                            </td>
                            <td class="text-end text-nowrap">
                                <div class="d-inline-flex gap-1">
                                    <button class="btn btn-sm btn-outline-secondary btn-icon-sm js-edit" type="button" data-id="<?= (int)$r['id'] ?>" aria-label="Sửa giao dịch" title="Sửa">
                                        <i class="ph ph-pencil-simple" aria-hidden="true"></i>
                                    </button>
                                    <form class="d-inline" method="post" action="<?= url('giao-dich/' . (int)$r['id'] . '/xoa') ?>" data-confirm="Xóa giao dịch <?= e(money($r['amount'])) ?> ngày <?= e(vn_date($r['trans_date'])) ?>?">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="back" value="<?= e($back) ?>">
                                        <button class="btn btn-sm btn-outline-secondary btn-icon-sm" type="submit" aria-label="Xóa giao dịch" title="Xóa">
                                            <i class="ph ph-trash" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($result['pages'] > 1): ?>
                <nav class="mt-4 pt-2 border-top d-flex justify-content-center" aria-label="Phân trang">
                    <ul class="pagination mb-0">
                        <?php for ($p = 1; $p <= $result['pages']; $p++): ?>
                            <li class="page-item<?= $p === $result['page'] ? ' active' : '' ?>">
                                <a class="page-link" href="<?= e($pageUrl($p)) ?>"><?= $p ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Hộp thoại thêm / sửa: gửi bằng fetch (POST), máy chủ trả về JSON -->
<div class="modal fade" id="txModal" tabindex="-1" aria-labelledby="txModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" id="txForm" novalidate>
            <div class="modal-header">
                <h2 class="modal-title h3 mb-0" id="txModalTitle">Thêm giao dịch</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="txId">
                <div class="btn-group w-100 mb-3" role="group" aria-label="Loại giao dịch">
                    <input type="radio" class="btn-check" name="type" id="txTypeExpense" value="expense" checked>
                    <label class="btn btn-outline-primary" for="txTypeExpense">Chi</label>
                    <input type="radio" class="btn-check" name="type" id="txTypeIncome" value="income">
                    <label class="btn btn-outline-primary" for="txTypeIncome">Thu</label>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="txAmount">Số tiền (₫)</label>
                    <input class="form-control form-control-lg js-money fw-bold" id="txAmount" name="amount" inputmode="numeric" autocomplete="off" placeholder="0" required>
                    <div class="invalid-feedback" data-error-for="amount"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="txCategory">Danh mục</label>
                    <select class="form-select" id="txCategory" name="category_id" required>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= (int)$c['id'] ?>" data-type="<?= e($c['type']) ?>"><?= e($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback" data-error-for="category_id"></div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label" for="txWallet">Ví</label>
                        <select class="form-select" id="txWallet" name="wallet_id" required>
                            <?php foreach ($wallets as $w): ?>
                                <option value="<?= (int)$w['id'] ?>"><?= e($w['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback" data-error-for="wallet_id"></div>
                    </div>
                    <div class="col-6">
                        <label class="form-label" for="txDate">Ngày</label>
                        <input class="form-control" type="date" id="txDate" name="trans_date" required>
                        <div class="invalid-feedback" data-error-for="trans_date"></div>
                    </div>
                </div>
                <div>
                    <label class="form-label" for="txNote">Ghi chú</label>
                    <input class="form-control" id="txNote" name="note" maxlength="255" placeholder="VD: cơm trưa với bạn">
                    <div class="invalid-feedback" data-error-for="note"></div>
                </div>
                <div class="alert alert-danger mt-3 mb-0 d-none" id="txFormError" role="alert"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-primary px-4" id="txSubmit">Lưu</button>
            </div>
        </form>
    </div>
</div>
