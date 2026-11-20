<?php
namespace App\Models;

use App\Core\Database;

/**
 * Bảng transfers (chuyển tiền giữa hai ví của cùng một người). Phụ trách: TV4.
 * Chuyển tiền KHÔNG phải thu hay chi: không vào bảng transactions, không tính vào báo cáo hay ngân sách (BR-09).
 * Số dư ví đã tự cộng/trừ tiền chuyển trong Wallet::BALANCE_SQL.
 */
class Transfer
{
    /** Các lần chuyển gần đây của một người, mới nhất trước (kèm tên hai ví, kể cả ví đã ẩn) */
    public static function recent(int $userId, int $limit = 10): array
    {
        return Database::fetchAll(
            'SELECT tr.id, tr.amount, tr.trans_date, tr.note, wf.name AS from_name, wt.name AS to_name
             FROM transfers tr
             JOIN wallets wf ON wf.id = tr.from_wallet_id
             JOIN wallets wt ON wt.id = tr.to_wallet_id
             WHERE tr.user_id = ?
             ORDER BY tr.trans_date DESC, tr.id DESC
             LIMIT ' . max(1, $limit),
            [$userId]
        );
    }

    public static function create(int $userId, int $fromWalletId, int $toWalletId, int $amount, string $date, ?string $note): int
    {
        return Database::insert(
            'INSERT INTO transfers (user_id, from_wallet_id, to_wallet_id, amount, trans_date, note) VALUES (?, ?, ?, ?, ?, ?)',
            [$userId, $fromWalletId, $toWalletId, $amount, $date, $note]
        );
    }

    public static function delete(int $id, int $userId): bool
    {
        return Database::query('DELETE FROM transfers WHERE id = ? AND user_id = ?', [$id, $userId])->rowCount() > 0;
    }
}
