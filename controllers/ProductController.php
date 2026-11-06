<?php

class ProductController
{
    private ProductModel $productModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
    }

    public function index(): void
    {
        $products = $this->productModel->all();
        $categories = $this->productModel->categories();

        $categoryValue = $_GET['category'] ?? '';
        $collectionValue = $_GET['collection'] ?? '';
        $searchValue = $_GET['q'] ?? '';
        $sortValue = $_GET['sort'] ?? 'featured';

        $selectedCategory = is_string($categoryValue) ? trim($categoryValue) : '';
        $selectedCollection = is_string($collectionValue) ? trim($collectionValue) : '';
        $search = is_string($searchValue) ? trim($searchValue) : '';
        $sort = is_string($sortValue) ? $sortValue : 'featured';

        if ($selectedCategory !== '') {
            $products = array_filter(
                $products,
                static fn (array $product): bool => $product['category'] === $selectedCategory
            );
        }

        if ($selectedCollection !== '') {
            $products = array_filter(
                $products,
                static fn (array $product): bool => $product['collection'] === $selectedCollection
            );
        }

        if ($search !== '') {
            $matchesSearch = static fn (string $value): bool => function_exists('mb_stripos')
                ? mb_stripos($value, $search, 0, 'UTF-8') !== false
                : stripos($value, $search) !== false;

            $products = array_filter(
                $products,
                static fn (array $product): bool => $matchesSearch($product['name'])
                    || $matchesSearch($product['category'])
            );
        }

        if ($sort === 'price-asc') {
            usort($products, static fn (array $a, array $b): int => $a['price'] <=> $b['price']);
        } elseif ($sort === 'price-desc') {
            usort($products, static fn (array $a, array $b): int => $b['price'] <=> $a['price']);
        }

        $products = array_values($products);
        $title = 'Tất cả sản phẩm | HHDM Studio';

        require PATH_VIEW . 'products.php';
    }

    public function show(): void
    {
        $slugValue = $_GET['id'] ?? '';
        $slug = is_string($slugValue) ? trim($slugValue) : '';
        $product = $this->productModel->findBySlug($slug);

        if ($product === null) {
            http_response_code(404);
            $title = 'Không tìm thấy sản phẩm | HHDM Studio';
            require PATH_VIEW . 'not-found.php';
            return;
        }

        $relatedProducts = $this->productModel->relatedTo($slug);
        $title = $product['name'] . ' | HHDM Studio';

        require PATH_VIEW . 'product-detail.php';
    }

    public function notFound(): void
    {
        http_response_code(404);
        $title = 'Không tìm thấy trang | HHDM Studio';

        require PATH_VIEW . 'not-found.php';
    }
}
