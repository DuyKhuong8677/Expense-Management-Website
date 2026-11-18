-- Dữ liệu ban đầu: danh mục mặc định (FR-12) và tài khoản admin để demo.
-- Admin: admin@chitieu.local / Admin@123 — ĐỔI MẬT KHẨU khi đưa lên hosting.
INSERT INTO categories (user_id, name, type, icon) VALUES
    (NULL, 'Lương',      'income',  'money'),
    (NULL, 'Thưởng',     'income',  'gift'),
    (NULL, 'Làm thêm',   'income',  'briefcase'),
    (NULL, 'Thu khác',   'income',  'plus-circle'),
    (NULL, 'Ăn uống',    'expense', 'fork-knife'),
    (NULL, 'Đi lại',     'expense', 'bus'),
    (NULL, 'Học tập',    'expense', 'book-open'),
    (NULL, 'Nhà ở',      'expense', 'house'),
    (NULL, 'Hóa đơn',    'expense', 'receipt'),
    (NULL, 'Mua sắm',    'expense', 'shopping-bag'),
    (NULL, 'Giải trí',   'expense', 'film-slate'),
    (NULL, 'Sức khỏe',   'expense', 'heartbeat'),
    (NULL, 'Chi khác',   'expense', 'dots-three-circle');

INSERT INTO users (full_name, email, password_hash, role) VALUES
    ('Quản trị viên', 'admin@chitieu.local', '$2y$10$tTdtr60oS80D7xP8wKE6/uaesgS550DxUREbD5u9pfER2Pe9QTWIq', 'admin');
