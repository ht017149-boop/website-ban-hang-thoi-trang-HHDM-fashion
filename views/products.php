<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f8f6f2">
    <meta name="description" content="Khám phá bộ sưu tập thời trang nam HHDM Studio.">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/main.css">
    <script src="assets/js/main.js" defer></script>
</head>
<body class="catalog-page">
    <?php require PATH_VIEW . 'partials/header.php'; ?>

    <main>
        <nav class="breadcrumbs" aria-label="Đường dẫn">
            <a href="?action=/">Trang chủ</a>
            <span aria-hidden="true">/</span>
            <span>Tất cả sản phẩm</span>
        </nav>

        <section class="catalog-intro">
            <span class="eyebrow">HHDM STUDIO · MENSWEAR</span>
            <h1>Tất cả sản phẩm</h1>
            <p>Những thiết kế nam hiện đại, dễ phối và sẵn sàng đồng hành cùng bạn mỗi ngày.</p>
        </section>

        <div class="catalog-layout">
            <aside class="catalog-sidebar" aria-label="Lọc sản phẩm">
                <h2>Danh mục</h2>
                <a class="<?= $selectedCategory === '' ? 'is-active' : '' ?>" href="?action=products">Tất cả sản phẩm</a>
                <?php foreach ($categories as $category): ?>
                    <a class="<?= $selectedCategory === $category ? 'is-active' : '' ?>" href="?action=products&amp;category=<?= rawurlencode($category) ?>">
                        <?= htmlspecialchars($category, ENT_QUOTES, 'UTF-8') ?>
                    </a>
                <?php endforeach; ?>
                <h2 class="catalog-sidebar__subheading">Bộ sưu tập</h2>
                <a class="<?= $selectedCollection === 'Đồ công sở' ? 'is-active' : '' ?>" href="?action=products&amp;collection=Đồ%20công%20sở">Đồ công sở</a>
                <a class="<?= $selectedCollection === 'Streetwear' ? 'is-active' : '' ?>" href="?action=products&amp;collection=Streetwear">Streetwear</a>
                <div class="sidebar-note">
                    <b>Cần tư vấn chọn size?</b>
                    <span>Liên hệ đội ngũ HHDM để được hỗ trợ trước khi đặt hàng.</span>
                </div>
            </aside>

            <section class="catalog-results" aria-label="Danh sách sản phẩm">
                <form class="catalog-toolbar" method="get" action="">
                    <input type="hidden" name="action" value="products">
                    <?php if ($selectedCategory !== ''): ?>
                        <input type="hidden" name="category" value="<?= htmlspecialchars($selectedCategory, ENT_QUOTES, 'UTF-8') ?>">
                    <?php endif; ?>
                    <?php if ($selectedCollection !== ''): ?>
                        <input type="hidden" name="collection" value="<?= htmlspecialchars($selectedCollection, ENT_QUOTES, 'UTF-8') ?>">
                    <?php endif; ?>
                    <label class="catalog-search">
                        <span class="visually-hidden">Tìm sản phẩm</span>
                        <input type="search" name="q" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>" placeholder="Tìm tên sản phẩm...">
                        <button type="submit" aria-label="Tìm kiếm">⌕</button>
                    </label>
                    <label class="catalog-sort">
                        <span>Sắp xếp</span>
                        <select name="sort" data-auto-submit>
                            <option value="featured" <?= $sort === 'featured' ? 'selected' : '' ?>>Nổi bật</option>
                            <option value="price-asc" <?= $sort === 'price-asc' ? 'selected' : '' ?>>Giá tăng dần</option>
                            <option value="price-desc" <?= $sort === 'price-desc' ? 'selected' : '' ?>>Giá giảm dần</option>
                        </select>
                    </label>
                </form>

                <div class="catalog-summary">
                    <span><?= count($products) ?> sản phẩm</span>
                    <?php if ($selectedCategory !== ''): ?>
                        <span>· <?= htmlspecialchars($selectedCategory, ENT_QUOTES, 'UTF-8') ?></span>
                    <?php elseif ($selectedCollection !== ''): ?>
                        <span>· <?= htmlspecialchars($selectedCollection, ENT_QUOTES, 'UTF-8') ?></span>
                    <?php endif; ?>
                </div>

                <?php if ($products === []): ?>
                    <div class="empty-state">
                        <h2>Chưa tìm thấy sản phẩm phù hợp</h2>
                        <p>Hãy thử từ khóa khác hoặc xem toàn bộ bộ sưu tập.</p>
                        <a class="button" href="?action=products">Xem tất cả sản phẩm</a>
                    </div>
                <?php else: ?>
                    <div class="products catalog-products">
                        <?php foreach ($products as $product): ?>
                            <?php require PATH_VIEW . 'partials/product-card.php'; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </main>

    <?php require PATH_VIEW . 'partials/footer.php'; ?>
</body>
</html>
