USE website_ban_hang_thoi_trang;

UPDATE categories
SET description = 'Tất, đồ mặc trong, balo và túi đeo cho nhu cầu hằng ngày.'
WHERE slug = 'accessories';

INSERT INTO products (category_id, product_name, description, price, sale_price, stock_quantity, image_url, status)
SELECT c.category_id, seed.product_name, seed.description, seed.price, NULL, seed.stock_quantity, seed.image_url, 'active'
FROM (
  SELECT 'Tất cổ ngắn basic' AS product_name,
         'Tất cổ ngắn mềm nhẹ, dễ phối đồ. Giá 2.000₫ / đôi.' AS description,
         2000 AS price, 300 AS stock_quantity,
         'https://images.unsplash.com/photo-1586350977771-b3b0abd50c82?auto=format&fit=crop&w=780&q=85' AS image_url
  UNION ALL SELECT 'Sịp nam cotton basic',
         'Quần sịp cotton co giãn, thoáng mát, mặc thoải mái cả ngày.',
         59000, 80,
         'https://images.unsplash.com/photo-1618354691373-d851c5c3a990?auto=format&fit=crop&w=780&q=85'
  UNION ALL SELECT 'Túi đeo vai Daily',
         'Túi đeo vai gọn nhẹ, tiện dụng cho những vật dụng thiết yếu.',
         390000, 20,
         'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?auto=format&fit=crop&w=780&q=85'
) AS seed
INNER JOIN categories c ON c.slug = 'accessories'
WHERE NOT EXISTS (
  SELECT 1 FROM products existing WHERE existing.product_name = seed.product_name
);

INSERT INTO product_variants (product_id, size, color, sku, stock_quantity, additional_price, status)
SELECT p.product_id, variants.size, variants.color,
       CONCAT('HHDM-', p.product_id, '-', REPLACE(variants.size, ' ', '')),
       variants.stock_quantity, 0, 'active'
FROM products p
INNER JOIN categories c ON c.category_id = p.category_id AND c.slug = 'accessories'
INNER JOIN (
  SELECT 'Tất cổ ngắn basic' AS product_name, 'Free Size' AS size, 'Trắng' AS color, 300 AS stock_quantity
  UNION ALL SELECT 'Sịp nam cotton basic', 'S', 'Đen', 20
  UNION ALL SELECT 'Sịp nam cotton basic', 'M', 'Đen', 20
  UNION ALL SELECT 'Sịp nam cotton basic', 'L', 'Đen', 20
  UNION ALL SELECT 'Sịp nam cotton basic', 'XL', 'Đen', 20
  UNION ALL SELECT 'Túi đeo vai Daily', 'Free Size', 'Đen', 20
) AS variants ON variants.product_name = p.product_name
WHERE NOT EXISTS (
  SELECT 1 FROM product_variants existing
  WHERE existing.product_id = p.product_id AND existing.size = variants.size
);
