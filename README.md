# Website quản lý chi tiêu cá nhân – Nhóm 4

Đồ án môn Lập trình website, Đại học Giao thông vận tải TP.HCM (UTH).
Người dùng ghi thu chi hằng ngày, quản lý nhiều ví, đặt ngân sách theo tháng và xem báo cáo bằng biểu đồ.

**Công nghệ:** PHP 8.2+ (thuần, tổ chức theo MVC) · MySQL 8 / MariaDB 10.4+ · Bootstrap 5.3 · JavaScript thuần · Chart.js · font Inter · icon Phosphor.

---

## 1. Cài đặt trên máy (làm một lần)

### Bước 1 — Tải code

**XAMPP:** clone vào `C:\xampp\htdocs\`
**Laragon:** clone vào `C:\laragon\www\`

```bash
git clone https://github.com/DuyKhuong8677/Expense-Management-Website.git
cd Expense-Management-Website
```

### Bước 2 — Tạo file cấu hình

Copy `config/config.example.php` thành `config/config.php`. Mặc định XAMPP và Laragon dùng user `root`, mật khẩu trống, nên thường không phải sửa gì.

```bash
copy config\config.example.php config\config.php
```

> `config/config.php` không được commit (đã có trong `.gitignore`), vì mỗi máy có mật khẩu CSDL riêng.

### Bước 3 — Bật Apache + MySQL rồi tạo CSDL

Mở XAMPP Control Panel (hoặc Laragon), bấm Start cho Apache và MySQL. Sau đó chạy:

```bash
# Laragon (mở Terminal của Laragon)
php database/migrate.php

# XAMPP
C:\xampp\php\php.exe database/migrate.php
```

Lệnh này tự tạo CSDL `expense_management`, các bảng, danh mục mặc định và một tài khoản admin.

### Bước 4 — Mở website

- XAMPP: http://localhost/Expense-Management-Website/
- Laragon: http://expense-management-website.test/ (hoặc http://localhost/Expense-Management-Website/)

Tài khoản admin để thử: `admin@chitieu.local` / `Admin@123` (đổi mật khẩu khi đưa lên hosting).

Không có Apache? Chạy tạm bằng server có sẵn của PHP: `php -S localhost:8000 -t public public/index.php`, rồi mở http://localhost:8000.

---

## 2. Phân công và nhánh Git

| Thành viên | Module | Nhánh |
| --- | --- | --- |
| TV1 – Trưởng nhóm | Khung dự án, migration, deploy | `feature/nen-tang` |
| TV2 | Tài khoản, Admin | `feature/tai-khoan` |
| TV3 | Giao dịch | `feature/giao-dich` |
| TV4 | Ví, danh mục, ngân sách | `feature/vi-ngan-sach` |
| TV5 | Tổng quan, báo cáo, xuất CSV | `feature/bao-cao` |
| TV6 | Giao diện chung, trang công khai, SEO | `feature/giao-dien` |

Mỗi module đã có controller và trang chờ ghi rõ việc cần làm (đăng nhập rồi bấm vào menu để xem).

### Quy trình hằng ngày

```bash
git checkout main
git pull
git checkout feature/giao-dich      # nhánh của mình (lần đầu: git checkout -b feature/giao-dich)
git merge main                      # lấy code mới nhất của cả nhóm
php database/migrate.php            # chạy migration mới nếu có

# ... code ...
git add .
git commit -m "Thêm form sửa giao dịch"
git push -u origin feature/giao-dich
```

Lên GitHub mở **Pull Request** vào `main`. Trưởng nhóm review rồi merge. **Không commit thẳng vào `main`.**

---

## 3. Cấu trúc thư mục

```
app/
  Core/          Router, Database (PDO), Auth, Csrf, Validator, Controller — KHÔNG sửa khi chưa báo TV1
  Controllers/   mỗi module một controller
  Models/        truy vấn CSDL của từng bảng
  Views/
    layouts/     app.php (sau đăng nhập), public.php (trang công khai)
    partials/    phần dùng chung: head, flash
    <module>/    view riêng của module
  routes.php     danh sách đường dẫn — mỗi người thêm route vào khối của mình
  helpers.php    e(), url(), asset(), money(), csrf_field(), old(), error_for()...
config/          config.example.php (mẫu)
database/
  migrations/    001_create_users.sql ... (chạy theo thứ tự số)
  migrate.php    script chạy migration
public/          thư mục web gốc: index.php, assets/css, assets/js, assets/img
storage/logs/    log lỗi PHP
```

### Luồng một request

`public/index.php` → `Router` (tự kiểm tra CSRF với mọi POST) → `Controller` → `Model` (truy vấn có `user_id`) → `View` trong layout.

---

## 4. Quy ước code (bắt buộc)

**Bảo mật**
- In dữ liệu người dùng ra HTML: luôn dùng `<?= e($bien) ?>`. Trong JS: `escapeHtml(bien)` trước khi đưa vào `innerHTML`.
- Truy vấn CSDL: luôn dùng `Database::query($sql, [$thamSo])`, không nối chuỗi SQL.
- Mọi truy vấn dữ liệu cá nhân phải có `WHERE user_id = ?` với `Auth::id()`.
- Form POST phải có `<?= csrf_field() ?>`. Gọi API bằng `api()` trong `app.js` (tự gửi CSRF).
- Đầu mỗi hàm controller cần đăng nhập: `$this->requireLogin();` (admin: `$this->requireAdmin();`).

**Dữ liệu**
- Số tiền lưu dạng số nguyên VNĐ (`BIGINT`), kiểm tra bằng luật `money` của `Validator`.
- Ngày trong JS: dùng `todayISO()`, **không** dùng `new Date().toISOString()` (bị lùi 1 ngày do múi giờ).
- Cần thêm/sửa bảng: tạo file migration mới số tiếp theo (`009_add_xxx.sql`). Không sửa file đã merge.

**Giao diện** (theo tài liệu Quy tắc thiết kế)
- Màu, cỡ chữ, bo góc, khoảng cách: chỉ dùng biến trong `public/assets/css/variables.css`.
- Mỗi màn hình chỉ một nút nền xanh đặc (`btn-primary`); nút phụ dùng `btn-outline-primary`.
- Số tiền: class `money`, thêm `money-income` (kèm "+") hoặc `money-expense` (kèm "−").
- Icon: Phosphor, ví dụ `<i class="ph ph-wallet" aria-hidden="true"></i>` — xem tên icon tại https://phosphoricons.com.

### Ví dụ thêm một trang

```php
// app/routes.php
$router->get('/giao-dich', [TransactionController::class, 'index']);
$router->post('/giao-dich', [TransactionController::class, 'store']);
$router->post('/giao-dich/{id}/xoa', [TransactionController::class, 'destroy']);

// app/Controllers/TransactionController.php
public function index(): void
{
    $this->requireLogin();
    $rows = Database::fetchAll(
        'SELECT * FROM transactions WHERE user_id = ? ORDER BY trans_date DESC LIMIT 20',
        [Auth::id()]
    );
    $this->view('transactions/index', ['pageTitle' => 'Giao dịch', 'rows' => $rows]);
}
```

---

## 5. Cơ sở dữ liệu

| Bảng | Nội dung |
| --- | --- |
| `users` | Người dùng, vai trò `user`/`admin`, cột khóa tài khoản và đếm lần đăng nhập sai |
| `wallets` | Ví tiền; số dư = `initial_balance` + thu − chi ± chuyển tiền (không lưu cột số dư) |
| `categories` | Danh mục thu/chi; `user_id` NULL = danh mục mặc định |
| `transactions` | Giao dịch thu/chi, index `(user_id, trans_date)` |
| `transfers` | Chuyển tiền giữa hai ví |
| `budgets` | Ngân sách theo tháng, duy nhất theo `(user_id, category_id, year, month)` |
| `feedbacks` | Góp ý từ trang Liên hệ |

Xóa sạch và tạo lại CSDL trên máy mình: `php database/migrate.php --fresh`.
