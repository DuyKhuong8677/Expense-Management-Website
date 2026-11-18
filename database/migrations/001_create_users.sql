-- Người dùng và quản trị viên (FR-01..03, FR-24..29)
CREATE TABLE users (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name       VARCHAR(100) NOT NULL,
    email           VARCHAR(191) NOT NULL,
    password_hash   VARCHAR(255) NOT NULL,
    role            ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    avatar          VARCHAR(255) NULL,
    is_locked       TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Admin khóa tài khoản (FR-27)',
    failed_attempts TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Số lần đăng nhập sai liên tiếp (FR-03)',
    locked_until    DATETIME NULL COMMENT 'Tạm khóa đăng nhập tới thời điểm này (FR-03)',
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
