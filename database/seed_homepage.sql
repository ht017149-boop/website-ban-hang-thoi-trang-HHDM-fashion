USE website_ban_hang_thoi_trang;

INSERT INTO categories (category_name, slug, description, status) VALUES
  ('Thời trang hằng ngày', 'everyday', 'Dễ mặc, dễ phối, luôn là chính bạn.', 'active'),
  ('Thanh lịch công sở', 'office', 'Chỉn chu trong từng chuyển động.', 'active'),
  ('Phong cách cuối tuần', 'weekend', 'Thoải mái để đi bất cứ đâu.', 'active')
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
