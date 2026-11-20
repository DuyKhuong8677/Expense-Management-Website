<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Validator;
use App\Models\Wallet;

/**
 * Ví tiền — phụ trách: TV4 (feature/vi-ngan-sach)
 *
 *   GET  /vi             danh sách ví + số dư
 *   POST /vi             thêm ví
 *   POST /vi/{id}/sua    sửa tên, loại, số dư ban đầu
 *
 * Còn lại cho TV4: ẩn ví (FR-09), chuyển tiền giữa hai ví (FR-11, bảng transfers).
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
