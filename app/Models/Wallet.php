<?php
namespace App\Models;

use App\Core\Database;

/**
 * Bảng wallets. Phụ trách: TV4.
 * Số dư KHÔNG lưu thành cột mà tính từ giao dịch (BR-06):
 *   số dư = số dư ban đầu + tổng thu − tổng chi + tiền chuyển đến − tiền chuyển đi
 */
class Wallet
{
    public const TYPES = [
        'cash'    => 'Tiền mặt',
        'bank'    => 'Tài khoản ngân hàng',
        'ewallet' => 'Ví điện tử',
        'other'   => 'Khác',
    ];

    /** Câu SQL tính số dư của ví w (amount là BIGINT UNSIGNED nên phải CAST trước khi đổi dấu) */
    private const BALANCE_SQL = 'w.initial_balance
        + COALESCE((SELECT SUM(IF(t.type = \'income\', CAST(t.amount AS SIGNED), -CAST(t.amount AS SIGNED)))
                    FROM transactions t WHERE t.wallet_id = w.id), 0)
        + COALESCE((SELECT SUM(CAST(tr.amount AS SIGNED)) FROM transfers tr WHERE tr.to_wallet_id = w.id), 0)
        - COALESCE((SELECT SUM(CAST(tr.amount AS SIGNED)) FROM transfers tr WHERE tr.from_wallet_id = w.id), 0)';

    /** Số giao dịch và lần chuyển tiền đã dùng ví w (ví có số này > 0 thì chỉ được ẩn, không được xóa — BR-08) */
    private const USAGE_SQL = '(SELECT COUNT(*) FROM transactions t WHERE t.wallet_id = w.id)
        + (SELECT COUNT(*) FROM transfers tr WHERE tr.from_wallet_id = w.id OR tr.to_wallet_id = w.id)';

    /** Các ví đang dùng của một người, kèm số dư hiện tại */
    public static function withBalance(int $userId): array
    {
        return Database::fetchAll(
            'SELECT w.id, w.name, w.type, w.initial_balance, (' . self::BALANCE_SQL . ') AS balance
             FROM wallets w
             WHERE w.user_id = ? AND w.is_hidden = 0
             ORDER BY w.id',
            [$userId]
        );
    }

    public static function forUser(int $userId): array
    {
        return Database::fetchAll(
            'SELECT id, name, type FROM wallets WHERE user_id = ? AND is_hidden = 0 ORDER BY id',
            [$userId]
        );
    }

    public static function findForUser(int $id, int $userId): ?array
    {
        return Database::fetch(
            'SELECT id, name, type FROM wallets WHERE id = ? AND user_id = ? AND is_hidden = 0',
            [$id, $userId]
        );
    }

    /** Lấy ví của người dùng (kèm số dư ban đầu) để sửa. Không phải ví của họ thì trả về null */
    public static function findOwn(int $id, int $userId): ?array
    {
        return Database::fetch(
            'SELECT id, name, type, initial_balance FROM wallets WHERE id = ? AND user_id = ? AND is_hidden = 0',
            [$id, $userId]
        );
    }

    public static function create(int $userId, string $name, string $type, int $initialBalance): int
    {
        return Database::insert(
            'INSERT INTO wallets (user_id, name, type, initial_balance) VALUES (?, ?, ?, ?)',
            [$userId, trim($name), $type, $initialBalance]
        );
    }

    public static function update(int $id, int $userId, string $name, string $type, int $initialBalance): void
    {
        Database::query(
            'UPDATE wallets SET name = ?, type = ?, initial_balance = ? WHERE id = ? AND user_id = ?',
            [trim($name), $type, $initialBalance, $id, $userId]
        );
    }

    /** Ẩn ví: ví đã có giao dịch không được xóa (BR-08), nên chỉ đánh dấu ẩn, giao dịch cũ vẫn giữ nguyên */
    public static function hide(int $id, int $userId): void
    {
        Database::query('UPDATE wallets SET is_hidden = 1 WHERE id = ? AND user_id = ?', [$id, $userId]);
    }

    /** Các ví đã ẩn của một người (để hiện lại) */
    public static function hidden(int $userId): array
    {
        return Database::fetchAll(
            'SELECT w.id, w.name, w.type, (' . self::USAGE_SQL . ') AS used
             FROM wallets w
             WHERE w.user_id = ? AND w.is_hidden = 1
             ORDER BY w.id',
            [$userId]
        );
    }

    /** Hiện lại ví đã ẩn. Trả về false nếu không phải ví đã ẩn của người này */
    public static function unhide(int $id, int $userId): bool
    {
        return Database::query(
            'UPDATE wallets SET is_hidden = 0 WHERE id = ? AND user_id = ? AND is_hidden = 1',
            [$id, $userId]
        )->rowCount() > 0;
    }

    /**
     * Xóa hẳn một ví ĐÃ ẨN và chưa có giao dịch hay lần chuyển tiền nào (BR-08).
     * Trả về 'deleted', 'in_use' (đã có dữ liệu, chỉ được ẩn) hoặc 'missing' (không phải ví đã ẩn của người này).
     */
    public static function delete(int $id, int $userId): string
    {
        $row = Database::fetch(
            'SELECT (' . self::USAGE_SQL . ') AS used FROM wallets w WHERE w.id = ? AND w.user_id = ? AND w.is_hidden = 1',
            [$id, $userId]
        );
        if ($row === null) {
            return 'missing';
        }
        if ((int)$row['used'] > 0) {
            return 'in_use';
        }
        Database::query('DELETE FROM wallets WHERE id = ? AND user_id = ? AND is_hidden = 1', [$id, $userId]);
        return 'deleted';
    }
}
