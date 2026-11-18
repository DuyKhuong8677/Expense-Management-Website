-- Ngân sách theo tháng cho từng danh mục chi (FR-18..19). Mỗi danh mục tối đa 1 ngân sách/tháng (BR-05).
CREATE TABLE budgets (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id      INT UNSIGNED NOT NULL,
    category_id  INT UNSIGNED NOT NULL,
    limit_amount BIGINT UNSIGNED NOT NULL,
    year         SMALLINT UNSIGNED NOT NULL,
    month        TINYINT UNSIGNED NOT NULL,
    created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_budgets_user     FOREIGN KEY (user_id)     REFERENCES users (id)      ON DELETE CASCADE,
    CONSTRAINT fk_budgets_category FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE CASCADE,
    CONSTRAINT chk_budgets_month   CHECK (month BETWEEN 1 AND 12),
    CONSTRAINT chk_budgets_amount  CHECK (limit_amount >= 1),
    UNIQUE KEY uq_budgets_category_month (user_id, category_id, year, month)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
