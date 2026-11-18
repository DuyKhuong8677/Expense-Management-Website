<?php
namespace App\Models;

use App\Core\Database;

/**
 * Bảng budgets (ngân sách theo tháng). Phụ trách: TV4.
 * Mỗi danh mục chi có tối đa 1 ngân sách mỗi tháng (BR-05, khóa UNIQUE trong migration 006).
 * Số đã chi tính từ bảng transactions theo ngày giao dịch (BR-12), chỉ cộng giao dịch loại chi.
 */
class Budget
{
    public const WARN_PERCENT = 80;

    /** Ngân sách của một tháng, kèm số đã chi, phần trăm và trạng thái */
    public static function forMonth(int $userId, int $year, int $month): array
    {
        $from = sprintf('%04d-%02d-01', $year, $month);
        $to = date('Y-m-t', strtotime($from));

        $rows = Database::fetchAll(
            "SELECT b.id, b.category_id, b.limit_amount, c.name AS category_name, c.icon AS category_icon,
                    COALESCE((SELECT SUM(t.amount) FROM transactions t
                              WHERE t.user_id = b.user_id AND t.category_id = b.category_id
                                AND t.type = 'expense' AND t.trans_date BETWEEN ? AND ?), 0) AS spent
             FROM budgets b
             JOIN categories c ON c.id = b.category_id
             WHERE b.user_id = ? AND b.year = ? AND b.month = ?
             ORDER BY c.name",
            [$from, $to, $userId, $year, $month]
        );

        return array_map([self::class, 'withStatus'], $rows);
    }

    /** Ngân sách của một danh mục trong tháng chứa ngày $date (dùng để cảnh báo khi thêm giao dịch) */
    public static function forCategoryOn(int $userId, int $categoryId, string $date): ?array
    {
        $year = (int)date('Y', strtotime($date));
        $month = (int)date('n', strtotime($date));
        foreach (self::forMonth($userId, $year, $month) as $b) {
            if ((int)$b['category_id'] === $categoryId) {
                return $b;
            }
        }
        return null;
    }

    /** Thêm mới, hoặc cập nhật hạn mức nếu danh mục đã có ngân sách trong tháng. Trả về true nếu là cập nhật. */
    public static function save(int $userId, int $categoryId, int $year, int $month, int $limit): bool
    {
        $existing = Database::fetch(
            'SELECT id FROM budgets WHERE user_id = ? AND category_id = ? AND year = ? AND month = ?',
            [$userId, $categoryId, $year, $month]
        );
        if ($existing) {
            Database::query('UPDATE budgets SET limit_amount = ? WHERE id = ? AND user_id = ?', [$limit, $existing['id'], $userId]);
            return true;
        }
        Database::insert(
            'INSERT INTO budgets (user_id, category_id, limit_amount, year, month) VALUES (?, ?, ?, ?, ?)',
            [$userId, $categoryId, $limit, $year, $month]
        );
        return false;
    }

    public static function delete(int $id, int $userId): bool
    {
        return Database::query('DELETE FROM budgets WHERE id = ? AND user_id = ?', [$id, $userId])->rowCount() > 0;
    }

    /** Gắn phần trăm và trạng thái: normal (< 80%), warning (80–100%), exceeded (> 100%) */
    private static function withStatus(array $b): array
    {
        $limit = (int)$b['limit_amount'];
        $spent = (int)$b['spent'];
        $percent = $limit > 0 ? (int)round($spent * 100 / $limit) : 0;

        $b['limit_amount'] = $limit;
        $b['spent'] = $spent;
        $b['percent'] = $percent;
        $b['remaining'] = $limit - $spent;
        $b['status'] = $spent > $limit ? 'exceeded' : ($percent >= self::WARN_PERCENT ? 'warning' : 'normal');
        return $b;
    }
}
