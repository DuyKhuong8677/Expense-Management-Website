<?php
namespace App\Controllers;

use App\Core\Controller;

/**
 * Quản trị — phụ trách: TV2 (feature/tai-khoan)
 * Hiện là trang chờ. TV2 thay hàm index() bằng code thật và thêm route vào app/routes.php.
 */
class AdminController extends Controller
{
    public function index(): void
    {
        $this->requireAdmin();
        $this->placeholder('Quản trị', 'TV2', 'feature/tai-khoan', [
            'FR-26/08: danh sách người dùng, tìm theo tên hoặc email (không xem giao dịch của họ)',
            'FR-27: khóa và mở khóa tài khoản',
            'FR-28: thêm, sửa, ẩn danh mục mặc định',
            'FR-29: thống kê số người dùng, số giao dịch theo tháng',
            'FR-31: xem góp ý, đánh dấu đã xử lý',
        ]);
    }
}
