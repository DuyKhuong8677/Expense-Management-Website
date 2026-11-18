<?php
namespace App\Core;

/**
 * Lớp cha cho mọi controller.
 */
class Controller
{
    /** Hiển thị view trong layout. $view = 'transactions/index' -> app/Views/transactions/index.php */
    protected function view(string $view, array $data = [], string $layout = 'app'): void
    {
        $data['pageTitle'] = $data['pageTitle'] ?? config('app.name');
        extract($data, EXTR_SKIP);

        ob_start();
        require VIEW_PATH . '/' . $view . '.php';
        $content = ob_get_clean();

        require VIEW_PATH . '/layouts/' . $layout . '.php';
    }

    /** Trả JSON cho các yêu cầu fetch từ JavaScript */
    protected function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /** Bắt buộc đăng nhập. Gọi ở đầu mọi hàm cần đăng nhập. */
    protected function requireLogin(): array
    {
        $user = Auth::user();
        if ($user === null) {
            if (self::wantsJson()) {
                $this->json(['success' => false, 'message' => 'Vui lòng đăng nhập.'], 401);
            }
            flash('warning', 'Vui lòng đăng nhập để tiếp tục.');
            redirect('dang-nhap');
        }
        return $user;
    }

    /** Bắt buộc là admin (NFR-11: kiểm tra ở phía server) */
    protected function requireAdmin(): array
    {
        $user = $this->requireLogin();
        if (!Auth::isAdmin()) {
            $this->forbidden();
        }
        return $user;
    }

    public function notFound(): void
    {
        http_response_code(404);
        if (self::wantsJson()) {
            $this->json(['success' => false, 'message' => 'Không tìm thấy.'], 404);
        }
        $this->view('errors/404', ['pageTitle' => 'Không tìm thấy trang'], 'public');
        exit;
    }

    public function forbidden(): void
    {
        http_response_code(403);
        if (self::wantsJson()) {
            $this->json(['success' => false, 'message' => 'Bạn không có quyền truy cập.'], 403);
        }
        $this->view('errors/403', ['pageTitle' => 'Không có quyền truy cập'], 'public');
        exit;
    }

    public static function wantsJson(): bool
    {
        return str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')
            || ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';
    }

    /** Hiển thị trang chờ cho chức năng chưa làm, ghi rõ ai phụ trách */
    protected function placeholder(string $title, string $owner, string $branch, array $tasks): void
    {
        $this->requireLogin();
        $this->view('placeholder', compact('title', 'owner', 'branch', 'tasks') + ['pageTitle' => $title]);
    }
}
