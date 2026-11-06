<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f8f6f2">
    <meta name="description" content="<?= htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8') ?>">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/main.css">
    <script src="assets/js/main.js" defer></script>
</head>
<body class="product-page">
    <?php require PATH_VIEW . 'partials/header.php'; ?>

    <main>
        <nav class="breadcrumbs" aria-label="Đường dẫn">
            <a href="?action=/">Trang chủ</a>
            <span aria-hidden="true">/</span>
            <a href="?action=products">Sản phẩm</a>
            <span aria-hidden="true">/</span>
            <span><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></span>
        </nav>

        <section class="product-detail">
            <div class="product-gallery">
                <div class="product-gallery__main">
                    <img id="product-main-image" src="<?= htmlspecialchars($product['gallery'][0], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>">
                    <?php if (!empty($product['badge'])): ?>
                        <span class="badge"><?= htmlspecialchars($product['badge'], ENT_QUOTES, 'UTF-8') ?></span>
                    <?php endif; ?>
                </div>
                <div class="product-gallery__thumbs" aria-label="Ảnh sản phẩm">
                    <?php foreach ($product['gallery'] as $index => $image): ?>
                        <button class="gallery-thumb <?= $index === 0 ? 'is-active' : '' ?>" type="button" data-image="<?= htmlspecialchars($image, ENT_QUOTES, 'UTF-8') ?>" aria-label="Xem ảnh <?= $index + 1 ?>">
                            <img src="<?= htmlspecialchars($image, ENT_QUOTES, 'UTF-8') ?>" alt="" loading="lazy">
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="product-detail__info">
                <div class="product-detail__meta">
                    <span><?= htmlspecialchars($product['category'], ENT_QUOTES, 'UTF-8') ?></span>
                    <span class="rating">★ <?= number_format($product['rating'], 1) ?> <a href="#product-reviews">(<?= (int) $product['review_count'] ?> đánh giá)</a></span>
                </div>
                <h1><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></h1>
                <div class="product-detail__sku">Mã sản phẩm: <?= htmlspecialchars($product['sku'], ENT_QUOTES, 'UTF-8') ?></div>

                <div class="product-detail__price">
                    <strong><?= number_format($product['price'], 0, ',', '.') ?>₫</strong>
                    <?php if (!empty($product['old_price'])): ?>
                        <del><?= number_format($product['old_price'], 0, ',', '.') ?>₫</del>
                        <span class="discount-label">Tiết kiệm <?= number_format($product['old_price'] - $product['price'], 0, ',', '.') ?>₫</span>
                    <?php endif; ?>
                </div>

                <p class="product-detail__description">
                    <?= htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8') ?>
                </p>

                <div class="detail-option">
                    <div class="detail-option__heading">
                        <span>Màu sắc: <b id="selected-color-label"><?= htmlspecialchars($product['color_name'], ENT_QUOTES, 'UTF-8') ?></b></span>
                    </div>
                    <div class="detail-swatches" role="group" aria-label="Chọn màu">
                        <?php foreach ($product['colors'] as $index => $color): ?>
                            <button class="detail-swatch <?= $index === 0 ? 'is-active' : '' ?>" type="button" style="--swatch-color: <?= htmlspecialchars($color, ENT_QUOTES, 'UTF-8') ?>" aria-label="<?= htmlspecialchars($product['color_labels'][$index] ?? ('Màu ' . ($index + 1)), ENT_QUOTES, 'UTF-8') ?>" data-color-name="<?= htmlspecialchars($product['color_labels'][$index] ?? ('Màu ' . ($index + 1)), ENT_QUOTES, 'UTF-8') ?>"></button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php if (count($product['sizes']) > 1): ?>
                    <div class="detail-option">
                        <div class="detail-option__heading">
                            <span>Chọn kích cỡ: <b id="selected-size-label">Chưa chọn</b></span>
                            <button class="text-button" type="button" data-open-size-guide>Mình chọn size nào?</button>
                        </div>
                        <div class="size-options" role="group" aria-label="Chọn kích cỡ">
                            <?php foreach ($product['sizes'] as $size): ?>
                                <button class="size-option" type="button" data-size="<?= htmlspecialchars($size, ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars($size, ENT_QUOTES, 'UTF-8') ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                        <p class="form-message" id="size-message" aria-live="polite"></p>
                    </div>
                <?php else: ?>
                    <span class="single-size-label">Kích cỡ: <b><?= htmlspecialchars($product['sizes'][0], ENT_QUOTES, 'UTF-8') ?></b></span>
                    <input type="hidden" id="selected-size" value="<?= htmlspecialchars($product['sizes'][0], ENT_QUOTES, 'UTF-8') ?>">
                <?php endif; ?>

                <div class="purchase-row">
                    <div class="quantity-control" aria-label="Số lượng">
                        <button type="button" data-quantity-change="-1" aria-label="Giảm số lượng">−</button>
                        <input id="quantity" type="number" min="1" max="10" value="1" aria-label="Số lượng sản phẩm">
                        <button type="button" data-quantity-change="1" aria-label="Tăng số lượng">+</button>
                    </div>
                    <span class="stock-status"><i></i><?= htmlspecialchars($product['stock'], ENT_QUOTES, 'UTF-8') ?></span>
                </div>

                <button class="button product-add-button" id="product-add" type="button" data-product-name="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>">
                    Thêm vào giỏ hàng <span aria-hidden="true">→</span>
                </button>
                <p class="cart-message" id="cart-message" aria-live="polite"></p>

                <div class="product-policies">
                    <div><b>Giao hàng toàn quốc</b><span>Miễn phí cho đơn hàng từ 899.000₫</span></div>
                    <div><b>Đổi size linh hoạt</b><span>Hỗ trợ đổi size trong 7 ngày</span></div>
                    <div><b>Thanh toán an tâm</b><span>Kiểm tra sản phẩm khi nhận hàng</span></div>
                </div>
            </div>
        </section>

        <section class="product-information">
            <div class="product-tabs" role="tablist" aria-label="Thông tin sản phẩm">
                <button class="product-tab is-active" id="tab-description" type="button" role="tab" aria-selected="true" aria-controls="panel-description">Mô tả sản phẩm</button>
                <button class="product-tab" id="tab-specifications" type="button" role="tab" aria-selected="false" aria-controls="panel-specifications">Thông tin &amp; bảo quản</button>
                <button class="product-tab" id="tab-reviews" type="button" role="tab" aria-selected="false" aria-controls="panel-reviews">Đánh giá (<?= (int) $product['review_count'] ?>)</button>
            </div>
            <div class="product-tab-panel is-active" id="panel-description" role="tabpanel" aria-labelledby="tab-description">
                <h2><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></h2>
                <p><?= htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8') ?></p>
                <ul>
                    <?php foreach ($product['details'] as $detail): ?>
                        <li><?= htmlspecialchars($detail, ENT_QUOTES, 'UTF-8') ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="product-tab-panel" id="panel-specifications" role="tabpanel" aria-labelledby="tab-specifications" hidden>
                <dl class="specification-list">
                    <div><dt>Mã sản phẩm</dt><dd><?= htmlspecialchars($product['sku'], ENT_QUOTES, 'UTF-8') ?></dd></div>
                    <div><dt>Chất liệu</dt><dd><?= htmlspecialchars($product['material'], ENT_QUOTES, 'UTF-8') ?></dd></div>
                    <div><dt>Kiểu dáng / kích thước</dt><dd><?= htmlspecialchars($product['fit'], ENT_QUOTES, 'UTF-8') ?></dd></div>
                    <div><dt>Màu sắc</dt><dd><?= htmlspecialchars($product['color_name'], ENT_QUOTES, 'UTF-8') ?></dd></div>
                    <div><dt>Hướng dẫn bảo quản</dt><dd><?= htmlspecialchars($product['care'], ENT_QUOTES, 'UTF-8') ?></dd></div>
                </dl>
            </div>
            <div class="product-tab-panel" id="panel-reviews" role="tabpanel" aria-labelledby="tab-reviews" hidden>
                <div class="review-summary"><strong><?= number_format($product['rating'], 1) ?></strong><span>★★★★★</span><p>Dựa trên <?= (int) $product['review_count'] ?> đánh giá sản phẩm.</p></div>
                <p>Khách hàng đánh giá cao phom dáng, chất liệu và khả năng phối đồ của sản phẩm.</p>
            </div>
        </section>

        <section class="section related-products">
            <div class="section-heading section-heading--row">
                <div>
                    <span class="eyebrow">CÓ THỂ BẠN SẼ THÍCH</span>
                    <h2>Gợi ý dành cho bạn</h2>
                </div>
                <a class="text-link" href="?action=products">Xem tất cả →</a>
            </div>
            <div class="products">
                <?php foreach ($relatedProducts as $product): ?>
                    <?php require PATH_VIEW . 'partials/product-card.php'; ?>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <dialog class="size-guide" id="size-guide">
        <form method="dialog"><button class="dialog-close" aria-label="Đóng">×</button></form>
        <span class="eyebrow">HHDM SIZE GUIDE</span>
        <h2>Hướng dẫn chọn size</h2>
        <p>Chọn kích cỡ thường mặc. Nếu bạn thích phom rộng hoặc nằm giữa hai size, hãy chọn size lớn hơn.</p>
        <div class="size-table-wrap">
            <?php if ($product['category'] === 'Quần dài'): ?>
                <table>
                    <thead><tr><th>Size</th><th>Vòng eo gợi ý</th><th>Vòng mông gợi ý</th></tr></thead>
                    <tbody>
                        <tr><td>29</td><td>74 – 77 cm</td><td>94 – 97 cm</td></tr>
                        <tr><td>30</td><td>77 – 80 cm</td><td>97 – 100 cm</td></tr>
                        <tr><td>31</td><td>80 – 83 cm</td><td>100 – 103 cm</td></tr>
                        <tr><td>32</td><td>83 – 86 cm</td><td>103 – 106 cm</td></tr>
                        <tr><td>33–34</td><td>86 – 92 cm</td><td>106 – 112 cm</td></tr>
                    </tbody>
                </table>
            <?php else: ?>
                <table>
                    <thead><tr><th>Size</th><th>Chiều cao</th><th>Cân nặng</th></tr></thead>
                    <tbody>
                        <tr><td>S</td><td>1m55 – 1m65</td><td>48 – 58 kg</td></tr>
                        <tr><td>M</td><td>1m60 – 1m72</td><td>55 – 68 kg</td></tr>
                        <tr><td>L</td><td>1m68 – 1m80</td><td>65 – 78 kg</td></tr>
                        <tr><td>XL</td><td>1m75 – 1m88</td><td>75 – 90 kg</td></tr>
                        <tr><td>XXL</td><td>1m82 – 1m92</td><td>85 – 100 kg</td></tr>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </dialog>

    <?php require PATH_VIEW . 'partials/footer.php'; ?>
</body>
</html>
