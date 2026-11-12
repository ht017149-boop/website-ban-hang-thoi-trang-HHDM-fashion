<?php

require_once __DIR__ . '/BaseModel.php';

class ProductModel extends BaseModel
{
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
