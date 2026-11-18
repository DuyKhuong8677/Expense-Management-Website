<?php
namespace App\Models;

use App\Core\Database;

/**
 * Bảng users. Phụ trách: TV2.
 */
class User
{
    public static function findByEmail(string $email): ?array
    {
        return Database::fetch('SELECT * FROM users WHERE email = ?', [mb_strtolower(trim($email))]);
    }

    public static function emailExists(string $email): bool
    {
        return self::findByEmail($email) !== null;
    }

    /**
     * Tạo tài khoản mới kèm ví "Tiền mặt" số dư 0 (BR-11).
     * Hai lệnh INSERT nằm trong một transaction: lỗi thì không tạo gì cả.
     */
    public static function register(string $fullName, string $email, string $password): int
    {
        return Database::transaction(function () use ($fullName, $email, $password) {
            $userId = Database::insert(
                'INSERT INTO users (full_name, email, password_hash) VALUES (?, ?, ?)',
                [trim($fullName), mb_strtolower(trim($email)), password_hash($password, PASSWORD_BCRYPT)]
            );
            Database::insert(
                "INSERT INTO wallets (user_id, name, type, initial_balance) VALUES (?, 'Tiền mặt', 'cash', 0)",
                [$userId]
            );
            return $userId;
        });
    }
}
