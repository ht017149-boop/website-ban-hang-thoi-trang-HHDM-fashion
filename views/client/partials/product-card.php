<?php
$imageValue = (string) ($product['image'] ?? '');
$imageUrl = preg_match('/^https?:\/\//i', $imageValue)
  ? $imageValue
  : (strpos($imageValue, 'assets/uploads/') === 0
    ? BASE_URL . $imageValue
    : ($imageValue !== '' ? 'https://images.unsplash.com/' . $imageValue . '?auto=format&fit=crop&w=780&q=85' : ''));
$variantsBySize = [];
foreach ($product['variants'] ?? [] as $variant) {
  $variantsBySize[$variant['size']] = $variant;
}
?>
<article class="product-card" data-search="<?= $escape($product['name'] . ' ' . $product['code'] . ' ' . $product['tag']) ?>">
  <div class="product-image-wrap">
    <?php if ($imageUrl !== ''): ?><img loading="lazy" src="<?= $escape($imageUrl) ?>" alt="<?= $escape($product['alt']) ?>"><?php else: ?><div class="product-image-placeholder" role="img" aria-label="Sản phẩm chưa có ảnh"></div><?php endif; ?>
    <span class="product-tag <?= $product['old'] ? 'tag-sale' : '' ?>"><?= $escape($product['tag']) ?></span>
    <button class="favorite-button" type="button" aria-label="Thêm <?= $escape($product['name']) ?> vào yêu thích">♡</button>
    <div class="quick-panel"><span>CHỌN SIZE CỦA BẠN</span><?php if ($product['sizes']): ?><div class="size-options"><?php foreach ($product['sizes'] as $size): $variant = $variantsBySize[$size]; ?><button class="size-option" type="button" data-size="<?= $escape($size) ?>" data-stock="<?= (int) $variant['stock_quantity'] ?>" data-additional-price="<?= $escape(number_format((float) $variant['additional_price'], 2, '.', '')) ?>"><?= $escape($size) ?></button><?php endforeach; ?></div><button class="add-button" type="button" data-add data-name="<?= $escape($product['name']) ?>" data-code="<?= $escape($product['code']) ?>" data-base-price="<?= $escape(number_format((float) $product['price'], 2, '.', '')) ?>" data-base-old="<?= $escape($product['old'] === null ? '' : number_format((float) $product['old'], 2, '.', '')) ?>" data-price="<?= $escape(number_format((float) $product['price'], 2, '.', '')) ?>" data-image="<?= $escape($imageUrl) ?>">THÊM VÀO GIỎ <span>+</span></button><?php else: ?><span class="no-size">Sản phẩm hiện đã hết size.</span><?php endif; ?></div>
  </div>
  <div class="product-info"><div class="product-code">HHDM / <?= $escape($product['code']) ?></div><h3><?= $escape($product['name']) ?></h3><div class="product-price"><strong data-current-price><?= number_format((float) $product['price'], 0, ',', '.') ?>₫</strong><?php if ($product['old']): ?><del data-old-price><?= number_format((float) $product['old'], 0, ',', '.') ?>₫</del><span class="discount" data-discount>-<?= (int) round((1 - $product['price'] / $product['old']) * 100) ?>%</span><?php endif; ?></div><?php if ($product['colors']): ?><div class="product-colors">Màu: <?= $escape(implode(', ', $product['colors'])) ?></div><?php endif; ?><a class="detail-button" href="index.php?page=product_detail&amp;id=<?= (int) $product['product_id'] ?>">XEM CHI TIẾT <span>→</span></a></div>
</article>
