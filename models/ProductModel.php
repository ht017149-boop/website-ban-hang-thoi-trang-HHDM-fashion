<?php

require_once __DIR__ . '/BaseModel.php';

class ProductModel extends BaseModel
{
    public function getAdminProducts(): array
    {
        return $this->pdo->query(
            "SELECT p.product_id, p.product_name, p.price, p.sale_price, p.stock_quantity,
                p.image_url, p.status, p.created_at, c.category_name,
                GROUP_CONCAT(DISTINCT NULLIF(pv.size, '') ORDER BY FIELD(pv.size, 'XS', 'S', 'M', 'L', 'XL', 'XXL'), pv.size SEPARATOR ', ') AS sizes
             FROM products p
             INNER JOIN categories c ON c.category_id = p.category_id
             LEFT JOIN product_variants pv ON pv.product_id = p.product_id AND pv.status = 'active'
             GROUP BY p.product_id, p.product_name, p.price, p.sale_price, p.stock_quantity,
                p.image_url, p.status, p.created_at, c.category_name
             ORDER BY p.created_at DESC, p.product_id DESC"
        )->fetchAll();
    }

    public function getAdminCategories(): array
    {
        return $this->pdo->query(
            'SELECT category_id, category_name, status FROM categories ORDER BY category_name ASC'
        )->fetchAll();
    }

    public function findAdminProduct(int $productId): ?array
    {
        $statement = $this->pdo->prepare(
            "SELECT p.product_id, p.category_id, p.product_name, p.description, p.price,
                p.sale_price, p.stock_quantity, p.image_url, p.status,
                GROUP_CONCAT(DISTINCT NULLIF(pv.size, '') ORDER BY FIELD(pv.size, 'XS', 'S', 'M', 'L', 'XL', 'XXL'), pv.size SEPARATOR ', ') AS sizes
             FROM products p
             LEFT JOIN product_variants pv ON pv.product_id = p.product_id AND pv.status = 'active'
             WHERE p.product_id = :product_id
             GROUP BY p.product_id, p.category_id, p.product_name, p.description, p.price,
                p.sale_price, p.stock_quantity, p.image_url, p.status"
        );
        $statement->execute(['product_id' => $productId]);
        $product = $statement->fetch();

        return $product ?: null;
    }

    public function saveAdminProduct(array $product, array $sizes, ?int $productId = null): int
    {
        $this->pdo->beginTransaction();
        try {
            if ($productId === null) {
                $statement = $this->pdo->prepare(
                    'INSERT INTO products (category_id, product_name, description, price, sale_price, stock_quantity, image_url, status) '
                    . 'VALUES (:category_id, :product_name, :description, :price, :sale_price, :stock_quantity, :image_url, :status)'
                );
                $statement->execute($product);
                $productId = (int) $this->pdo->lastInsertId();
            } else {
                $statement = $this->pdo->prepare(
                    'UPDATE products SET category_id = :category_id, product_name = :product_name, description = :description, '
                    . 'price = :price, sale_price = :sale_price, stock_quantity = :stock_quantity, image_url = :image_url, status = :status '
                    . 'WHERE product_id = :product_id'
                );
                $statement->execute($product + ['product_id' => $productId]);
            }

            $deactivate = $this->pdo->prepare(
                "UPDATE product_variants SET status = 'inactive' WHERE product_id = :product_id"
            );
            $deactivate->execute(['product_id' => $productId]);

            $findVariant = $this->pdo->prepare(
                'SELECT variant_id FROM product_variants WHERE product_id = :product_id AND size = :size LIMIT 1'
            );
            $updateVariant = $this->pdo->prepare(
                "UPDATE product_variants SET color = 'Màu trung tính', stock_quantity = :stock_quantity, status = 'active'
                 WHERE variant_id = :variant_id"
            );
            $insertVariant = $this->pdo->prepare(
                "INSERT INTO product_variants (product_id, size, color, sku, stock_quantity, status)
                 VALUES (:product_id, :size, 'Màu trung tính', :sku, :stock_quantity, 'active')"
            );
            $baseStock = intdiv((int) $product['stock_quantity'], count($sizes));
            $remainingStock = (int) $product['stock_quantity'] % count($sizes);

            foreach ($sizes as $index => $size) {
                $variantStock = $baseStock + ($index === 0 ? $remainingStock : 0);
                $findVariant->execute(['product_id' => $productId, 'size' => $size]);
                $variantId = $findVariant->fetchColumn();
                if ($variantId !== false) {
                    $updateVariant->execute([
                        'stock_quantity' => $variantStock,
                        'variant_id' => $variantId,
                    ]);
                } else {
                    $insertVariant->execute([
                        'product_id' => $productId,
                        'size' => $size,
                        'sku' => 'HHDM-' . $productId . '-' . $size,
                        'stock_quantity' => $variantStock,
                    ]);
                }
            }

            $this->pdo->commit();

            return $productId;
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $exception;
        }
    }

    public function deleteAdminProduct(int $productId): void
    {
        $statement = $this->pdo->prepare('DELETE FROM products WHERE product_id = :product_id');
        $statement->execute(['product_id' => $productId]);
    }

    public function categoryExists(int $categoryId): bool
    {
        $statement = $this->pdo->prepare('SELECT 1 FROM categories WHERE category_id = :category_id LIMIT 1');
        $statement->execute(['category_id' => $categoryId]);

        return (bool) $statement->fetchColumn();
    }

    public function getHomepageData(): array
    {
        $categoryStatement = $this->pdo->query(
            "SELECT category_id, category_name, slug, description
             FROM categories
             WHERE status = 'active'
             ORDER BY category_id ASC"
        );
        $categories = $categoryStatement->fetchAll();
        $productsStatement = $this->pdo->query(
            "SELECT
                p.product_id,
                p.product_name AS name,
                CONCAT('HHDM-', LPAD(p.product_id, 6, '0')) AS code,
                CASE WHEN p.sale_price IS NOT NULL THEN p.sale_price ELSE p.price END AS price,
                CASE WHEN p.sale_price IS NOT NULL THEN p.price ELSE NULL END AS old,
                CASE
                    WHEN p.sale_price IS NOT NULL THEN 'Ưu đãi'
                    WHEN p.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 'Mới về'
                    ELSE 'Được yêu thích'
                END AS tag,
                c.slug AS category_slug,
                p.image_url AS image,
                COALESCE(NULLIF(p.description, ''), p.product_name) AS alt,
                GROUP_CONCAT(DISTINCT NULLIF(pv.size, '') ORDER BY FIELD(pv.size, 'XS', 'S', 'M', 'L', 'XL', 'XXL'), pv.size SEPARATOR ',') AS sizes,
                GROUP_CONCAT(DISTINCT NULLIF(pv.color, '') SEPARATOR ',') AS colors,
                p.created_at
             FROM products p
             INNER JOIN categories c ON c.category_id = p.category_id
             LEFT JOIN product_variants pv
                ON pv.product_id = p.product_id AND pv.status = 'active'
             WHERE p.status = 'active' AND c.status = 'active'
             GROUP BY p.product_id, p.product_name, p.price, p.sale_price,
                p.created_at, c.slug, p.image_url, p.description
             ORDER BY p.created_at DESC, p.product_id DESC"
        );
        $products = $productsStatement->fetchAll();

        foreach ($products as &$product) {
            $product['price'] = (float) $product['price'];
            $product['old'] = $product['old'] !== null ? (float) $product['old'] : null;
            $product['sizes'] = $product['sizes'] ? explode(',', $product['sizes']) : [];
            $product['colors'] = $product['colors'] ? explode(',', $product['colors']) : [];
        }
        unset($product);

        foreach ($categories as &$category) {
            $category['products'] = array_values(array_filter(
                $products,
                static fn(array $product): bool => $product['category_slug'] === $category['slug']
            ));
        }
        unset($category);

        $featuredProducts = array_values(array_filter(array_map(
            static fn(array $category): ?array => $category['products'][0] ?? null,
            $categories
        )));

        $bannerStatement = $this->pdo->query(
            "SELECT placement, eyebrow, title, description, image_url, image_alt, badge_text,
                    button_label, target_url, sort_order
             FROM homepage_banners
             WHERE status = 'active'
             ORDER BY sort_order ASC, banner_id ASC"
        );
        $banners = [];
        foreach ($bannerStatement->fetchAll() as $banner) {
            $banners[$banner['placement']] = $banner;
        }

        return [
            'categories' => $categories,
            'new_products' => $featuredProducts,
            'banners' => $banners,
        ];
    }
}
