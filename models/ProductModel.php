<?php

class ProductModel
{
    private array $products = [
        [
            'slug' => 'ao-so-mi-linen-an-nhien',
            'sku' => 'HHDM-SM-001',
            'name' => 'Áo sơ mi linen An Nhiên',
            'category' => 'Áo sơ mi',
            'collection' => 'Thời trang hàng ngày',
            'price' => 590000,
            'old_price' => null,
            'badge' => 'Bán chạy',
            'description' => 'Sơ mi linen nhẹ thoáng, phom regular dễ mặc và dễ phối từ văn phòng đến những chuyến đi cuối tuần.',
            'material' => '55% linen, 45% cotton',
            'fit' => 'Regular fit',
            'color_name' => 'Be tự nhiên',
            'colors' => ['#e9dfcf', '#9eaa96', '#d9b6a5'],
            'color_labels' => ['Be tự nhiên', 'Xanh olive', 'Hồng đất'],
            'sizes' => ['S', 'M', 'L', 'XL'],
            'image' => 'https://images.unsplash.com/photo-1603252110481-7ba873bf42ab?auto=format&fit=crop&w=900&q=85',
            'gallery' => [
                'https://images.unsplash.com/photo-1603252110481-7ba873bf42ab?auto=format&fit=crop&w=1200&q=90',
                'https://images.unsplash.com/photo-1598033129183-c4f50c736f10?auto=format&fit=crop&w=1200&q=85',
                'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?auto=format&fit=crop&w=1200&q=85',
            ],
            'details' => ['Vải linen pha cotton mềm nhẹ, thoáng khí', 'Cổ bẻ cổ điển, hàng nút phía trước', 'Túi ngực tối giản và đường may gia cố', 'Phù hợp mặc riêng hoặc phối layer'],
            'care' => 'Giặt máy ở chế độ nhẹ với nước lạnh. Phơi trong bóng râm, ủi ở nhiệt độ thấp khi áo còn hơi ẩm.',
            'stock' => 'Còn hàng',
            'rating' => 4.9,
            'review_count' => 28,
        ],
        [
            'slug' => 'ao-khoac-da-moc',
            'sku' => 'HHDM-AK-014',
            'name' => 'Áo khoác da Mộc',
            'category' => 'Áo khoác',
            'collection' => 'Streetwear',
            'price' => 1290000,
            'old_price' => null,
            'badge' => 'Hàng mới',
            'description' => 'Áo khoác dáng gọn với bề mặt da mềm, lớp lót nhẹ và các chi tiết kim loại tối giản.',
            'material' => 'Da tổng hợp cao cấp, lớp lót polyester',
            'fit' => 'Relaxed fit',
            'color_name' => 'Nâu trầm',
            'colors' => ['#927763', '#343331'],
            'color_labels' => ['Nâu trầm', 'Đen'],
            'sizes' => ['M', 'L', 'XL'],
            'image' => 'https://images.unsplash.com/photo-1551028719-00167b16eac5?auto=format&fit=crop&w=900&q=85',
            'gallery' => [
                'https://images.unsplash.com/photo-1551028719-00167b16eac5?auto=format&fit=crop&w=1200&q=90',
                'https://images.unsplash.com/photo-1520975958225-35c3a3a0b5b7?auto=format&fit=crop&w=1200&q=85',
                'https://images.unsplash.com/photo-1544923246-77307dd654cb?auto=format&fit=crop&w=1200&q=85',
            ],
            'details' => ['Bề mặt mềm, hạn chế bám bụi', 'Khóa kéo kim loại chắc chắn', 'Hai túi bên tiện dụng', 'Lớp lót trơn dễ mặc cùng áo len hoặc sơ mi'],
            'care' => 'Lau bằng khăn mềm hơi ẩm. Không giặt máy, không dùng chất tẩy và tránh phơi trực tiếp dưới nắng gắt.',
            'stock' => 'Còn hàng',
            'rating' => 4.8,
            'review_count' => 16,
        ],
        [
            'slug' => 'tui-deo-cheo-gao',
            'sku' => 'HHDM-TC-008',
            'name' => 'Túi đeo chéo Gạo',
            'category' => 'Phụ kiện',
            'collection' => 'Thời trang hàng ngày',
            'price' => 790000,
            'old_price' => null,
            'badge' => 'Bán chạy',
            'description' => 'Túi đeo chéo gọn nhẹ với ngăn chứa hợp lý, phù hợp cho những ngày di chuyển trong thành phố.',
            'material' => 'Da tổng hợp, lót cotton',
            'fit' => 'Kích thước 22 × 16 × 7 cm',
            'color_name' => 'Nâu đất',
            'colors' => ['#5d4639', '#d8cbb9', '#24231f'],
            'color_labels' => ['Nâu đất', 'Be sữa', 'Đen'],
            'sizes' => ['Một kích cỡ'],
            'image' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=900&q=85',
            'gallery' => [
                'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=1200&q=90',
                'https://images.unsplash.com/photo-1553062407-8f3e1fdc4c91?auto=format&fit=crop&w=1200&q=85',
                'https://images.unsplash.com/photo-1622560480654-d96214fdc887?auto=format&fit=crop&w=1200&q=85',
            ],
            'details' => ['Ngăn chính có khóa kéo', 'Ngăn phụ phía trước để vật dụng nhỏ', 'Dây đeo có thể điều chỉnh', 'Đựng vừa điện thoại, ví và sổ tay nhỏ'],
            'care' => 'Dùng khăn mềm lau sạch bề mặt. Bảo quản ở nơi khô thoáng khi không sử dụng.',
            'stock' => 'Còn hàng',
            'rating' => 4.9,
            'review_count' => 12,
        ],
        [
            'slug' => 'ao-polo-hoa-nang',
            'sku' => 'HHDM-PL-021',
            'name' => 'Áo polo Hoa Nắng',
            'category' => 'Áo polo',
            'collection' => 'Đồ công sở',
            'price' => 890000,
            'old_price' => 1090000,
            'badge' => 'Giảm 18%',
            'description' => 'Áo polo có phom vừa vặn, bề mặt vải pique thoáng và cổ áo giữ dáng tốt suốt ngày dài.',
            'material' => '95% cotton, 5% spandex',
            'fit' => 'Regular fit',
            'color_name' => 'Kem nhạt',
            'colors' => ['#d4b7a6', '#e8e0d5'],
            'color_labels' => ['Nâu hồng', 'Kem'],
            'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
            'image' => 'https://images.unsplash.com/photo-1523398002811-999ca8dec234?auto=format&fit=crop&w=900&q=85',
            'gallery' => [
                'https://images.unsplash.com/photo-1523398002811-999ca8dec234?auto=format&fit=crop&w=1200&q=90',
                'https://images.unsplash.com/photo-1625910513413-5fc45e2a3041?auto=format&fit=crop&w=1200&q=85',
                'https://images.unsplash.com/photo-1627225924765-552d49cf47ad?auto=format&fit=crop&w=1200&q=85',
            ],
            'details' => ['Vải pique cotton co giãn nhẹ', 'Cổ dệt bo giữ phom', 'Nẹp hai nút và đường xẻ gấu nhỏ', 'Phối phù hợp với quần chinos hoặc jeans'],
            'care' => 'Giặt cùng màu ở nhiệt độ tối đa 30°C. Lộn trái khi giặt và không sấy ở nhiệt độ cao.',
            'stock' => 'Còn hàng',
            'rating' => 4.7,
            'review_count' => 21,
        ],
        [
            'slug' => 'quan-kaki-pho',
            'sku' => 'HHDM-QK-006',
            'name' => 'Quần kaki Phố',
            'category' => 'Quần dài',
            'collection' => 'Đồ công sở',
            'price' => 720000,
            'old_price' => null,
            'badge' => 'Dễ phối',
            'description' => 'Quần kaki ống đứng với độ co giãn nhẹ, dễ kết hợp cùng sơ mi, polo và áo thun.',
            'material' => '98% cotton, 2% spandex',
            'fit' => 'Straight fit',
            'color_name' => 'Be cát',
            'colors' => ['#c4b69e', '#333331'],
            'color_labels' => ['Be cát', 'Đen'],
            'sizes' => ['29', '30', '31', '32', '33', '34'],
            'image' => 'https://images.unsplash.com/photo-1473966968600-fa801b869a1a?auto=format&fit=crop&w=900&q=85',
            'gallery' => [
                'https://images.unsplash.com/photo-1473966968600-fa801b869a1a?auto=format&fit=crop&w=1200&q=90',
                'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?auto=format&fit=crop&w=1200&q=85',
                'https://images.unsplash.com/photo-1542272604-787c3835535d?auto=format&fit=crop&w=1200&q=85',
            ],
            'details' => ['Vải cotton dệt chéo bền chắc', 'Co giãn nhẹ tạo cảm giác thoải mái', 'Hai túi trước và hai túi sau', 'Ống đứng dễ phối nhiều kiểu giày'],
            'care' => 'Giặt máy ở chế độ thường với nước lạnh. Không dùng thuốc tẩy và lộn trái khi phơi.',
            'stock' => 'Còn hàng',
            'rating' => 4.8,
            'review_count' => 9,
        ],
        [
            'slug' => 'ao-thun-basic-nui',
            'sku' => 'HHDM-AT-004',
            'name' => 'Áo thun basic Núi',
            'category' => 'Áo thun',
            'collection' => 'Streetwear',
            'price' => 350000,
            'old_price' => null,
            'badge' => 'Mới về',
            'description' => 'Áo thun cotton phom rộng vừa, bề mặt mịn và đường vai hạ nhẹ cho vẻ ngoài thoải mái.',
            'material' => '100% cotton compact',
            'fit' => 'Relaxed fit',
            'color_name' => 'Đen washed',
            'colors' => ['#252525', '#f1eee8', '#79816d'],
            'color_labels' => ['Đen washed', 'Trắng', 'Xanh rêu'],
            'sizes' => ['S', 'M', 'L', 'XL'],
            'image' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=900&q=85',
            'gallery' => [
                'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=1200&q=90',
                'https://images.unsplash.com/photo-1503341504253-dff4815485f1?auto=format&fit=crop&w=1200&q=85',
                'https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?auto=format&fit=crop&w=1200&q=85',
            ],
            'details' => ['Cotton compact mềm và thoáng', 'Cổ bo dệt hạn chế bai dão', 'Đường may vai và sườn chắc chắn', 'Dễ phối cùng jeans, kaki hoặc quần short'],
            'care' => 'Giặt lộn trái với nước lạnh, không giặt chung với đồ sáng màu trong lần đầu.',
            'stock' => 'Còn hàng',
            'rating' => 4.8,
            'review_count' => 18,
        ],
        [
            'slug' => 'ao-khoac-so-mi-lang',
            'sku' => 'HHDM-AK-019',
            'name' => 'Áo khoác sơ mi Lặng',
            'category' => 'Áo khoác',
            'collection' => 'Thời trang hàng ngày',
            'price' => 850000,
            'old_price' => null,
            'badge' => 'Bán chạy',
            'description' => 'Thiết kế sơ mi khoác ngoài với chất vải dày vừa, hợp mặc layer trong những ngày mát trời.',
            'material' => '65% cotton, 35% polyester',
            'fit' => 'Relaxed fit',
            'color_name' => 'Xanh rêu',
            'colors' => ['#66705e', '#c9b99f'],
            'color_labels' => ['Xanh rêu', 'Be cát'],
            'sizes' => ['M', 'L', 'XL'],
            'image' => 'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?auto=format&fit=crop&w=900&q=85',
            'gallery' => [
                'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?auto=format&fit=crop&w=1200&q=90',
                'https://images.unsplash.com/photo-1548126032-079a0fb0099d?auto=format&fit=crop&w=1200&q=85',
                'https://images.unsplash.com/photo-1543076447-215ad9ba6923?auto=format&fit=crop&w=1200&q=85',
            ],
            'details' => ['Vải dệt dày vừa, giữ phom tốt', 'Hai túi ngực nắp gập', 'Có thể mặc như áo khoác nhẹ', 'Phù hợp phối cùng áo thun trơn'],
            'care' => 'Giặt máy chế độ nhẹ, lộn trái áo. Ủi mặt trái ở nhiệt độ trung bình.',
            'stock' => 'Còn hàng',
            'rating' => 4.9,
            'review_count' => 14,
        ],
    ];

    public function all(): array
    {
        return $this->products;
    }

    public function featured(int $limit = 4): array
    {
        return array_slice($this->products, 0, $limit);
    }

    public function findBySlug(string $slug): ?array
    {
        foreach ($this->products as $product) {
            if ($product['slug'] === $slug) {
                return $product;
            }
        }

        return null;
    }

    public function relatedTo(string $slug, int $limit = 4): array
    {
        $related = array_filter(
            $this->products,
            static fn (array $product): bool => $product['slug'] !== $slug
        );

        return array_slice(array_values($related), 0, $limit);
    }

    public function byCollection(string $collection): array
    {
        return array_values(array_filter($this->products, static fn (array $product): bool => $product['collection'] === $collection));
    }

    public function categories(): array
    {
        return array_values(array_unique(array_column($this->products, 'category')));
    }
}
