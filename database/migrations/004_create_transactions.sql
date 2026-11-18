-- Giao dịch thu/chi (FR-14..17). Số tiền là số nguyên VNĐ, 1 đến 10 tỷ (BR-01).
CREATE TABLE transactions (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED NOT NULL,
    wallet_id   INT UNSIGNED NOT NULL,
    category_id INT UNSIGNED NOT NULL,
    type        ENUM('income', 'expense') NOT NULL,
    amount      BIGINT UNSIGNED NOT NULL,
    trans_date  DATE NOT NULL COMMENT 'Ngày phát sinh; thống kê tính theo cột này (BR-12)',
    note        VARCHAR(255) NULL,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_transactions_user     FOREIGN KEY (user_id)     REFERENCES users (id)      ON DELETE CASCADE,
    CONSTRAINT fk_transactions_wallet   FOREIGN KEY (wallet_id)   REFERENCES wallets (id)    ON DELETE RESTRICT,
    CONSTRAINT fk_transactions_category FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE RESTRICT,
    CONSTRAINT chk_transactions_amount  CHECK (amount BETWEEN 1 AND 10000000000),
    -- Danh sách, lọc và thống kê luôn lọc theo user + khoảng ngày (NFR-01)
    KEY idx_transactions_user_date (user_id, trans_date),
    KEY idx_transactions_wallet (wallet_id),
    KEY idx_transactions_category (category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
