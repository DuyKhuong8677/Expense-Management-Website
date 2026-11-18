<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Transaction;

/**
 * Báo cáo — phụ trách: TV5 (feature/bao-cao)
 *
 *   GET /bao-cao?nam=2026            biểu đồ cột thu/chi 12 tháng (FR-22) + bảng tổng hợp
 *   GET /bao-cao?nam=2026&thang=11   chọn thêm tháng thì biểu đồ tròn chỉ tính tháng đó (FR-21)
 *
 * Còn lại cho TV5: chọn khoảng ngày tùy ý, so sánh với cùng kỳ năm trước, đo tốc độ bằng Lighthouse.
 */
class ReportController extends Controller
{
    public function index(): void
    {
        $user = $this->requireLogin();
        $userId = (int)$user['id'];

        $year = (int)($_GET['nam'] ?? date('Y'));
        if ($year < 2000 || $year > (int)date('Y') + 1) {
            $year = (int)date('Y');
        }
        $month = (int)($_GET['thang'] ?? 0);
        $month = ($month >= 1 && $month <= 12) ? $month : 0;

        $from = $month ? sprintf('%04d-%02d-01', $year, $month) : "$year-01-01";
        $to = $month ? date('Y-m-t', strtotime($from)) : "$year-12-31";

        $monthly = Transaction::monthlyTotals($userId, $year);
        $byCategory = Transaction::expenseByCategory($userId, $from, $to);
        $totalIncome = array_sum(array_column($monthly, 'income'));
        $totalExpense = array_sum(array_column($monthly, 'expense'));
        $activeMonths = count(array_filter($monthly, fn ($m) => $m['income'] || $m['expense']));

        $this->view('reports/index', [
            'pageTitle'    => 'Báo cáo',
            'year'         => $year,
            'month'        => $month,
            'monthly'      => $monthly,
            'byCategory'   => $byCategory,
            'periodTotal'  => array_sum(array_map(fn ($r) => (int)$r['total'], $byCategory)),
            'totalIncome'  => $totalIncome,
            'totalExpense' => $totalExpense,
            'avgExpense'   => $activeMonths ? intdiv($totalExpense, $activeMonths) : 0,
            'scripts'      => ['js/reports.js'],
            'chartJs'      => true,
        ]);
    }
}
