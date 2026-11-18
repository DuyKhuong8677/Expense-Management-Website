<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;

/**
 * Trang công khai — phụ trách: TV6 (feature/giao-dien)
 * TODO TV6: hoàn thiện nội dung trang hướng dẫn, form liên hệ/góp ý (FR-30), meta SEO.
 */
class HomeController extends Controller
{
    public function index(): void
    {
        if (Auth::check()) {
            redirect('tong-quan');
        }
        $this->view('home/index', [
            'pageTitle'       => 'Quản lý chi tiêu cá nhân miễn phí',
            'metaDescription' => 'Ghi chép thu chi hằng ngày, đặt ngân sách theo tháng và xem biểu đồ chi tiêu. Miễn phí, dùng tốt trên điện thoại.',
        ], 'public');
    }

    public function guide(): void
    {
        $this->view('home/guide', [
            'pageTitle'       => 'Hướng dẫn sử dụng',
            'metaDescription' => 'Hướng dẫn ghi giao dịch, tạo ví, đặt ngân sách và xem báo cáo trên website quản lý chi tiêu.',
        ], 'public');
    }

    public function contact(): void
    {
        $this->view('home/contact', [
            'pageTitle'       => 'Liên hệ và góp ý',
            'metaDescription' => 'Gửi góp ý hoặc liên hệ với nhóm phát triển website quản lý chi tiêu.',
        ], 'public');
    }

    public function sendContact(): void
    {
        // TODO TV6 (FR-30): validate rồi lưu vào bảng feedbacks
        flash('info', 'Chức năng góp ý đang được xây dựng.');
        redirect('lien-he');
    }
}
