-- Ví tiền (FR-09..10). Số dư KHÔNG lưu thành cột mà tính từ giao dịch (BR-06).
CREATE TABLE wallets (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         INT UNSIGNED NOT NULL,
    name            VARCHAR(100) NOT NULL,
    type            ENUM('cash', 'bank', 'ewallet', 'other') NOT NULL DEFAULT 'cash',
    initial_balance BIGINT NOT NULL DEFAULT 0 COMMENT 'VNĐ, cho phép âm (thẻ tín dụng)',
    is_hidden       TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Ví đã có giao dịch chỉ được ẩn, không xóa (BR-08)',
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_wallets_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    KEY idx_wallets_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
