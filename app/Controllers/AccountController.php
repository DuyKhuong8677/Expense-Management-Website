<?php
namespace App\Controllers;

use App\Core\Controller;

/**
 * Tài khoản cá nhân — phụ trách: TV2 (feature/tai-khoan)
 * Hiện là trang chờ. TV2 thay hàm index() bằng code thật và thêm route vào app/routes.php.
 */
class AccountController extends Controller
{
    public function index(): void
    {
        $this->placeholder('Tài khoản', 'TV2', 'feature/tai-khoan', [
            'FR-24: xem và sửa họ tên, ảnh đại diện',
            'FR-25: đổi mật khẩu (phải nhập mật khẩu cũ)',
        ]);
    }
}
