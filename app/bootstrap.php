<?php
/**
 * Khởi động ứng dụng: nạp cấu hình, autoload class, mở session, tính đường dẫn gốc.
 */

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('VIEW_PATH', APP_PATH . '/Views');

// ---- Cấu hình ----
$configFile = ROOT_PATH . '/config/config.php';
if (!is_file($configFile)) {
    http_response_code(500);
    exit('Chưa có file config/config.php. Hãy copy config/config.example.php thành config/config.php rồi sửa thông tin CSDL.');
}
$GLOBALS['config'] = require $configFile;

date_default_timezone_set($GLOBALS['config']['app']['timezone'] ?? 'Asia/Ho_Chi_Minh');

// ---- Hiển thị lỗi: chỉ bật khi debug ----
$debug = !empty($GLOBALS['config']['app']['debug']);
error_reporting(E_ALL);
ini_set('display_errors', $debug ? '1' : '0');
ini_set('log_errors', '1');
ini_set('error_log', ROOT_PATH . '/storage/logs/php-error.log');

// ---- Autoload: App\Core\Router -> app/Core/Router.php ----
spl_autoload_register(function (string $class): void {
    if (strncmp($class, 'App\\', 4) !== 0) {
        return;
    }
    $file = APP_PATH . '/' . str_replace('\\', '/', substr($class, 4)) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

require APP_PATH . '/helpers.php';

// ---- Đường dẫn gốc (chạy được ở localhost/thu-muc/ lẫn trên hosting) ----
$scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
// Truy cập qua .htaccess ở thư mục gốc thì URL không có "/public"
if (substr($scriptDir, -7) === '/public' && strpos($requestPath, $scriptDir) !== 0) {
    $scriptDir = substr($scriptDir, 0, -7);
}
define('BASE_PATH', $scriptDir);

// ---- Session an toàn ----
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => BASE_PATH === '' ? '/' : BASE_PATH . '/',
    'httponly' => true,
    'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'samesite' => 'Lax',
]);
session_name('EMW_SESSION');
session_start();

// Hết hạn phiên sau N phút không thao tác (NFR-10)
$timeout = 60 * (int)($GLOBALS['config']['app']['session_timeout_minutes'] ?? 30);
if (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > $timeout) {
    $_SESSION = [];
    session_regenerate_id(true);
    $_SESSION['flash']['warning'] = 'Phiên đăng nhập đã hết hạn, vui lòng đăng nhập lại.';
}
$_SESSION['last_activity'] = time();
