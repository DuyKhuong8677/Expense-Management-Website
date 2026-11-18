<?php
namespace App\Core;

/**
 * Chống CSRF (NFR-09).
 * - Form HTML: thêm <?= csrf_field() ?> vào trong <form method="post">.
 * - Gọi fetch từ JS: dùng hàm api() trong assets/js/app.js (tự gửi header X-CSRF-Token).
 */
class Csrf
{
    public static function token(): string
    {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf'];
    }

    public static function verify(): void
    {
        $sent = $_POST['_csrf'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
        if (!is_string($sent) || !hash_equals(self::token(), $sent)) {
            http_response_code(403);
            if (Controller::wantsJson()) {
                header('Content-Type: application/json; charset=utf-8');
                exit(json_encode(['success' => false, 'message' => 'Phiên làm việc đã hết hạn, hãy tải lại trang.'], JSON_UNESCAPED_UNICODE));
            }
            exit('Phiên làm việc đã hết hạn, hãy quay lại và tải lại trang.');
        }
    }
}
