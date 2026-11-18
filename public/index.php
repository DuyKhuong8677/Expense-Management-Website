<?php
/**
 * Cửa vào duy nhất của website (front controller).
 * Mọi request đều đi qua file này rồi được Router chuyển tới controller phù hợp.
 */

// Khi chạy bằng "php -S", để server tự trả file tĩnh (css, js, ảnh)
if (PHP_SAPI === 'cli-server') {
    $file = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (is_file($file)) {
        return false;
    }
}

require dirname(__DIR__) . '/app/bootstrap.php';

$router = new App\Core\Router();
require APP_PATH . '/routes.php';
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
