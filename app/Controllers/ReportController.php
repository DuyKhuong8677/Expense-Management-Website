<?php
namespace App\Controllers;

use App\Core\Controller;

/**
 * Thống kê – báo cáo — phụ trách: TV5 (feature/bao-cao)
 * Hiện là trang chờ. TV5 thay hàm index() bằng code thật và thêm route vào app/routes.php.
 */
class ReportController extends Controller
{
    public function index(): void
    {
        $this->placeholder('Báo cáo', 'TV5', 'feature/bao-cao', [
            'FR-21: biểu đồ tròn tỷ lệ chi theo danh mục trong kỳ được chọn',
            'FR-22: biểu đồ cột thu và chi theo 12 tháng',
            'FR-23: xuất danh sách giao dịch đã lọc ra CSV',
            'Chỉ cộng giao dịch có type = \'expense\' khi tính tổng chi (và ngược lại cho thu)',
        ]);
    }
}
