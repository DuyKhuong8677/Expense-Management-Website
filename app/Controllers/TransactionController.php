<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Validator;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\Wallet;

/**
 * Giao dịch — phụ trách: TV3 (feature/giao-dich)
 *
 *   GET  /giao-dich             danh sách + lọc (tham số GET trên URL)          -> HTML
 *   GET  /giao-dich/{id}        lấy 1 giao dịch để điền vào hộp thoại sửa       -> JSON
 *   POST /giao-dich             thêm (gửi bằng fetch từ hộp thoại)              -> JSON
 *   POST /giao-dich/{id}/sua    sửa  (gửi bằng fetch từ hộp thoại)              -> JSON
 *   POST /giao-dich/{id}/xoa    xóa  (form HTML thường, có xác nhận)            -> chuyển trang
 *
 * Khi lưu một khoản chi làm ngân sách chạm 80% hoặc vượt 100%, hiện thêm cảnh báo (UC-02, bước 5a).
 * Còn lại cho TV3: FR-07 sắp xếp theo số tiền, xuất CSV (cùng TV5).
 *   Lưu ý: giao dịch thuộc danh mục đã bị ẩn sẽ không chọn lại được danh mục đó trong hộp thoại sửa — cần xử lý.
 */
class TransactionController extends Controller
{
    public function index(): void
    {
        $user = $this->requireLogin();
        $userId = (int)$user['id'];

        // Tháng đang xem: ?thang=2026-11, mặc định tháng hiện tại
        $month = preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $_GET['thang'] ?? '') ? $_GET['thang'] : date('Y-m');
        $filters = [
            'from'        => $month . '-01',
            'to'          => date('Y-m-t', strtotime($month . '-01')),
            'type'        => in_array($_GET['loai'] ?? '', ['income', 'expense'], true) ? $_GET['loai'] : '',
            'category_id' => (int)($_GET['danh_muc'] ?? 0),
            'wallet_id'   => (int)($_GET['vi'] ?? 0),
            'q'           => mb_substr(trim($_GET['q'] ?? ''), 0, 100),
        ];

        $this->view('transactions/index', [
            'pageTitle'  => 'Giao dịch',
            'month'      => $month,
            'filters'    => $filters,
            'result'     => Transaction::paginate($userId, $filters, (int)($_GET['trang'] ?? 1)),
            'totals'     => Transaction::totals($userId, $filters),
            'categories' => Category::forUser($userId),
            'wallets'    => Wallet::forUser($userId),
            'scripts'    => ['js/transactions.js'],
        ]);
    }

    public function show(int $id): void
    {
        $user = $this->requireLogin();
        $row = Transaction::findForUser($id, (int)$user['id']);
        if (!$row) {
            $this->json(['success' => false, 'message' => 'Không tìm thấy giao dịch.'], 404);
        }
        $this->json(['success' => true, 'data' => $row]);
    }

    public function store(): void
    {
        $user = $this->requireLogin();
        $data = $this->validated((int)$user['id']);
        $id = Transaction::create((int)$user['id'], $data);

        flash('success', 'Đã thêm giao dịch ' . money($data['amount']) . '.');
        $this->budgetAlert((int)$user['id'], $data);
        $this->json(['success' => true, 'message' => 'Đã thêm giao dịch.', 'id' => $id], 201);
    }

    public function update(int $id): void
    {
        $user = $this->requireLogin();
        if (!Transaction::findForUser($id, (int)$user['id'])) {
            $this->json(['success' => false, 'message' => 'Không tìm thấy giao dịch.'], 404);
        }
        $data = $this->validated((int)$user['id']);
        Transaction::update($id, (int)$user['id'], $data);

        flash('success', 'Đã cập nhật giao dịch.');
        $this->budgetAlert((int)$user['id'], $data);
        $this->json(['success' => true, 'message' => 'Đã cập nhật giao dịch.']);
    }

    public function destroy(int $id): void
    {
        $user = $this->requireLogin();
        if (Transaction::delete($id, (int)$user['id'])) {
            flash('success', 'Đã xóa giao dịch.');
        } else {
            flash('danger', 'Không tìm thấy giao dịch để xóa.');
        }
        $back = $_POST['back'] ?? '';
        redirect(str_starts_with($back, 'giao-dich') ? $back : 'giao-dich');
    }

    /** Sau khi lưu một khoản chi: nếu ngân sách của danh mục đó đạt 80% hoặc vượt thì thêm thông báo cảnh báo */
    private function budgetAlert(int $userId, array $data): void
    {
        if ($data['type'] !== 'expense') {
            return;
        }
        $b = Budget::forCategoryOn($userId, $data['category_id'], $data['trans_date']);
        if ($b && $b['status'] === 'exceeded') {
            flash('danger', 'Ngân sách ' . $b['category_name'] . ' đã vượt ' . money(-$b['remaining']) . ' (' . $b['percent'] . '% hạn mức).');
        } elseif ($b && $b['status'] === 'warning') {
            flash('warning', 'Ngân sách ' . $b['category_name'] . ' đã dùng ' . $b['percent'] . '%, còn ' . money($b['remaining']) . '.');
        }
    }

    /**
     * Kiểm tra dữ liệu gửi lên. Sai thì trả JSON 422 kèm lỗi từng ô, đúng thì trả về mảng đã làm sạch.
     */
    private function validated(int $userId): array
    {
        $v = Validator::make($_POST, [
            'type'        => 'required|in:income,expense',
            'amount'      => 'required|money',
            'category_id' => 'required|int',
            'wallet_id'   => 'required|int',
            'trans_date'  => 'required|date|not_future',
            'note'        => 'max:255',
        ], [
            'type'        => 'Loại giao dịch',
            'amount'      => 'Số tiền',
            'category_id' => 'Danh mục',
            'wallet_id'   => 'Ví',
            'trans_date'  => 'Ngày',
            'note'        => 'Ghi chú',
        ]);
        $errors = $v->errors();

        // Ví và danh mục phải thuộc về người dùng này; loại danh mục phải trùng loại giao dịch (BR-03)
        if (!isset($errors['wallet_id']) && !Wallet::findForUser((int)$_POST['wallet_id'], $userId)) {
            $errors['wallet_id'] = 'Ví không hợp lệ.';
        }
        if (!isset($errors['category_id'])) {
            $category = Category::findForUser((int)$_POST['category_id'], $userId);
            if (!$category) {
                $errors['category_id'] = 'Danh mục không hợp lệ.';
            } elseif (!isset($errors['type']) && $category['type'] !== $_POST['type']) {
                $errors['category_id'] = 'Danh mục này không thuộc loại ' . ($_POST['type'] === 'income' ? 'thu' : 'chi') . '.';
            }
        }

        if ($errors) {
            $this->json(['success' => false, 'message' => 'Vui lòng kiểm tra lại các ô được đánh dấu.', 'errors' => $errors], 422);
        }

        $note = trim($_POST['note'] ?? '');
        return [
            'type'        => $_POST['type'],
            'amount'      => Validator::toMoney($_POST['amount']),
            'category_id' => (int)$_POST['category_id'],
            'wallet_id'   => (int)$_POST['wallet_id'],
            'trans_date'  => $_POST['trans_date'],
            'note'        => $note === '' ? null : $note,
        ];
    }
}
