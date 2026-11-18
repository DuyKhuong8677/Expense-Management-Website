<?php
/**
 * File cấu hình mẫu. Copy thành config/config.php rồi sửa cho máy của mình.
 * config/config.php KHÔNG được commit (đã có trong .gitignore) vì chứa mật khẩu.
 */
return [
    'app' => [
        'name'                    => 'Quản lý chi tiêu',
        'debug'                   => true,              // false khi đưa lên hosting
        'timezone'                => 'Asia/Ho_Chi_Minh',
        'session_timeout_minutes' => 30,                // NFR-10
    ],
    'database' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'name' => 'expense_management',
        'user' => 'root',
        'pass' => '',                                   // XAMPP/Laragon mặc định để trống
    ],
];
