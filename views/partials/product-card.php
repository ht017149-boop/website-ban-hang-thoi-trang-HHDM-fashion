<article class="product-card">
    <a class="product-card__link" href="?action=product&amp;id=<?= rawurlencode($product['slug']) ?>" aria-label="Xem <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>">
        <div class="product-image">
            <img src="<?= htmlspecialchars($product['image'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>" loading="lazy">
            <?php if (!empty($product['badge'])): ?>
                <span class="badge"><?= htmlspecialchars($product['badge'], ENT_QUOTES, 'UTF-8') ?></span>
            <?php endif; ?>
        </div>
        <div class="product-info">
            <span class="product-category"><?= htmlspecialchars($product['category'], ENT_QUOTES, 'UTF-8') ?></span>
            <h3><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></h3>
            <div class="price-row">
                <span class="price"><?= number_format($product['price'], 0, ',', '.') ?>₫</span>
                <?php if (!empty($product['old_price'])): ?>
                    <span class="old-price"><?= number_format($product['old_price'], 0, ',', '.') ?>₫</span>
                <?php endif; ?>
            </div>
        </div>
    </a>
    <div class="product-card__actions">
        <div class="swatches" aria-label="Màu sắc">
            <?php foreach ($product['colors'] as $color): ?>
                <i class="swatch" style="--swatch-color: <?= htmlspecialchars($color, ENT_QUOTES, 'UTF-8') ?>"></i>
            <?php endforeach; ?>
        </div>
        <button class="quick-add" type="button" data-product-name="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>">
            Thêm nhanh <span aria-hidden="true">+</span>
        </button>
    </div>
</article>
