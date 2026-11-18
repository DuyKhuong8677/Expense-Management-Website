<?php
/**
 * Tạo tài khoản demo có sẵn dữ liệu để quay video, chụp màn hình, thử giao diện.
 * KHÔNG phải migration: chỉ chạy trên máy mình khi cần.
 *
 *   php database/demo_seed.php        (XAMPP: C:\xampp\php\php.exe database/demo_seed.php)
 *
 * Tài khoản: demo@chitieu.local / Demo@123. Chạy lại sẽ xóa và tạo lại tài khoản demo.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Chỉ chạy được từ dòng lệnh.');
}

$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['REQUEST_URI'] = '/';
require dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\Database;
use App\Models\User;

$email = 'demo@chitieu.local';
Database::query('DELETE FROM transactions WHERE user_id IN (SELECT id FROM users WHERE email = ?)', [$email]);
Database::query('DELETE FROM transfers WHERE user_id IN (SELECT id FROM users WHERE email = ?)', [$email]);
Database::query('DELETE FROM users WHERE email = ?', [$email]);

$userId = User::register('Nguyễn Minh An', $email, 'Demo@123');
$cash = (int)Database::fetch('SELECT id FROM wallets WHERE user_id = ?', [$userId])['id'];
Database::query('UPDATE wallets SET initial_balance = 800000 WHERE id = ?', [$cash]);
$bank = Database::insert(
    "INSERT INTO wallets (user_id, name, type, initial_balance) VALUES (?, 'Vietcombank', 'bank', 2000000)",
    [$userId]
);
$momo = Database::insert(
    "INSERT INTO wallets (user_id, name, type, initial_balance) VALUES (?, 'MoMo', 'ewallet', 500000)",
    [$userId]
);

$cat = [];
foreach (Database::fetchAll('SELECT id, name FROM categories WHERE user_id IS NULL') as $c) {
    $cat[$c['name']] = (int)$c['id'];
}

// [số ngày trước hôm nay, danh mục, ví, số tiền, ghi chú]  — thu là "Lương", "Làm thêm", "Thưởng"
$rows = [
    [1,  'Ăn uống',  $cash, 45000,   'Cơm trưa'],
    [1,  'Đi lại',   $momo, 32000,   'Grab đi học'],
    [2,  'Ăn uống',  $momo, 65000,   'Trà sữa với nhóm'],
    [3,  'Học tập',  $bank, 180000,  'Giáo trình Lập trình web'],
    [4,  'Làm thêm', $bank, 1500000, 'Gia sư tuần này'],
    [5,  'Ăn uống',  $cash, 40000,   'Bánh mì, cà phê'],
    [6,  'Giải trí', $momo, 120000,  'Xem phim'],
    [7,  'Hóa đơn',  $bank, 250000,  'Tiền điện'],
    [8,  'Mua sắm',  $bank, 390000,  'Áo khoác'],
    [9,  'Ăn uống',  $cash, 55000,   'Bún bò'],
    [10, 'Đi lại',   $cash, 100000,  'Đổ xăng'],
    [12, 'Lương',    $bank, 4000000, 'Lương part-time tháng'],
    [14, 'Nhà ở',    $bank, 1800000, 'Tiền phòng trọ'],
    [16, 'Sức khỏe', $cash, 85000,   'Thuốc cảm'],
    [20, 'Ăn uống',  $cash, 50000,   'Cơm tối'],
    [26, 'Thưởng',   $bank, 500000,  'Học bổng khuyến khích'],
    [33, 'Ăn uống',  $cash, 47000,   'Cơm trưa'],
    [38, 'Mua sắm',  $momo, 210000,  'Dụng cụ học tập'],
    // Các tháng trước, để trang Báo cáo có dữ liệu so sánh
    [42, 'Lương',    $bank, 4000000, 'Lương part-time'],
    [44, 'Nhà ở',    $bank, 1800000, 'Tiền phòng trọ'],
    [47, 'Ăn uống',  $cash, 650000,  'Ăn uống cả tuần'],
    [55, 'Giải trí', $momo, 250000,  'Đi chơi cuối tuần'],
    [72, 'Lương',    $bank, 3500000, 'Lương part-time'],
    [74, 'Nhà ở',    $bank, 1800000, 'Tiền phòng trọ'],
    [78, 'Ăn uống',  $cash, 900000,  'Ăn uống cả tháng'],
    [82, 'Học tập',  $bank, 1200000, 'Học phí khóa tiếng Anh'],
    [103, 'Lương',   $bank, 3500000, 'Lương part-time'],
    [105, 'Nhà ở',   $bank, 1800000, 'Tiền phòng trọ'],
    [110, 'Ăn uống', $cash, 1100000, 'Ăn uống cả tháng'],
];
$income = ['Lương', 'Làm thêm', 'Thưởng'];

foreach ($rows as [$daysAgo, $name, $wallet, $amount, $note]) {
    Database::insert(
        'INSERT INTO transactions (user_id, wallet_id, category_id, type, amount, trans_date, note) VALUES (?, ?, ?, ?, ?, ?, ?)',
        [$userId, $wallet, $cat[$name], in_array($name, $income, true) ? 'income' : 'expense', $amount, date('Y-m-d', strtotime("-$daysAgo days")), $note]
    );
}

// Ngân sách tháng này: Ăn uống gần chạm 80%, Mua sắm đã vượt, Đi lại còn nhiều
foreach (['Ăn uống' => 400000, 'Mua sắm' => 300000, 'Đi lại' => 500000, 'Giải trí' => 300000] as $name => $limit) {
    Database::insert(
        'INSERT INTO budgets (user_id, category_id, limit_amount, year, month) VALUES (?, ?, ?, ?, ?)',
        [$userId, $cat[$name], $limit, (int)date('Y'), (int)date('n')]
    );
}

echo "Đã tạo tài khoản demo: $email / Demo@123 với " . count($rows) . " giao dịch.\n";
