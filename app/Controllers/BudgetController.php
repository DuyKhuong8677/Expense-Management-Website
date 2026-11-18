<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Validator;
use App\Models\Budget;
use App\Models\Category;

/**
 * Ngân sách — phụ trách: TV4 (feature/vi-ngan-sach)
 *
 *   GET  /ngan-sach?thang=2026-11     danh sách ngân sách của tháng + thanh tiến độ (FR-19)
 *   POST /ngan-sach                   đặt hạn mức cho một danh mục (FR-18); đã có thì cập nhật
 *   POST /ngan-sach/{id}/xoa          xóa ngân sách
 *
 * Còn lại cho TV4: FR-20 sao chép ngân sách tháng trước sang tháng mới.
 */
class BudgetController extends Controller
{
    public function index(): void
    {
        $user = $this->requireLogin();
        $month = self::monthParam($_GET['thang'] ?? '');
        [$year, $m] = array_map('intval', explode('-', $month));

        $budgets = Budget::forMonth((int)$user['id'], $year, $m);
        $usedIds = array_map(fn ($b) => (int)$b['category_id'], $budgets);
        $expenseCategories = array_values(array_filter(
            Category::forUser((int)$user['id']),
            fn ($c) => $c['type'] === 'expense'
        ));

        $this->view('budgets/index', [
            'pageTitle'  => 'Ngân sách',
            'month'      => $month,
            'budgets'    => $budgets,
            'categories' => $expenseCategories,
            'usedIds'    => $usedIds,
            'totalLimit' => array_sum(array_column($budgets, 'limit_amount')),
            'totalSpent' => array_sum(array_column($budgets, 'spent')),
        ]);
    }

    public function store(): void
    {
        $user = $this->requireLogin();
        $month = self::monthParam($_POST['thang'] ?? '');

        $v = Validator::make($_POST, [
            'category_id'  => 'required|int',
            'limit_amount' => 'required|money',
        ], ['category_id' => 'Danh mục', 'limit_amount' => 'Hạn mức']);
        $errors = $v->errors();

        if (!isset($errors['category_id'])) {
            $category = Category::findForUser((int)$_POST['category_id'], (int)$user['id']);
            if (!$category || $category['type'] !== 'expense') {
                $errors['category_id'] = 'Chỉ đặt ngân sách cho danh mục chi.';
            }
        }
        if ($errors) {
            with_old($_POST);
            with_errors($errors);
            redirect('ngan-sach?thang=' . $month);
        }

        [$year, $m] = array_map('intval', explode('-', $month));
        $limit = Validator::toMoney($_POST['limit_amount']);
        $updated = Budget::save((int)$user['id'], (int)$_POST['category_id'], $year, $m, $limit);

        flash('success', ($updated ? 'Đã cập nhật hạn mức ' : 'Đã đặt ngân sách ') . $category['name'] . ': ' . money($limit) . '.');
        redirect('ngan-sach?thang=' . $month);
    }

    public function destroy(int $id): void
    {
        $user = $this->requireLogin();
        $month = self::monthParam($_POST['thang'] ?? '');
        flash(...(Budget::delete($id, (int)$user['id'])
            ? ['success', 'Đã xóa ngân sách.']
            : ['danger', 'Không tìm thấy ngân sách để xóa.']));
        redirect('ngan-sach?thang=' . $month);
    }

    /** Chuẩn hóa tham số tháng dạng YYYY-MM, sai thì dùng tháng hiện tại */
    private static function monthParam(string $value): string
    {
        return preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $value) ? $value : date('Y-m');
    }
}
