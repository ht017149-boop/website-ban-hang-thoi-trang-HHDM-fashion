USE website_ban_hang_thoi_trang;

INSERT INTO categories (category_name, slug, description, status) VALUES
  ('Thời trang hằng ngày', 'everyday', 'Dễ mặc, dễ phối, luôn là chính bạn.', 'active'),
  ('Thanh lịch công sở', 'office', 'Chỉn chu trong từng chuyển động.', 'active'),
  ('Phong cách cuối tuần', 'weekend', 'Thoải mái để đi bất cứ đâu.', 'active'),
  ('Thời trang nam', 'menswear', 'Thiết kế hiện đại, linh hoạt cho phong cách nam mỗi ngày.', 'active'),
  ('Phụ kiện', 'accessories', 'Những điểm nhấn hoàn thiện phong cách của bạn.', 'active')
ON DUPLICATE KEY UPDATE
  category_name = VALUES(category_name),
  description = VALUES(description),
  status = VALUES(status);

INSERT INTO products (category_id, product_name, description, price, sale_price, stock_quantity, image_url, status)
SELECT c.category_id, seed.product_name, seed.description, seed.price, seed.sale_price,
       seed.stock_quantity, seed.image_url, 'active'
FROM (
  SELECT 'everyday' AS category_slug, 'Áo linen cổ V Luma' AS product_name,
         'Áo linen nữ cổ V màu kem, phom suông dễ phối.' AS description,
         590000 AS price, NULL AS sale_price, 30 AS stock_quantity,
         'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?auto=format&fit=crop&w=780&q=85' AS image_url
  UNION ALL SELECT 'everyday', 'Đầm midi cổ vuông Hana', 'Đầm midi nữ cổ vuông thanh lịch.', 990000, 890000, 22, 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?auto=format&fit=crop&w=780&q=85'
  UNION ALL SELECT 'everyday', 'Áo khoác nhẹ Daily', 'Áo khoác nữ nhẹ màu trung tính.', 1090000, NULL, 18, 'https://images.unsplash.com/photo-1591369822096-ffd140ec948f?auto=format&fit=crop&w=780&q=85'
  UNION ALL SELECT 'everyday', 'Quần ống rộng Miro', 'Quần ống rộng nữ cạp cao, phom thoải mái.', 720000, NULL, 26, 'https://images.unsplash.com/photo-1551163943-3f6a855d1153?auto=format&fit=crop&w=780&q=85'
  UNION ALL SELECT 'office', 'Sơ mi lụa tay dài Nari', 'Áo sơ mi nữ thanh lịch phù hợp ngày làm việc.', 790000, 680000, 16, 'https://images.unsplash.com/photo-1598554747436-c9293d6a588f?auto=format&fit=crop&w=780&q=85'
  UNION ALL SELECT 'office', 'Quần âu ống đứng Elin', 'Quần âu nữ ống đứng, dễ phối cùng sơ mi.', 790000, NULL, 20, 'https://images.unsplash.com/photo-1506629905607-d9b1b2e8c3d0?auto=format&fit=crop&w=780&q=85'
  UNION ALL SELECT 'office', 'Blazer dáng relaxed', 'Áo blazer nữ dáng rộng cho phong cách hiện đại.', 1490000, 1290000, 12, 'https://images.unsplash.com/photo-1591369822096-ffd140ec948f?auto=format&fit=crop&w=780&q=85'
  UNION ALL SELECT 'office', 'Chân váy midi Sora', 'Chân váy midi nữ màu tối, phom dáng thanh lịch.', 650000, NULL, 19, 'https://images.unsplash.com/photo-1583496661160-fb5886a13d77?auto=format&fit=crop&w=780&q=85'
  UNION ALL SELECT 'weekend', 'Áo thun boxy Sunday', 'Áo thun nữ phom boxy cho ngày cuối tuần.', 390000, NULL, 35, 'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?auto=format&fit=crop&w=780&q=85'
  UNION ALL SELECT 'weekend', 'Jeans ống suông Nola', 'Quần jeans nữ ống suông dễ phối đồ.', 850000, 750000, 23, 'https://images.unsplash.com/photo-1541099649105-f69ad21f3246?auto=format&fit=crop&w=780&q=85'
  UNION ALL SELECT 'weekend', 'Đầm hoa nhí Amie', 'Đầm nữ họa tiết hoa nhí nhẹ nhàng.', 980000, NULL, 14, 'https://images.unsplash.com/photo-1485230895905-ec40ba36b9bc?auto=format&fit=crop&w=780&q=85'
  UNION ALL SELECT 'weekend', 'Áo cardigan dệt mịn', 'Áo cardigan nữ dệt kim mềm mại.', 690000, NULL, 17, 'https://images.unsplash.com/photo-1434389677669-e08b4cac3105?auto=format&fit=crop&w=780&q=85'
  UNION ALL SELECT 'menswear', 'Áo polo nam Form Daily', 'Áo polo nam cotton thoáng nhẹ, phom gọn gàng.', 490000, NULL, 22, 'https://images.unsplash.com/photo-1618354691373-d851c5c3a990?auto=format&fit=crop&w=780&q=85'
  UNION ALL SELECT 'menswear', 'Sơ mi Oxford nam', 'Áo sơ mi Oxford nam dễ mặc đi làm và dạo phố.', 690000, NULL, 18, 'https://images.unsplash.com/photo-1618354691438-25bc04584c23?auto=format&fit=crop&w=780&q=85'
  UNION ALL SELECT 'menswear', 'Quần chino nam Straight', 'Quần chino nam ống đứng, phối đồ linh hoạt.', 790000, 690000, 16, 'https://images.unsplash.com/photo-1516826957135-700dedea698c?auto=format&fit=crop&w=780&q=85'
  UNION ALL SELECT 'menswear', 'Áo khoác nam Urban', 'Áo khoác nam phong cách hiện đại cho ngày se lạnh.', 1290000, NULL, 10, 'https://images.unsplash.com/photo-1551028719-00167b16eac5?auto=format&fit=crop&w=780&q=85'
  UNION ALL SELECT 'accessories', 'Túi tote canvas HHDM', 'Túi tote canvas tiện dụng cho lịch trình mỗi ngày.', 320000, NULL, 28, 'https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&w=780&q=85'
  UNION ALL SELECT 'accessories', 'Túi đeo chéo Mini', 'Túi đeo chéo nhỏ gọn, điểm nhấn cho trang phục.', 490000, NULL, 14, 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?auto=format&fit=crop&w=780&q=85'
  UNION ALL SELECT 'accessories', 'Đồng hồ Classic', 'Đồng hồ mặt tối giản, dây đeo dễ phối.', 890000, 790000, 12, 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=780&q=85'
  UNION ALL SELECT 'accessories', 'Balo City nhẹ', 'Balo gọn nhẹ cho công việc và những chuyến đi ngắn.', 650000, NULL, 15, 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=780&q=85'
) AS seed
INNER JOIN categories c ON c.slug = seed.category_slug
WHERE NOT EXISTS (
  SELECT 1 FROM products existing WHERE existing.product_name = seed.product_name
);

INSERT INTO product_variants (product_id, size, color, sku, stock_quantity, status)
SELECT p.product_id, sizes.size, 'Màu trung tính', CONCAT('HHDM-', p.product_id, '-', sizes.size), 8, 'active'
FROM products p
CROSS JOIN (
  SELECT 'S' AS size UNION ALL SELECT 'M' UNION ALL SELECT 'L' UNION ALL SELECT 'XL'
) AS sizes
WHERE p.product_name IN (
  'Áo linen cổ V Luma', 'Đầm midi cổ vuông Hana', 'Áo khoác nhẹ Daily', 'Quần ống rộng Miro',
  'Sơ mi lụa tay dài Nari', 'Quần âu ống đứng Elin', 'Blazer dáng relaxed', 'Chân váy midi Sora',
  'Áo thun boxy Sunday', 'Jeans ống suông Nola', 'Đầm hoa nhí Amie', 'Áo cardigan dệt mịn'
)
AND NOT EXISTS (
  SELECT 1 FROM product_variants existing
  WHERE existing.product_id = p.product_id AND existing.size = sizes.size
);

INSERT INTO product_variants (product_id, size, color, sku, stock_quantity, status)
SELECT p.product_id, sizes.size, sizes.color,
       CONCAT('HHDM-', p.product_id, '-', REPLACE(sizes.size, ' ', '')), 8, 'active'
FROM products p
INNER JOIN (
  SELECT 'Áo polo nam Form Daily' AS product_name, 'M' AS size, 'Đen' AS color
  UNION ALL SELECT 'Áo polo nam Form Daily', 'L', 'Đen'
  UNION ALL SELECT 'Áo polo nam Form Daily', 'XL', 'Đen'
  UNION ALL SELECT 'Sơ mi Oxford nam', 'M', 'Trắng'
  UNION ALL SELECT 'Sơ mi Oxford nam', 'L', 'Trắng'
  UNION ALL SELECT 'Sơ mi Oxford nam', 'XL', 'Trắng'
  UNION ALL SELECT 'Quần chino nam Straight', 'M', 'Be'
  UNION ALL SELECT 'Quần chino nam Straight', 'L', 'Be'
  UNION ALL SELECT 'Quần chino nam Straight', 'XL', 'Be'
  UNION ALL SELECT 'Áo khoác nam Urban', 'M', 'Đen'
  UNION ALL SELECT 'Áo khoác nam Urban', 'L', 'Đen'
  UNION ALL SELECT 'Áo khoác nam Urban', 'XL', 'Đen'
  UNION ALL SELECT 'Túi tote canvas HHDM', 'Free Size', 'Be'
  UNION ALL SELECT 'Túi đeo chéo Mini', 'Free Size', 'Nâu'
  UNION ALL SELECT 'Đồng hồ Classic', 'Free Size', 'Bạc'
  UNION ALL SELECT 'Balo City nhẹ', 'Free Size', 'Đen'
) AS sizes ON sizes.product_name = p.product_name
WHERE NOT EXISTS (
  SELECT 1 FROM product_variants existing
  WHERE existing.product_id = p.product_id AND existing.size = sizes.size
);

CREATE TABLE IF NOT EXISTS homepage_banners (
  banner_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  placement VARCHAR(40) NOT NULL,
  eyebrow VARCHAR(120) DEFAULT NULL,
  title VARCHAR(240) NOT NULL,
  description TEXT DEFAULT NULL,
  image_url VARCHAR(500) NOT NULL,
  image_alt VARCHAR(240) NOT NULL,
  badge_text VARCHAR(60) DEFAULT NULL,
  button_label VARCHAR(100) DEFAULT NULL,
  target_url VARCHAR(200) DEFAULT NULL,
  sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (banner_id),
  UNIQUE KEY uk_homepage_banners_placement (placement)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO homepage_banners
  (placement, eyebrow, title, description, image_url, image_alt, badge_text, button_label, target_url, sort_order, status)
VALUES
  ('hero', 'HHDM · COLLECTION 2026', 'PHONG CÁCH, KHÔNG GIỚI HẠN', 'Khám phá thời trang nữ, nam và phụ kiện cho mọi nhịp sống.', 'https://images.unsplash.com/photo-1539109136881-3be0616acf4b?auto=format&fit=crop&w=2200&q=88', 'Bộ sưu tập thời trang mới HHDM', NULL, 'KHÁM PHÁ BỘ SƯU TẬP', '#new-arrivals', 1, 'active'),
  ('sale', 'HHDM MEMBER DAYS', 'THÊM YÊU THÍCH. BỚT MỘT CHÚT GIÁ.', 'Ưu đãi theo mùa cho những thiết kế được yêu thích.', 'https://images.unsplash.com/photo-1483985988355-763728e1935b?auto=format&fit=crop&w=1800&q=85', 'Ưu đãi thời trang HHDM cho nữ và nam', 'UP TO|50%|OFF', 'XEM ƯU ĐÃI', '#new-arrivals', 2, 'active'),
  ('feature_everyday', '01 / EVERYDAY', 'CHUYỂN ĐỘNG CÙNG BẠN', 'Những lựa chọn dễ mặc cho ngày thường.', 'https://images.unsplash.com/photo-1529139574466-a303027c1d8b?auto=format&fit=crop&w=1100&q=85', 'Trang phục HHDM cho ngày thường', NULL, 'KHÁM PHÁ', '#everyday', 1, 'active'),
  ('feature_menswear', '02 / MENSWEAR', 'PHONG ĐỘ MỖI NGÀY', 'Thiết kế nam hiện đại, thoải mái và linh hoạt.', 'https://images.unsplash.com/photo-1617137968427-85924c800a22?auto=format&fit=crop&w=900&q=85', 'Bộ sưu tập thời trang nam HHDM', NULL, 'KHÁM PHÁ', '#menswear', 2, 'active'),
  ('feature_accessories', '03 / ACCESSORIES', 'ĐIỂM NHẤN CỦA BẠN', 'Phụ kiện hoàn thiện phong cách theo cách riêng.', 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?auto=format&fit=crop&w=900&q=85', 'Bộ sưu tập phụ kiện HHDM', NULL, 'KHÁM PHÁ', '#accessories', 3, 'active')
ON DUPLICATE KEY UPDATE
  eyebrow = VALUES(eyebrow), title = VALUES(title), description = VALUES(description),
  image_url = VALUES(image_url), image_alt = VALUES(image_alt), badge_text = VALUES(badge_text),
  button_label = VALUES(button_label), target_url = VALUES(target_url),
  sort_order = VALUES(sort_order), status = VALUES(status);
