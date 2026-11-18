<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Validator;
use App\Models\Category;

/**
 * Danh mục — phụ trách: TV4 (feature/vi-ngan-sach)
 *
 *   GET  /danh-muc              danh mục mặc định + danh mục riêng, kèm số giao dịch (FR-12)
 *   POST /danh-muc              thêm danh mục riêng (FR-13)
 *   POST /danh-muc/{id}/sua     sửa tên, loại, icon (chỉ đổi loại khi chưa có giao dịch)
 *   POST /danh-muc/{id}/xoa     xóa; đã có giao dịch hoặc ngân sách thì chỉ ẩn (BR-08)
 *
 * Danh mục mặc định (user_id NULL) chỉ admin sửa được — phần của TV2 (FR-28).
 */
class CategoryController extends Controller
{
    public function index(): void
    {
        $user = $this->requireLogin();
        $all = Category::forUserWithUsage((int)$user['id']);

        $this->view('categories/index', [
            'pageTitle' => 'Danh mục',
            'groups'    => [
                'expense' => array_values(array_filter($all, fn ($c) => $c['type'] === 'expense')),
                'income'  => array_values(array_filter($all, fn ($c) => $c['type'] === 'income')),
            ],
            'icons'     => Category::ICONS,
            'scripts'   => ['js/categories.js'],
        ]);
    }

    public function store(): void
    {
        $user = $this->requireLogin();
        $this->validated((int)$user['id'], 0, 'add');
        Category::create((int)$user['id'], $_POST['name'], $_POST['type'], $_POST['icon']);
        flash('success', 'Đã thêm danh mục "' . trim($_POST['name']) . '".');
        redirect('danh-muc');
    }

    public function update(int $id): void
    {
        $user = $this->requireLogin();
        $category = Category::findOwn($id, (int)$user['id']);
        if (!$category) {
            flash('danger', 'Chỉ sửa được danh mục do bạn tạo.');
            redirect('danh-muc');
        }
        $this->validated((int)$user['id'], $id, 'edit');

        // Đổi loại thu/chi khi đã có giao dịch sẽ làm sai giao dịch cũ (BR-03), nên chặn lại
        if ($_POST['type'] !== $category['type'] && Category::usageCount($id, (int)$user['id']) > 0) {
            flash('danger', 'Danh mục đã có giao dịch hoặc ngân sách nên không đổi được loại thu/chi.');
            redirect('danh-muc');
        }

        Category::update($id, (int)$user['id'], $_POST['name'], $_POST['type'], $_POST['icon']);
        flash('success', 'Đã cập nhật danh mục "' . trim($_POST['name']) . '".');
        redirect('danh-muc');
    }

    public function destroy(int $id): void
    {
        $user = $this->requireLogin();
        $category = Category::findOwn($id, (int)$user['id']);
        if (!$category) {
            flash('danger', 'Chỉ xóa được danh mục do bạn tạo.');
            redirect('danh-muc');
        }
        $result = Category::remove($id, (int)$user['id']);
        flash('success', $result === 'hidden'
            ? 'Danh mục "' . $category['name'] . '" đã có giao dịch nên được ẩn đi; giao dịch cũ vẫn giữ nguyên.'
            : 'Đã xóa danh mục "' . $category['name'] . '".');
        redirect('danh-muc');
    }

    /** Kiểm tra form; sai thì quay lại trang kèm lỗi. $form = 'add' | 'edit' để biết hiện lỗi ở form nào */
    private function validated(int $userId, int $exceptId, string $form): void
    {
        $v = Validator::make($_POST, [
            'name' => 'required|max:50',
            'type' => 'required|in:income,expense',
            'icon' => 'required|in:' . implode(',', Category::ICONS),
        ], ['name' => 'Tên danh mục', 'type' => 'Loại', 'icon' => 'Biểu tượng']);
        $errors = $v->errors();

        if (!$errors && Category::nameTaken($userId, $_POST['name'], $_POST['type'], $exceptId)) {
            $errors['name'] = 'Đã có danh mục ' . ($_POST['type'] === 'income' ? 'thu' : 'chi') . ' tên này.';
        }
        if ($errors) {
            with_old($_POST + ['_form' => $form, '_id' => $exceptId]);
            with_errors($errors);
            redirect('danh-muc');
        }
    }
}
