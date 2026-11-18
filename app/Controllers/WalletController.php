<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Validator;
use App\Models\Wallet;

/**
 * Ví tiền — phụ trách: TV4 (feature/vi-ngan-sach)
 * Đã có: xem danh sách ví + số dư, thêm ví.
 * Còn lại cho TV4: sửa, ẩn ví (FR-09), chuyển tiền giữa hai ví (FR-11, bảng transfers).
 */
class WalletController extends Controller
{
    public function index(): void
    {
        $user = $this->requireLogin();
        $wallets = Wallet::withBalance((int)$user['id']);

        $this->view('wallets/index', [
            'pageTitle' => 'Ví tiền',
            'wallets'   => $wallets,
            'total'     => array_sum(array_map(fn ($w) => (int)$w['balance'], $wallets)),
        ]);
    }

    public function store(): void
    {
        $user = $this->requireLogin();
        $v = Validator::make($_POST, [
            'name' => 'required|max:100',
            'type' => 'required|in:' . implode(',', array_keys(Wallet::TYPES)),
        ], ['name' => 'Tên ví', 'type' => 'Loại ví']);

        $errors = $v->errors();
        $initial = trim($_POST['initial_balance'] ?? '');
        if ($initial !== '' && !preg_match('/^-?[\d.]+$/', $initial)) {
            $errors['initial_balance'] = 'Số dư ban đầu phải là số.';
        }
        if ($errors) {
            with_old($_POST);
            with_errors($errors);
            redirect('vi');
        }

        $amount = (str_starts_with($initial, '-') ? -1 : 1) * Validator::toMoney(ltrim($initial, '-'));
        Wallet::create((int)$user['id'], $_POST['name'], $_POST['type'], $amount);
        flash('success', 'Đã thêm ví "' . trim($_POST['name']) . '".');
        redirect('vi');
    }
}
