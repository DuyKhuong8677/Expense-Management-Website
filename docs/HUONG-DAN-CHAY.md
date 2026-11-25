# Hướng dẫn chạy dự án trên máy (Nhóm 4)

Làm theo từ trên xuống, mất khoảng 20–30 phút. Mỗi bước có dòng **Đúng khi** để tự kiểm tra trước khi sang bước sau. Làm tới đâu bị lỗi thì xem mục [Lỗi thường gặp](#9-lỗi-thường-gặp), không được thì chụp màn hình gửi nhóm.

Hướng dẫn viết cho **Windows + XAMPP**.

---

## 1. Cài phần mềm (làm một lần)

| Phần mềm | Tải ở đâu | Ghi chú |
| --- | --- | --- |
| XAMPP (bản PHP 8.2) | https://www.apachefriends.org | Cài vào `C:\xampp` (mặc định) |
| Git | https://git-scm.com/download/win | Cứ bấm Next tới hết |
| VS Code | https://code.visualstudio.com | Để sửa code |

Khi cài XAMPP:
- Nếu hiện cảnh báo về **UAC** thì bấm **OK** và cài tiếp. Vì cài vào `C:\xampp` chứ không phải `C:\Program Files` nên không bị ảnh hưởng.
- Ở màn chọn thành phần, giữ mặc định. Bắt buộc phải có **Apache, MySQL, PHP, phpMyAdmin**.

Sau khi cài Git, mở **Command Prompt** và khai báo tên của mình (thay bằng tên thật và email GitHub):

```bat
git config --global user.name "Nguyen Van A"
git config --global user.email "email-github-cua-ban@gmail.com"
```

> Trưởng nhóm phải thêm bạn vào repo trên GitHub (Settings → Collaborators). Bạn mở email, bấm **Accept invitation** thì mới push được.

---

## 2. Tải code về

Mở **Command Prompt** (bấm Start, gõ `cmd`, Enter) rồi chạy:

```bat
cd C:\xampp\htdocs
git clone https://github.com/DuyKhuong8677/Expense-Management-Website.git
cd Expense-Management-Website
```

**Đúng khi:** có thư mục `C:\xampp\htdocs\Expense-Management-Website`, bên trong có `app`, `config`, `database`, `public`.

> Lần đầu clone, Git có thể mở cửa sổ đăng nhập GitHub. Đăng nhập bằng tài khoản đã được mời vào repo.

---

## 3. Tạo file cấu hình

Vẫn trong cửa sổ Command Prompt đó:

```bat
copy config\config.example.php config\config.php
```

**Đúng khi:** hiện `1 file(s) copied.`

File này chứa thông tin kết nối MySQL. XAMPP mặc định dùng user `root`, mật khẩu trống, nên không cần sửa gì. File này không được đưa lên GitHub vì mỗi máy có thể khác nhau.

---

## 4. Bật Apache và MySQL

1. Mở **XAMPP Control Panel** (Start → gõ `XAMPP`).
2. Bấm **Start** ở dòng **Apache**, rồi **Start** ở dòng **MySQL**.

**Đúng khi:** cả hai dòng có nền xanh lá, cột Port ghi `80, 443` và `3306`.

Nếu MySQL báo đỏ `Port 3306 in use` thì máy đang có một MySQL khác chạy sẵn. Xem cách sửa ở [mục 9](#9-lỗi-thường-gặp).

---

## 5. Tạo cơ sở dữ liệu

Quay lại Command Prompt (đang ở thư mục dự án) và chạy lần lượt:

```bat
C:\xampp\php\php.exe database/migrate.php
C:\xampp\php\php.exe database/demo_seed.php
```

**Đúng khi:**
- Lệnh thứ nhất in ra 8 dòng `Đã chạy: 001_create_users.sql` … `008_seed_defaults.sql`, rồi `Xong, chạy 8 migration.`
- Lệnh thứ hai in ra `Đã tạo tài khoản demo: demo@chitieu.local / Demo@123 với 29 giao dịch.`

`migrate.php` tạo CSDL `expense_management`, 7 bảng, danh mục mặc định và tài khoản admin.
`demo_seed.php` tạo một tài khoản có sẵn ví, giao dịch, ngân sách để thử cho nhanh. Chạy lại lệnh này lúc nào cũng được, nó sẽ xóa và tạo lại tài khoản demo.

---

## 6. Mở website

Vào trình duyệt, mở: **http://localhost/Expense-Management-Website/**

| Tài khoản | Email | Mật khẩu |
| --- | --- | --- |
| Người dùng có dữ liệu mẫu | `demo@chitieu.local` | `Demo@123` |
| Quản trị | `admin@chitieu.local` | `Admin@123` |

**Đúng khi:** đăng nhập bằng tài khoản demo thì vào trang **Tổng quan** có số liệu và biểu đồ. Bấm thử Giao dịch, Ví, Danh mục, Ngân sách, Báo cáo ở menu trái.

Muốn xem dữ liệu trong CSDL: mở **http://localhost/phpmyadmin**, bấm `expense_management` ở cột trái.

---

## 7. Mỗi ngày làm việc với Git

Mỗi người làm trên **nhánh của mình**, không sửa thẳng nhánh `main`.

| Thành viên | Module | Nhánh |
| --- | --- | --- |
| Nguyễn Đình Duy Khương (TV1, trưởng nhóm) | Khung dự án, CSDL, triển khai | `feature/nen-tang` |
| Phạm Thành Đô (TV2) | Tài khoản, Admin | `feature/tai-khoan` |
| Đỗ Trung Kiên (TV3) | Giao dịch | `feature/giao-dich` |
| Đỗ Nhựt Huy (TV4) | Ví, danh mục, ngân sách | `feature/vi-ngan-sach` |
| Nguyễn Hào Hiệp (TV5) | Tổng quan, báo cáo | `feature/bao-cao` |
| Hoàng Duy Khánh (TV6) | Giao diện, trang công khai, SEO | `feature/giao-dien` |

**Lần đầu**, tạo nhánh của mình (ví dụ TV3):

```bat
git checkout main
git pull
git checkout -b feature/giao-dich
```

**Mỗi lần bắt đầu làm**, lấy code mới nhất của cả nhóm:

```bat
git checkout main
git pull
git checkout feature/giao-dich
git merge main
C:\xampp\php\php.exe database/migrate.php
```

Lệnh cuối chạy các file tạo bảng mới nếu bạn khác vừa thêm. Không có gì mới thì nó in `Không có migration mới.`

**Làm xong một phần**, đưa lên GitHub:

```bat
git add .
git commit -m "Thêm chức năng sửa giao dịch"
git push -u origin feature/giao-dich
```

Rồi lên trang GitHub của repo, bấm **Compare & pull request** để mở Pull Request vào `main`. Trưởng nhóm xem lại rồi gộp vào.

> Commit message viết tiếng Việt, nói rõ đã làm gì. Không commit file `config/config.php`.

---

## 8. Biết sửa code ở đâu

| Muốn làm | Sửa file |
| --- | --- |
| Thêm đường dẫn mới | `app/routes.php` (thêm vào đúng khối của module mình) |
| Xử lý khi bấm nút, kiểm tra dữ liệu | `app/Controllers/<Module>Controller.php` |
| Câu lệnh SQL | `app/Models/<Bảng>.php` |
| Giao diện trang | `app/Views/<module>/index.php` |
| Màu, cỡ chữ | `public/assets/css/variables.css` (chỉ dùng biến có sẵn) |
| JavaScript của trang | `public/assets/js/<module>.js` |
| Thêm hoặc sửa bảng | Tạo file mới `database/migrations/009_....sql`, **không sửa file đã có** |

Quy ước code chi tiết (bảo mật, số tiền, giao diện) xem trong `README.md`, mục 4.

---

## 9. Lỗi thường gặp

| Hiện tượng | Cách sửa |
| --- | --- |
| MySQL không Start, báo `Port 3306 in use` | Máy có MySQL cài riêng đang chạy. Bấm Windows + R, gõ `services.msc`, tìm **MySQL** hoặc **MySQL80**, chuột phải chọn **Stop**, rồi vào **Properties** đổi **Startup type** thành **Manual**. Quay lại XAMPP bấm Start. |
| Apache không Start, báo `Port 80 in use` | Thường do Skype, IIS hoặc VMware. Tắt các chương trình đó rồi Start lại. |
| Chạy migrate báo `Không kết nối được MySQL` | MySQL trong XAMPP chưa bật (bước 4). |
| Báo `Chưa có config/config.php` | Làm lại bước 3. |
| `'C:\xampp\php\php.exe' is not recognized` | XAMPP không cài ở `C:\xampp`. Tìm thư mục cài XAMPP và thay đường dẫn cho đúng. |
| Trang chủ mở được nhưng bấm Đăng nhập thì ra **404 Not Found** | Mở `C:\xampp\apache\conf\httpd.conf` bằng Notepad, tìm dòng `#LoadModule rewrite_module modules/mod_rewrite.so`, xóa dấu `#` ở đầu, lưu lại. Trong XAMPP bấm Stop rồi Start lại Apache. |
| Đăng nhập báo sai mật khẩu với tài khoản demo | Chưa chạy `demo_seed.php` (bước 5). |
| Muốn xóa sạch CSDL làm lại từ đầu | `C:\xampp\php\php.exe database/migrate.php --fresh`, rồi chạy lại `demo_seed.php`. |
| `git push` báo `Permission denied` hoặc `403` | Chưa nhận lời mời vào repo (mục 1), hoặc đăng nhập sai tài khoản GitHub. |
| `git merge main` báo `CONFLICT` | Hai người sửa cùng một chỗ. Mở file trong VS Code, chọn giữ phần nào, lưu, rồi `git add .` và `git commit`. Không chắc thì hỏi trưởng nhóm trước. |

Lỗi PHP chi tiết được ghi trong `storage/logs/php-error.log`.
