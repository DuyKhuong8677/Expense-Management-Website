<?php
namespace App\Core;

use PDO;

/**
 * Kết nối CSDL dùng chung (PDO). Luôn dùng prepared statement:
 *   Database::query('SELECT * FROM wallets WHERE user_id = ?', [$userId])->fetchAll();
 */
class Database
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo === null) {
            $db = config('database');
            $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                $db['host'], $db['port'] ?? 3306, $db['name']);
            self::$pdo = new PDO($dsn, $db['user'], $db['pass'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
            self::$pdo->exec("SET time_zone = '+07:00'");
        }
        return self::$pdo;
    }

    /** Chạy câu lệnh có tham số, trả về PDOStatement */
    public static function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = self::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function fetch(string $sql, array $params = []): ?array
    {
        $row = self::query($sql, $params)->fetch();
        return $row === false ? null : $row;
    }

    public static function fetchAll(string $sql, array $params = []): array
    {
        return self::query($sql, $params)->fetchAll();
    }

    public static function insert(string $sql, array $params = []): int
    {
        self::query($sql, $params);
        return (int)self::pdo()->lastInsertId();
    }

    /** Chạy nhiều câu lệnh trong một transaction: lỗi ở đâu thì hoàn tác hết */
    public static function transaction(callable $callback)
    {
        $pdo = self::pdo();
        $pdo->beginTransaction();
        try {
            $result = $callback($pdo);
            $pdo->commit();
            return $result;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
