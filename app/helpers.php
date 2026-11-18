<?php
/**
 * Hàm tiện ích dùng ở mọi nơi (controller và view).
 */

use App\Core\Csrf;

/** Lấy giá trị cấu hình, ví dụ config('app.name') */
function config(string $key, $default = null)
{
    $value = $GLOBALS['config'];
    foreach (explode('.', $key) as $part) {
        if (!is_array($value) || !array_key_exists($part, $value)) {
            return $default;
        }
        $value = $value[$part];
    }
    return $value;
}

/** Mã hóa dữ liệu trước khi in ra HTML (chống XSS). LUÔN dùng khi in dữ liệu người dùng. */
function e($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Tạo URL trong website: url('giao-dich') -> /Expense-Management-Website/giao-dich */
function url(string $path = ''): string
{
    $path = ltrim($path, '/');
    return BASE_PATH . '/' . $path;
}

/** URL tới file tĩnh trong public/assets, có thêm ?v= để trình duyệt không giữ bản cũ */
function asset(string $path): string
{
    $file = ROOT_PATH . '/public/assets/' . ltrim($path, '/');
    $version = is_file($file) ? filemtime($file) : 0;
    return url('assets/' . ltrim($path, '/')) . '?v=' . $version;
}

function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}

/** Lưu thông báo hiện một lần ở trang kế tiếp. $type: success | danger | warning | info */
function flash(string $type, string $message): void
{
    $_SESSION['flash'][$type] = $message;
}

function take_flashes(): array
{
    $flashes = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $flashes;
}

/** Giữ lại dữ liệu form cũ khi nhập sai */
function with_old(array $input): void
{
    unset($input['password'], $input['password_confirmation'], $input['_csrf']);
    $_SESSION['old'] = $input;
}

function old(string $key, $default = ''): string
{
    return e($_SESSION['old'][$key] ?? $default);
}

function with_errors(array $errors): void
{
    $_SESSION['errors'] = $errors;
}

/** Lỗi validation của một trường (in dưới ô nhập) */
function error_for(string $field): string
{
    $message = $_SESSION['errors'][$field] ?? null;
    return $message ? '<div class="invalid-feedback d-block">' . e($message) . '</div>' : '';
}

function has_error(string $field): string
{
    return isset($_SESSION['errors'][$field]) ? ' is-invalid' : '';
}

/** Gọi ở cuối layout để xóa old input và lỗi sau khi đã hiển thị */
function clear_form_state(): void
{
    unset($_SESSION['old'], $_SESSION['errors']);
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(Csrf::token()) . '">';
}

/** Định dạng tiền VNĐ: 1250000 -> "1.250.000 ₫" */
function money($amount): string
{
    return number_format((float)$amount, 0, ',', '.') . ' ₫';
}

/** Định dạng ngày: 2026-11-24 -> 24/11/2026 */
function vn_date(?string $date): string
{
    return $date ? date('d/m/Y', strtotime($date)) : '';
}

/** Đánh dấu menu đang chọn */
function active(string $path): string
{
    $current = trim($GLOBALS['current_path'] ?? '', '/');
    $path = trim($path, '/');
    return ($current === $path || ($path !== '' && strpos($current, $path . '/') === 0)) ? ' active' : '';
}
