<?php
namespace App\Models;

use App\Core\Database;

/**
 * Bảng transactions (giao dịch thu/chi). Phụ trách: TV3.
 * Mọi hàm đều nhận $userId và lọc WHERE user_id = ?, để không ai đọc/sửa được dữ liệu của người khác.
 */
class Transaction
{
    public const PER_PAGE = 20;

    /**
     * Dựng điều kiện WHERE từ bộ lọc.
     * $f: from, to (bắt buộc, Y-m-d), type, category_id, wallet_id, q (tùy chọn)
     */
    private static function where(int $userId, array $f): array
    {
        $sql = 't.user_id = ? AND t.trans_date BETWEEN ? AND ?';
        $params = [$userId, $f['from'], $f['to']];

        if (!empty($f['type'])) {
            $sql .= ' AND t.type = ?';
            $params[] = $f['type'];
        }
        if (!empty($f['category_id'])) {
            $sql .= ' AND t.category_id = ?';
            $params[] = (int)$f['category_id'];
        }
        if (!empty($f['wallet_id'])) {
            $sql .= ' AND t.wallet_id = ?';
            $params[] = (int)$f['wallet_id'];
        }
        if (!empty($f['q'])) {
            $sql .= ' AND t.note LIKE ?';
            $params[] = '%' . $f['q'] . '%';
        }
        return [$sql, $params];
    }

    /** Danh sách giao dịch có lọc + phân trang (FR-05, FR-06, FR-16) */
    public static function paginate(int $userId, array $filters, int $page): array
    {
        [$where, $params] = self::where($userId, $filters);

        $total = (int)Database::fetch("SELECT COUNT(*) AS n FROM transactions t WHERE $where", $params)['n'];
        $pages = max(1, (int)ceil($total / self::PER_PAGE));
        $page = min(max(1, $page), $pages);
        $offset = ($page - 1) * self::PER_PAGE;

        // LIMIT/OFFSET là số nguyên đã ép kiểu ở trên nên ghép thẳng vào câu SQL an toàn
        $rows = Database::fetchAll(
            "SELECT t.id, t.type, t.amount, t.trans_date, t.note, t.category_id, t.wallet_id,
                    c.name AS category_name, c.icon AS category_icon, w.name AS wallet_name
             FROM transactions t
             JOIN categories c ON c.id = t.category_id
             JOIN wallets w ON w.id = t.wallet_id
             WHERE $where
             ORDER BY t.trans_date DESC, t.id DESC
             LIMIT " . self::PER_PAGE . " OFFSET $offset",
            $params
        );

        return ['rows' => $rows, 'total' => $total, 'page' => $page, 'pages' => $pages];
    }

    /** Tổng thu, tổng chi theo bộ lọc. Chỉ cộng đúng loại, không cộng lẫn thu với chi. */
    public static function totals(int $userId, array $filters): array
    {
        [$where, $params] = self::where($userId, $filters);
        $row = Database::fetch(
            "SELECT COALESCE(SUM(CASE WHEN t.type = 'income'  THEN t.amount END), 0) AS income,
                    COALESCE(SUM(CASE WHEN t.type = 'expense' THEN t.amount END), 0) AS expense
             FROM transactions t WHERE $where",
            $params
        );
        return ['income' => (int)$row['income'], 'expense' => (int)$row['expense']];
    }

    /** Chi theo danh mục trong khoảng ngày (cho biểu đồ tròn) */
    public static function expenseByCategory(int $userId, string $from, string $to): array
    {
        return Database::fetchAll(
            "SELECT c.name, SUM(t.amount) AS total
             FROM transactions t JOIN categories c ON c.id = t.category_id
             WHERE t.user_id = ? AND t.type = 'expense' AND t.trans_date BETWEEN ? AND ?
             GROUP BY c.id, c.name
             ORDER BY total DESC",
            [$userId, $from, $to]
        );
    }

    public static function recent(int $userId, int $limit = 5): array
    {
        return Database::fetchAll(
            'SELECT t.id, t.type, t.amount, t.trans_date, t.note, c.name AS category_name, c.icon AS category_icon, w.name AS wallet_name
             FROM transactions t
             JOIN categories c ON c.id = t.category_id
             JOIN wallets w ON w.id = t.wallet_id
             WHERE t.user_id = ?
             ORDER BY t.trans_date DESC, t.id DESC
             LIMIT ' . (int)$limit,
            [$userId]
        );
    }

    public static function findForUser(int $id, int $userId): ?array
    {
        return Database::fetch(
            'SELECT id, type, amount, trans_date, note, category_id, wallet_id
             FROM transactions WHERE id = ? AND user_id = ?',
            [$id, $userId]
        );
    }

    public static function create(int $userId, array $d): int
    {
        return Database::insert(
            'INSERT INTO transactions (user_id, wallet_id, category_id, type, amount, trans_date, note)
             VALUES (?, ?, ?, ?, ?, ?, ?)',
            [$userId, $d['wallet_id'], $d['category_id'], $d['type'], $d['amount'], $d['trans_date'], $d['note']]
        );
    }

    public static function update(int $id, int $userId, array $d): bool
    {
        Database::query(
            'UPDATE transactions
             SET wallet_id = ?, category_id = ?, type = ?, amount = ?, trans_date = ?, note = ?
             WHERE id = ? AND user_id = ?',
            [$d['wallet_id'], $d['category_id'], $d['type'], $d['amount'], $d['trans_date'], $d['note'], $id, $userId]
        );
        return true;
    }

    public static function delete(int $id, int $userId): bool
    {
        return Database::query('DELETE FROM transactions WHERE id = ? AND user_id = ?', [$id, $userId])->rowCount() > 0;
    }
}
