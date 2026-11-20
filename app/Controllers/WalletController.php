<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Validator;
use App\Models\Transfer;
use App\Models\Wallet;

/**
 * Ví tiền — phụ trách: TV4 (feature/vi-ngan-sach)
 *
 *   GET  /vi                          danh sách ví + số dư + các lần chuyển tiền gần đây
 *   POST /vi                          thêm ví
 *   POST /vi/{id}/sua                 sửa tên, loại, số dư ban đầu
 *   POST /vi/{id}/an                  ẩn ví (FR-09); giao dịch cũ vẫn giữ nguyên
 *   POST /vi/{id}/hien                hiện lại ví đã ẩn
 *   POST /vi/{id}/xoa                 xóa hẳn ví đã ẩn nếu chưa có giao dịch hay lần chuyển tiền nào (BR-08)
 *   POST /vi/chuyen-tien              chuyển tiền giữa hai ví (FR-11)
 *   POST /vi/chuyen-tien/{id}/xoa     xóa một lần chuyển tiền (nhập nhầm)
 */
class WalletController extends Controller
{
    /** Số dư ban đầu tối đa (VNĐ), giống giới hạn của số tiền giao dịch (BR-01) */
    private const MAX_INITIAL = 10_000_000_000;

    public function index(): void
    {
        $user = $this->requireLogin();
        $wallets = Wallet::withBalance((int)$user['id']);

        $this->view('wallets/index', [
            'pageTitle' => 'Ví tiền',
            'wallets'   => $wallets,
            'total'     => array_sum(array_map(fn ($w) => (int)$w['balance'], $wallets)),
            'transfers' => Transfer::recent((int)$user['id'], 10),
            'hiddenWallets' => Wallet::hidden((int)$user['id']),
            'scripts'   => ['js/wallets.js'],
        ]);
    }

    public function store(): void
    {
        $user = $this->requireLogin();
        [$name, $type, $amount] = $this->validated(0, 'add');

        Wallet::create((int)$user['id'], $name, $type, $amount);
        flash('success', 'Đã thêm ví "' . $name . '".');
        redirect('vi');
    }

    public function update(int $id): void
    {
        $user = $this->requireLogin();
        if (!Wallet::findOwn($id, (int)$user['id'])) {
            flash('danger', 'Không tìm thấy ví để sửa.');
            redirect('vi');
        }
        [$name, $type, $amount] = $this->validated($id, 'edit');

        Wallet::update($id, (int)$user['id'], $name, $type, $amount);
        flash('success', 'Đã cập nhật ví "' . $name . '".');
        redirect('vi');
    }

    public function hide(int $id): void
    {
        $user = $this->requireLogin();
        $wallet = Wallet::findOwn($id, (int)$user['id']);
        if (!$wallet) {
            flash('danger', 'Không tìm thấy ví để ẩn.');
            redirect('vi');
        }

        Wallet::hide($id, (int)$user['id']);
        flash('success', 'Đã ẩn ví "' . $wallet['name'] . '". Giao dịch cũ của ví vẫn được giữ nguyên.');
        redirect('vi');
    }

    public function unhide(int $id): void
    {
        $user = $this->requireLogin();
        flash(...(Wallet::unhide($id, (int)$user['id'])
            ? ['success', 'Đã hiện lại ví. Ví quay về danh sách cùng các giao dịch cũ.']
            : ['danger', 'Không tìm thấy ví đã ẩn để hiện lại.']));
        redirect('vi');
    }

    public function destroy(int $id): void
    {
        $user = $this->requireLogin();
        $result = Wallet::delete($id, (int)$user['id']);
        flash(...match ($result) {
            'deleted' => ['success', 'Đã xóa ví.'],
            'in_use'  => ['danger', 'Ví đã có giao dịch hoặc lần chuyển tiền nên không xóa được, chỉ có thể để ẩn.'],
            default   => ['danger', 'Không tìm thấy ví đã ẩn để xóa. Hãy ẩn ví trước rồi mới xóa.'],
        });
        redirect('vi');
    }

    public function transfer(): void
    {
        $user = $this->requireLogin();
        $userId = (int)$user['id'];

        $v = Validator::make($_POST, [
            'from_wallet_id' => 'required|int',
            'to_wallet_id'   => 'required|int',
            'amount'         => 'required|money',
            'trans_date'     => 'required|date|not_future',
            'note'           => 'max:255',
        ], [
            'from_wallet_id' => 'Ví nguồn',
            'to_wallet_id'   => 'Ví đích',
            'amount'         => 'Số tiền',
            'trans_date'     => 'Ngày chuyển',
            'note'           => 'Ghi chú',
        ]);
        $errors = $v->errors();

        // Hai ví phải là ví đang dùng của chính người này và phải khác nhau
        $from = null;
        $to = null;
        if (!isset($errors['from_wallet_id'])) {
            $from = Wallet::findForUser((int)$_POST['from_wallet_id'], $userId);
            if (!$from) {
                $errors['from_wallet_id'] = 'Ví nguồn không hợp lệ.';
            }
        }
        if (!isset($errors['to_wallet_id'])) {
            $to = Wallet::findForUser((int)$_POST['to_wallet_id'], $userId);
            if (!$to) {
                $errors['to_wallet_id'] = 'Ví đích không hợp lệ.';
            }
        }
        if ($from && $to && (int)$from['id'] === (int)$to['id']) {
            $errors['to_wallet_id'] = 'Ví đích phải khác ví nguồn.';
        }

        if ($errors) {
            with_old($_POST + ['_form' => 'transfer']);
            with_errors($errors);
            redirect('vi');
        }

        $amount = Validator::toMoney($_POST['amount']);
        $note = trim((string)($_POST['note'] ?? ''));
        Transfer::create($userId, (int)$from['id'], (int)$to['id'], $amount, $_POST['trans_date'], $note === '' ? null : $note);

        flash('success', 'Đã chuyển ' . money($amount) . ' từ "' . $from['name'] . '" sang "' . $to['name'] . '".');
        redirect('vi');
    }

    public function destroyTransfer(int $id): void
    {
        $user = $this->requireLogin();
        flash(...(Transfer::delete($id, (int)$user['id'])
            ? ['success', 'Đã xóa lần chuyển tiền. Số dư hai ví đã trở về như trước khi chuyển.']
            : ['danger', 'Không tìm thấy lần chuyển tiền để xóa.']));
        redirect('vi');
    }

    /**
     * Kiểm tra form thêm/sửa ví. Sai thì quay lại trang kèm lỗi.
     * $form = 'add' | 'edit' để biết hiện lỗi ở form nào. Trả về [tên, loại, số dư ban đầu].
     */
    private function validated(int $id, string $form): array
    {
        $v = Validator::make($_POST, [
            'name' => 'required|max:100',
            'type' => 'required|in:' . implode(',', array_keys(Wallet::TYPES)),
        ], ['name' => 'Tên ví', 'type' => 'Loại ví']);
        $errors = $v->errors();

        // Số dư ban đầu: bỏ trống = 0, cho phép âm (thẻ tín dụng), cho phép gõ "1.250.000"
        $initial = trim((string)($_POST['initial_balance'] ?? ''));
        $amount = 0;
        if ($initial !== '') {
            $negative = str_starts_with($initial, '-');
            $digits = str_replace(['.', ',', ' '], '', $negative ? substr($initial, 1) : $initial);
            if ($digits === '' || !ctype_digit($digits)) {
                $errors['initial_balance'] = 'Số dư ban đầu phải là số nguyên.';
            } elseif (strlen(ltrim($digits, '0')) > 11 || (int)$digits > self::MAX_INITIAL) {
                $errors['initial_balance'] = 'Số dư ban đầu tối đa 10.000.000.000 ₫.';
            } else {
                $amount = ($negative ? -1 : 1) * (int)$digits;
            }
        }

        if ($errors) {
            with_old($_POST + ['_form' => $form, '_id' => $id]);
            with_errors($errors);
            redirect('vi');
        }
        return [trim((string)$_POST['name']), (string)$_POST['type'], $amount];
    }
}
