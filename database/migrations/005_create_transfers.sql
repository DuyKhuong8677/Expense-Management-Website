-- Chuyển tiền giữa hai ví của cùng người dùng (FR-11). Không tính vào thu, chi, ngân sách (BR-09).
CREATE TABLE transfers (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id        INT UNSIGNED NOT NULL,
    from_wallet_id INT UNSIGNED NOT NULL,
    to_wallet_id   INT UNSIGNED NOT NULL,
    amount         BIGINT UNSIGNED NOT NULL,
    trans_date     DATE NOT NULL,
    note           VARCHAR(255) NULL,
    created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_transfers_user FOREIGN KEY (user_id)        REFERENCES users (id)   ON DELETE CASCADE,
    CONSTRAINT fk_transfers_from FOREIGN KEY (from_wallet_id) REFERENCES wallets (id) ON DELETE RESTRICT,
    CONSTRAINT fk_transfers_to   FOREIGN KEY (to_wallet_id)   REFERENCES wallets (id) ON DELETE RESTRICT,
    CONSTRAINT chk_transfers_wallets CHECK (from_wallet_id <> to_wallet_id),
    CONSTRAINT chk_transfers_amount  CHECK (amount BETWEEN 1 AND 10000000000),
    KEY idx_transfers_user_date (user_id, trans_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
