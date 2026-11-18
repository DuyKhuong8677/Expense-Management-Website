<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Budget;
use App\Models\Transaction;
use App\Models\Wallet;

/**
 * Tổng quan — phụ trách: TV5 (feature/bao-cao)
 * Đã có: 4 thẻ số liệu tháng này, biểu đồ tròn chi theo danh mục, 5 giao dịch gần nhất.
 * Đã có thêm: thanh tiến độ ngân sách tháng này (dùng Budget của TV4).
 * Còn lại cho TV5: trang Báo cáo 12 tháng, xuất CSV.
 */
class DashboardController extends Controller
{
    public function index(): void
    {
        $user = $this->requireLogin();
        $userId = (int)$user['id'];
        $from = date('Y-m-01');
        $to = date('Y-m-t');

        $wallets = Wallet::withBalance($userId);

        $this->view('dashboard/index', [
            'pageTitle'  => 'Tổng quan',
            'totals'     => Transaction::totals($userId, ['from' => $from, 'to' => $to]),
            'balance'    => array_sum(array_map(fn ($w) => (int)$w['balance'], $wallets)),
            'byCategory' => Transaction::expenseByCategory($userId, $from, $to),
            'recent'     => Transaction::recent($userId, 5),
            'budgets'    => Budget::forMonth($userId, (int)date('Y'), (int)date('n')),
            'scripts'    => ['js/dashboard.js'],
            'chartJs'    => true,
        ]);
    }
}
