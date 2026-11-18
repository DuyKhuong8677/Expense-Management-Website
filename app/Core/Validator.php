<?php
namespace App\Core;

/**
 * Kiểm tra dữ liệu ở phía server (luôn làm, kể cả khi JS đã kiểm tra).
 *
 *   $v = Validator::make($_POST, [
 *       'amount'     => 'required|money',
 *       'trans_date' => 'required|date|not_future',
 *       'type'       => 'required|in:income,expense',
 *   ], ['amount' => 'Số tiền']);
 *   if ($v->fails()) { ... $v->errors() ... }
 */
class Validator
{
    private array $errors = [];

    private function __construct(private array $data, array $rules, private array $labels)
    {
        foreach ($rules as $field => $ruleString) {
            foreach (explode('|', $ruleString) as $rule) {
                [$name, $arg] = array_pad(explode(':', $rule, 2), 2, null);
                if (isset($this->errors[$field])) {
                    break; // mỗi trường chỉ báo lỗi đầu tiên
                }
                $this->check($field, $name, $arg);
            }
        }
    }

    public static function make(array $data, array $rules, array $labels = []): self
    {
        return new self($data, $rules, $labels);
    }

    public function fails(): bool
    {
        return $this->errors !== [];
    }

    public function errors(): array
    {
        return $this->errors;
    }

    private function check(string $field, string $rule, ?string $arg): void
    {
        $value = $this->data[$field] ?? null;
        $value = is_string($value) ? trim($value) : $value;
        $label = $this->labels[$field] ?? $field;
        $empty = $value === null || $value === '';

        if ($rule !== 'required' && $empty) {
            return; // trường không bắt buộc mà bỏ trống thì bỏ qua các luật khác
        }

        $message = match ($rule) {
            'required'   => $empty ? "$label không được để trống." : null,
            'email'      => filter_var($value, FILTER_VALIDATE_EMAIL) ? null : "$label không hợp lệ.",
            'min'        => mb_strlen((string)$value) >= (int)$arg ? null : "$label phải có ít nhất $arg ký tự.",
            'max'        => mb_strlen((string)$value) <= (int)$arg ? null : "$label tối đa $arg ký tự.",
            'int'        => filter_var($value, FILTER_VALIDATE_INT) !== false ? null : "$label phải là số nguyên.",
            // Số tiền VNĐ: số nguyên dương, tối đa 10 tỷ (BR-01). Cho phép nhập "1.250.000"
            'money'      => self::isMoney($value) ? null : "$label phải là số nguyên dương, tối đa 10.000.000.000 ₫.",
            'date'       => self::isDate($value) ? null : "$label không đúng định dạng ngày.",
            'not_future' => self::isDate($value) && $value <= date('Y-m-d') ? null : "$label không được sau ngày hôm nay.",
            'in'         => in_array((string)$value, explode(',', (string)$arg), true) ? null : "$label không hợp lệ.",
            'same'       => $value === ($this->data[$arg] ?? null) ? null : "$label không khớp.",
            'password'   => preg_match('/[A-Za-z]/', $value) && preg_match('/\d/', $value) ? null : "$label phải có cả chữ và số.",
            default      => throw new \InvalidArgumentException("Luật kiểm tra không tồn tại: $rule"),
        };

        if ($message !== null) {
            $this->errors[$field] = $message;
        }
    }

    /** Chuyển "1.250.000" thành 1250000 */
    public static function toMoney($value): int
    {
        return (int)str_replace(['.', ',', ' '], '', (string)$value);
    }

    private static function isMoney($value): bool
    {
        $digits = str_replace(['.', ',', ' '], '', (string)$value);
        return ctype_digit($digits) && (int)$digits >= 1 && (int)$digits <= 10_000_000_000;
    }

    private static function isDate($value): bool
    {
        $d = \DateTime::createFromFormat('Y-m-d', (string)$value);
        return $d !== false && $d->format('Y-m-d') === $value;
    }
}
