USE website_ban_hang_thoi_trang;

CREATE TABLE IF NOT EXISTS order_payments (
  order_id BIGINT UNSIGNED NOT NULL,
  payment_link_id VARCHAR(64) NOT NULL,
  checkout_url VARCHAR(500) NOT NULL,
  qr_code TEXT NOT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'pending',
  last_checked_at DATETIME NULL,
  paid_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (order_id),
  UNIQUE KEY idx_order_payments_link (payment_link_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE order_payments MODIFY order_id BIGINT UNSIGNED NOT NULL;
