-- Góp ý, liên hệ (FR-30..31). user_id NULL = khách chưa đăng nhập gửi từ trang Liên hệ.
CREATE TABLE feedbacks (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id    INT UNSIGNED NULL,
    name       VARCHAR(100) NOT NULL,
    email      VARCHAR(191) NOT NULL,
    rating     TINYINT UNSIGNED NULL COMMENT '1 đến 5 sao',
    content    TEXT NOT NULL,
    status     ENUM('new', 'done') NOT NULL DEFAULT 'new',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_feedbacks_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL,
    CONSTRAINT chk_feedbacks_rating CHECK (rating IS NULL OR rating BETWEEN 1 AND 5),
    KEY idx_feedbacks_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
