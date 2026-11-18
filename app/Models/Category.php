<?php
namespace App\Models;

use App\Core\Database;

/**
 * Bảng categories. Phụ trách: TV4.
 * Người dùng thấy danh mục mặc định (user_id IS NULL, do admin quản lý) + danh mục riêng của mình.
 * Người dùng chỉ sửa/xóa được danh mục riêng; danh mục đã có giao dịch thì chỉ ẩn, không xóa (BR-08).
 */
class Category
{
    /** Bộ icon Phosphor cho người dùng chọn khi tạo danh mục */
    public const ICONS = [
        'tag', 'fork-knife', 'coffee', 'bus', 'car', 'gas-pump', 'book-open', 'graduation-cap',
        'house', 'lightning', 'receipt', 'shopping-bag', 't-shirt', 'film-slate', 'game-controller',
        'heartbeat', 'first-aid', 'gift', 'airplane', 'paw-print', 'phone', 'wifi-high',
        'money', 'briefcase', 'piggy-bank', 'trend-up', 'hand-coins', 'dots-three-circle',
    ];

    /** Danh mục dùng được của một người, sắp theo loại rồi theo tên */
    public static function forUser(int $userId): array
    {
        return Database::fetchAll(
            'SELECT id, user_id, name, type, icon FROM categories
             WHERE (user_id IS NULL OR user_id = ?) AND is_hidden = 0
             ORDER BY type DESC, name',
            [$userId]
        );
    }

    /** Như forUser() nhưng kèm số giao dịch của người này trong từng danh mục */
    public static function forUserWithUsage(int $userId): array
    {
        return Database::fetchAll(
            'SELECT c.id, c.user_id, c.name, c.type, c.icon,
                    (SELECT COUNT(*) FROM transactions t WHERE t.category_id = c.id AND t.user_id = ?) AS used
             FROM categories c
             WHERE (c.user_id IS NULL OR c.user_id = ?) AND c.is_hidden = 0
             ORDER BY c.user_id IS NULL DESC, c.name',
            [$userId, $userId]
        );
    }

    /** Lấy một danh mục nếu người dùng được phép dùng nó, ngược lại trả về null */
    public static function findForUser(int $id, int $userId): ?array
    {
        return Database::fetch(
            'SELECT id, user_id, name, type, icon FROM categories
             WHERE id = ? AND (user_id IS NULL OR user_id = ?) AND is_hidden = 0',
            [$id, $userId]
        );
    }

    /** Chỉ lấy danh mục RIÊNG của người dùng (để sửa, xóa) */
    public static function findOwn(int $id, int $userId): ?array
    {
        return Database::fetch(
            'SELECT id, user_id, name, type, icon FROM categories WHERE id = ? AND user_id = ? AND is_hidden = 0',
            [$id, $userId]
        );
    }

    /** Đã có danh mục cùng tên, cùng loại (mặc định hoặc của mình) chưa. $exceptId: bỏ qua chính nó khi sửa */
    public static function nameTaken(int $userId, string $name, string $type, int $exceptId = 0): bool
    {
        return Database::fetch(
            'SELECT id FROM categories
             WHERE (user_id IS NULL OR user_id = ?) AND is_hidden = 0 AND type = ? AND LOWER(name) = LOWER(?) AND id <> ?',
            [$userId, $type, trim($name), $exceptId]
        ) !== null;
    }

    public static function usageCount(int $id, int $userId): int
    {
        return (int)Database::fetch(
            'SELECT (SELECT COUNT(*) FROM transactions WHERE category_id = ? AND user_id = ?)
                  + (SELECT COUNT(*) FROM budgets WHERE category_id = ? AND user_id = ?) AS n',
            [$id, $userId, $id, $userId]
        )['n'];
    }

    public static function create(int $userId, string $name, string $type, string $icon): int
    {
        return Database::insert(
            'INSERT INTO categories (user_id, name, type, icon) VALUES (?, ?, ?, ?)',
            [$userId, trim($name), $type, $icon]
        );
    }

    public static function update(int $id, int $userId, string $name, string $type, string $icon): void
    {
        Database::query(
            'UPDATE categories SET name = ?, type = ?, icon = ? WHERE id = ? AND user_id = ?',
            [trim($name), $type, $icon, $id, $userId]
        );
    }

    /** Xóa hẳn nếu chưa dùng; đã có giao dịch hoặc ngân sách thì chỉ ẩn. Trả về 'deleted' hoặc 'hidden'. */
    public static function remove(int $id, int $userId): string
    {
        if (self::usageCount($id, $userId) > 0) {
            Database::query('UPDATE categories SET is_hidden = 1 WHERE id = ? AND user_id = ?', [$id, $userId]);
            return 'hidden';
        }
        Database::query('DELETE FROM categories WHERE id = ? AND user_id = ?', [$id, $userId]);
        return 'deleted';
    }
}
