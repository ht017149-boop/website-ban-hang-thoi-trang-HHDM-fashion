USE website_ban_hang_thoi_trang;

CREATE TABLE IF NOT EXISTS order_items (
  order_item_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  order_id BIGINT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  variant_id INT UNSIGNED NOT NULL,
  quantity INT UNSIGNED NOT NULL,
  unit_price DECIMAL(12,2) NOT NULL,
  subtotal DECIMAL(14,2) NOT NULL,
  PRIMARY KEY (order_item_id),
  KEY idx_order_items_order (order_id),
  KEY idx_order_items_product (product_id),
  KEY idx_order_items_variant (variant_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE order_items MODIFY order_id BIGINT UNSIGNED NOT NULL;
