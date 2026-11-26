USE website_ban_hang_thoi_trang;

ALTER TABLE orders
  ADD COLUMN recipient_name VARCHAR(150) NOT NULL DEFAULT '' AFTER user_id,
  ADD COLUMN recipient_phone VARCHAR(20) NOT NULL DEFAULT '' AFTER recipient_name,
  ADD COLUMN expected_delivery_from DATE NULL AFTER shipping_address,
  ADD COLUMN expected_delivery_to DATE NULL AFTER expected_delivery_from,
  ADD COLUMN shipping_company VARCHAR(100) NULL AFTER expected_delivery_to,
  ADD COLUMN tracking_number VARCHAR(100) NULL AFTER shipping_company,
  ADD COLUMN shipped_at DATETIME NULL AFTER tracking_number,
  ADD COLUMN delivered_at DATETIME NULL AFTER shipped_at;

CREATE TABLE IF NOT EXISTS late_delivery_vouchers (
  voucher_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  code VARCHAR(40) NOT NULL,
  origin_order_id BIGINT UNSIGNED NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL,
  product_id BIGINT UNSIGNED NOT NULL,
  discount_percent TINYINT UNSIGNED NOT NULL DEFAULT 15,
  status ENUM('available','reserved','redeemed','expired') NOT NULL DEFAULT 'available',
  reserved_order_id BIGINT UNSIGNED NULL,
  issued_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  expires_at DATETIME NOT NULL,
  redeemed_at DATETIME NULL,
  PRIMARY KEY (voucher_id),
  UNIQUE KEY uk_late_voucher_code (code),
  UNIQUE KEY uk_late_voucher_product (origin_order_id, product_id),
  KEY idx_late_voucher_owner (user_id, status, expires_at),
  KEY idx_late_voucher_reserved_order (reserved_order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
