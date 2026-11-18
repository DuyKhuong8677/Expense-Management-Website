<?php
namespace App\Core;

/**
 * Router đơn giản: ánh xạ "GET /giao-dich" tới [TransactionController::class, 'index'].
 * Hỗ trợ tham số dạng {id}: $router->post('/giao-dich/{id}/xoa', ...) -> destroy($id)
 * Mọi request POST đều được kiểm tra CSRF tự động.
 */
class Router
{
    private array $routes = [];

    public function get(string $path, array $action): void
    {
        $this->add('GET', $path, $action);
    }

    public function post(string $path, array $action): void
    {
        $this->add('POST', $path, $action);
    }

    private function add(string $method, string $path, array $action): void
    {
        $pattern = preg_replace('#\{(\w+)\}#', '(?P<$1>[0-9]+)', rtrim($path, '/') ?: '/');
        $this->routes[] = [$method, '#^' . $pattern . '$#', $action];
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        if (BASE_PATH !== '' && strpos($path, BASE_PATH) === 0) {
            $path = substr($path, strlen(BASE_PATH));
        }
        $path = '/' . trim($path, '/');
        $GLOBALS['current_path'] = $path;

        $pathMatched = false;
        foreach ($this->routes as [$routeMethod, $pattern, $action]) {
            if (!preg_match($pattern, $path, $matches)) {
                continue;
            }
            $pathMatched = true;
            if ($routeMethod !== $method) {
                continue;
            }
            if ($method === 'POST') {
                Csrf::verify();
            }
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            [$class, $function] = $action;
            (new $class())->$function(...array_map('intval', array_values($params)));
            return;
        }

        if ($pathMatched) {
            http_response_code(405);
            exit('Phương thức không được hỗ trợ.');
        }
        (new Controller())->notFound();
    }
}
