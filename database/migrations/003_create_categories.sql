-- Danh mục thu/chi (FR-12..13, FR-28). user_id NULL = danh mục mặc định do admin quản lý.
CREATE TABLE categories (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id    INT UNSIGNED NULL,
    name       VARCHAR(100) NOT NULL,
    type       ENUM('income', 'expense') NOT NULL,
    icon       VARCHAR(50) NOT NULL DEFAULT 'tag' COMMENT 'Tên icon Phosphor, ví dụ fork-knife',
    is_hidden  TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_categories_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    KEY idx_categories_user_type (user_id, type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
