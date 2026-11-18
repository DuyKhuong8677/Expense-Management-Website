<?php
/**
 * Chạy migration: tạo CSDL (nếu chưa có) rồi chạy lần lượt các file trong database/migrations
 * chưa từng chạy. Danh sách file đã chạy lưu trong bảng `migrations`.
 *
 * Cách dùng (mở terminal ở thư mục dự án):
 *   php database/migrate.php            chạy các migration mới
 *   php database/migrate.php --fresh    XÓA HẾT bảng rồi chạy lại từ đầu (chỉ dùng trên máy mình)
 *   php database/migrate.php --status   xem file nào đã chạy
 *
 * XAMPP: thay "php" bằng C:\xampp\php\php.exe
 * Quy ước: cần thêm/sửa bảng thì tạo file mới số tiếp theo (009_...sql), KHÔNG sửa file đã merge.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Chỉ chạy được từ dòng lệnh.');
}

$root = dirname(__DIR__);
if (!is_file($root . '/config/config.php')) {
    fwrite(STDERR, "Chưa có config/config.php. Copy config/config.example.php thành config/config.php trước.\n");
    exit(1);
}
$config = (require $root . '/config/config.php')['database'];
$args = array_slice($argv, 1);

try {
    $server = new PDO(
        sprintf('mysql:host=%s;port=%d;charset=utf8mb4', $config['host'], $config['port'] ?? 3306),
        $config['user'],
        $config['pass'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $name = str_replace('`', '', $config['name']);
    $server->exec("CREATE DATABASE IF NOT EXISTS `$name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $server->exec("USE `$name`");
} catch (PDOException $e) {
    fwrite(STDERR, "Không kết nối được MySQL: {$e->getMessage()}\nKiểm tra MySQL đã bật và thông tin trong config/config.php.\n");
    exit(1);
}

if (in_array('--fresh', $args, true)) {
    $server->exec('SET FOREIGN_KEY_CHECKS = 0');
    foreach ($server->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN) as $table) {
        $server->exec('DROP TABLE `' . str_replace('`', '', $table) . '`');
    }
    $server->exec('SET FOREIGN_KEY_CHECKS = 1');
    echo "Đã xóa toàn bộ bảng.\n";
}

$server->exec('CREATE TABLE IF NOT EXISTS migrations (
    file   VARCHAR(191) PRIMARY KEY,
    ran_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

$done = $server->query('SELECT file FROM migrations')->fetchAll(PDO::FETCH_COLUMN);
$files = glob(__DIR__ . '/migrations/*.sql');
sort($files);

if (in_array('--status', $args, true)) {
    foreach ($files as $file) {
        $base = basename($file);
        echo (in_array($base, $done, true) ? '[x] ' : '[ ] ') . $base . "\n";
    }
    exit(0);
}

$count = 0;
foreach ($files as $file) {
    $base = basename($file);
    if (in_array($base, $done, true)) {
        continue;
    }
    try {
        $server->exec(file_get_contents($file));
        $server->prepare('INSERT INTO migrations (file) VALUES (?)')->execute([$base]);
        echo "Đã chạy: $base\n";
        $count++;
    } catch (PDOException $e) {
        fwrite(STDERR, "Lỗi ở $base: {$e->getMessage()}\n");
        exit(1);
    }
}

echo $count ? "Xong, chạy $count migration.\n" : "Không có migration mới.\n";
