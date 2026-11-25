<?php
/**
 * Ví tiền — TV4. Biến: $wallets (kèm balance), $total, $transfers (các lần chuyển tiền gần đây)
 * Thêm ví và chuyển tiền: form bên phải. Sửa ví: hộp thoại (JS điền sẵn dữ liệu từ data-*, xem public/assets/js/wallets.js).
 * Ẩn ví, xóa lần chuyển tiền: form POST có xác nhận. Ví đã ẩn ($hiddenWallets, kèm used) hiện ở cuối cột trái, có nút Hiện lại; ví chưa dùng thì có thêm nút Xóa.
 */
use App\Models\Wallet;

$icons = ['cash' => 'money', 'bank' => 'bank', 'ewallet' => 'device-mobile', 'other' => 'wallet'];

// Form nào vừa nhập sai ('add', 'edit' hoặc 'transfer') thì chỉ form đó mới hiện lại dữ liệu cũ và lỗi
$oldForm = $_SESSION['old']['_form'] ?? '';
$addOld = fn ($k, $d = '') => $oldForm === 'add' ? old($k, $d) : e($d);
$addErr = fn ($k) => $oldForm === 'add' ? error_for($k) : '';
$addInvalid = fn ($k) => $oldForm === 'add' ? has_error($k) : '';
$editErr = fn ($k) => $oldForm === 'edit' ? error_for($k) : '';
$editInvalid = fn ($k) => $oldForm === 'edit' ? has_error($k) : '';
$trOld = fn ($k, $d = '') => $oldForm === 'transfer' ? old($k, $d) : e($d);
$trErr = fn ($k) => $oldForm === 'transfer' ? error_for($k) : '';
$trInvalid = fn ($k) => $oldForm === 'transfer' ? has_error($k) : '';
$defaultTo = (string)($wallets[1]['id'] ?? '');
?>
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-body">
                <div class="text-muted">Tổng số dư tất cả ví</div>
                <div class="money money-display <?= $total < 0 ? 'money-expense' : '' ?>"><?= $total < 0 ? '−' : '' ?><?= money(abs($total)) ?></div>
            </div>
        </div>

        <div class="row g-3">
            <?php foreach ($wallets as $w): $b = (int)$w['balance']; ?>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body d-flex gap-3 align-items-center">
                            <span class="feature-icon mb-0"><i class="ph ph-<?= e($icons[$w['type']] ?? 'wallet') ?>" aria-hidden="true"></i></span>
                            <div class="flex-fill">
                                <div class="fw-semibold"><?= e($w['name']) ?></div>
                                <div class="text-muted"><?= e(Wallet::TYPES[$w['type']] ?? '') ?></div>
                            </div>
                            <div class="money fs-5 fw-semibold <?= $b < 0 ? 'money-expense' : '' ?>"><?= $b < 0 ? '−' : '' ?><?= money(abs($b)) ?></div>
                        </div>
                        <div class="card-footer bg-transparent d-flex justify-content-between align-items-center">
                            <a href="<?= url('giao-dich?vi=' . (int)$w['id']) ?>">Xem giao dịch của ví này</a>
                            <div class="d-flex gap-1">
                                <button class="btn btn-sm btn-outline-secondary js-edit-wallet" type="button"
                                        data-id="<?= (int)$w['id'] ?>" data-name="<?= e($w['name']) ?>" data-type="<?= e($w['type']) ?>"
                                        data-initial="<?= (int)$w['initial_balance'] ?>" aria-label="Sửa ví <?= e($w['name']) ?>">
                                    <i class="ph ph-pencil-simple" aria-hidden="true"></i>
                                </button>
                                <form method="post" action="<?= url('vi/' . (int)$w['id'] . '/an') ?>"
                                      data-confirm="Ẩn ví <?= e($w['name']) ?>? Ví sẽ không hiện trong danh sách và tổng số dư nữa, giao dịch cũ vẫn được giữ nguyên.">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-sm btn-outline-secondary" type="submit" aria-label="Ẩn ví <?= e($w['name']) ?>">
                                        <i class="ph ph-eye-slash" aria-hidden="true"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($transfers): ?>
            <div class="card mt-3">
                <div class="card-body">
                    <h2 class="h3 mb-3">Chuyển tiền gần đây</h2>
                    <div class="d-grid gap-3">
                        <?php foreach ($transfers as $t): ?>
                            <div class="d-flex align-items-center gap-3">
                                <span class="feature-icon mb-0"><i class="ph ph-arrows-left-right" aria-hidden="true"></i></span>
                                <div class="flex-fill min-w-0">
                                    <div class="fw-semibold"><?= e($t['from_name']) ?> <i class="ph ph-arrow-right" aria-hidden="true"></i> <?= e($t['to_name']) ?></div>
                                    <div class="text-muted small-14"><?= e(vn_date($t['trans_date'])) ?><?= $t['note'] ? ' · ' . e($t['note']) : '' ?></div>
                                </div>
                                <div class="money fw-semibold"><?= money($t['amount']) ?></div>
                                <form method="post" action="<?= url('vi/chuyen-tien/' . (int)$t['id'] . '/xoa') ?>"
                                      data-confirm="Xóa lần chuyển tiền này? Số dư hai ví sẽ trở về như trước khi chuyển.">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-sm btn-outline-secondary" type="submit" aria-label="Xóa lần chuyển tiền này">
                                        <i class="ph ph-trash" aria-hidden="true"></i>
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($hiddenWallets)): ?>
            <div class="card mt-3">
                <div class="card-body">
                    <h2 class="h3 mb-1">Ví đã ẩn</h2>
                    <p class="text-muted">Ví đã ẩn không hiện trong danh sách và không tính vào tổng số dư. Hiện lại để dùng tiếp.</p>
                    <div class="d-grid gap-3">
                        <?php foreach ($hiddenWallets as $h): ?>
                            <div class="d-flex align-items-center gap-3">
                                <span class="feature-icon mb-0"><i class="ph ph-<?= e($icons[$h['type']] ?? 'wallet') ?>" aria-hidden="true"></i></span>
                                <div class="flex-fill min-w-0">
                                    <div class="fw-semibold text-truncate"><?= e($h['name']) ?></div>
                                    <div class="text-muted small-14"><?= e(Wallet::TYPES[$h['type']] ?? '') ?><?= (int)$h['used'] > 0 ? ' · đã có ' . (int)$h['used'] . ' giao dịch hoặc lần chuyển tiền, chỉ ẩn được' : '' ?></div>
                                </div>
                                <form method="post" action="<?= url('vi/' . (int)$h['id'] . '/hien') ?>">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-sm btn-outline-secondary" type="submit" aria-label="Hiện lại ví <?= e($h['name']) ?>">
                                        <i class="ph ph-eye" aria-hidden="true"></i> Hiện lại
                                    </button>
                                </form>
                                <?php if ((int)$h['used'] === 0): ?>
                                    <form method="post" action="<?= url('vi/' . (int)$h['id'] . '/xoa') ?>"
                                          data-confirm="Xóa hẳn ví <?= e($h['name']) ?>? Việc này không hoàn tác được.">
                                        <?= csrf_field() ?>
                                        <button class="btn btn-sm btn-outline-secondary" type="submit" aria-label="Xóa ví <?= e($h['name']) ?>">
                                            <i class="ph ph-trash" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="col-lg-4 d-grid gap-3 align-content-start">
        <div class="card">
            <div class="card-body">
                <h2 class="h3">Thêm ví</h2>
                <form method="post" action="<?= url('vi') ?>" novalidate>
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label" for="wName">Tên ví</label>
                        <input class="form-control<?= $addInvalid('name') ?>" id="wName" name="name" value="<?= $addOld('name') ?>" maxlength="100" placeholder="VD: Vietcombank" required>
                        <?= $addErr('name') ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="wType">Loại ví</label>
                        <select class="form-select<?= $addInvalid('type') ?>" id="wType" name="type">
                            <?php foreach (Wallet::TYPES as $value => $label): ?>
                                <option value="<?= e($value) ?>"<?= $addOld('type', 'bank') === $value ? ' selected' : '' ?>><?= e($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?= $addErr('type') ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="wInitial">Số dư ban đầu (₫)</label>
                        <input class="form-control<?= $addInvalid('initial_balance') ?>" id="wInitial" name="initial_balance" value="<?= $addOld('initial_balance') ?>" inputmode="numeric" placeholder="0">
                        <?= $addErr('initial_balance') ?>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Thêm ví</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h2 class="h3">Chuyển tiền giữa hai ví</h2>
                <?php if (count($wallets) < 2): ?>
                    <p class="text-muted mb-0">Bạn cần có ít nhất hai ví để chuyển tiền. Hãy thêm ví thứ hai ở trên.</p>
                <?php else: ?>
                    <p class="text-muted">Chuyển tiền chỉ đổi số dư hai ví, không tính là thu hay chi.</p>
                    <form method="post" action="<?= url('vi/chuyen-tien') ?>" novalidate>
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label" for="tFrom">Từ ví</label>
                            <select class="form-select<?= $trInvalid('from_wallet_id') ?>" id="tFrom" name="from_wallet_id">
                                <?php foreach ($wallets as $w): ?>
                                    <option value="<?= (int)$w['id'] ?>"<?= $trOld('from_wallet_id') === (string)$w['id'] ? ' selected' : '' ?>><?= e($w['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?= $trErr('from_wallet_id') ?>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="tTo">Sang ví</label>
                            <select class="form-select<?= $trInvalid('to_wallet_id') ?>" id="tTo" name="to_wallet_id">
                                <?php foreach ($wallets as $w): ?>
                                    <option value="<?= (int)$w['id'] ?>"<?= $trOld('to_wallet_id', $defaultTo) === (string)$w['id'] ? ' selected' : '' ?>><?= e($w['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?= $trErr('to_wallet_id') ?>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="tAmount">Số tiền (₫)</label>
                            <input class="form-control js-money<?= $trInvalid('amount') ?>" id="tAmount" name="amount" value="<?= $trOld('amount') ?>" inputmode="numeric" autocomplete="off" placeholder="VD: 500.000" required>
                            <?= $trErr('amount') ?>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="tDate">Ngày chuyển</label>
                            <input class="form-control<?= $trInvalid('trans_date') ?>" type="date" id="tDate" name="trans_date" value="<?= $trOld('trans_date', date('Y-m-d')) ?>" max="<?= e(date('Y-m-d')) ?>" required>
                            <?= $trErr('trans_date') ?>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="tNote">Ghi chú (không bắt buộc)</label>
                            <input class="form-control<?= $trInvalid('note') ?>" id="tNote" name="note" value="<?= $trOld('note') ?>" maxlength="255" placeholder="VD: Rút tiền mặt">
                            <?= $trErr('note') ?>
                        </div>
                        <button class="btn btn-outline-primary w-100" type="submit">Chuyển tiền</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Hộp thoại sửa ví -->
<div class="modal fade" id="walletModal" tabindex="-1" aria-labelledby="walletModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" method="post" id="walletForm" data-base="<?= e(url('vi')) ?>" novalidate>
            <?= csrf_field() ?>
            <div class="modal-header">
                <h2 class="modal-title h3" id="walletModalTitle">Sửa ví</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label" for="wEditName">Tên ví</label>
                    <input class="form-control<?= $editInvalid('name') ?>" id="wEditName" name="name" maxlength="100" required>
                    <?= $editErr('name') ?>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="wEditType">Loại ví</label>
                    <select class="form-select<?= $editInvalid('type') ?>" id="wEditType" name="type">
                        <?php foreach (Wallet::TYPES as $value => $label): ?>
                            <option value="<?= e($value) ?>"><?= e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?= $editErr('type') ?>
                </div>
                <div>
                    <label class="form-label" for="wEditInitial">Số dư ban đầu (₫)</label>
                    <input class="form-control<?= $editInvalid('initial_balance') ?>" id="wEditInitial" name="initial_balance" inputmode="numeric" placeholder="0">
                    <?= $editErr('initial_balance') ?>
                    <div class="form-text">Số dư hiện tại = số dư ban đầu + thu − chi ± chuyển tiền, nên đổi số này sẽ làm số dư hiện tại thay đổi theo.</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>
<?php if ($oldForm === 'edit'): ?>
    <!-- Sửa bị lỗi: mở lại hộp thoại với dữ liệu vừa nhập -->
    <script type="application/json" id="walletEditOld"><?= json_encode([
        'id' => (int)($_SESSION['old']['_id'] ?? 0), 'name' => $_SESSION['old']['name'] ?? '',
        'type' => $_SESSION['old']['type'] ?? 'cash', 'initial' => $_SESSION['old']['initial_balance'] ?? '',
    ], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
<?php endif; ?>
