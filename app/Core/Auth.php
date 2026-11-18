<?php
namespace App\Core;

/**
 * Quản lý đăng nhập và phân quyền.
 *   Auth::id()      -> id người đang đăng nhập (dùng để lọc mọi truy vấn: WHERE user_id = ?)
 *   Auth::user()    -> mảng thông tin người dùng
 *   Auth::isAdmin() -> có phải admin không
 */
class Auth
{
    private static ?array $user = null;

    public static function login(array $user): void
    {
        session_regenerate_id(true); // chống session fixation
        $_SESSION['user_id'] = (int)$user['id'];
        self::$user = null;
    }

    public static function logout(): void
    {
        $_SESSION = [];
        session_regenerate_id(true);
        self::$user = null;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function id(): ?int
    {
        return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
    }

    public static function user(): ?array
    {
        if (self::$user === null && self::id() !== null) {
            self::$user = Database::fetch(
                'SELECT id, full_name, email, role, avatar, is_locked FROM users WHERE id = ?',
                [self::id()]
            );
            // Tài khoản bị xóa hoặc bị admin khóa trong lúc đang đăng nhập -> đăng xuất
            if (!self::$user || (int)self::$user['is_locked'] === 1) {
                self::logout();
                return null;
            }
        }
        return self::$user;
    }

    public static function isAdmin(): bool
    {
        return (self::user()['role'] ?? '') === 'admin';
    }
}
