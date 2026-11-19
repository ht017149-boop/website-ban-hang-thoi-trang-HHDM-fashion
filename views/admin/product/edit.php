<?php
$escapeProduct = static fn($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$currentImageValue = (string) ($productForm['image_url'] ?? '');
$currentImageUrl = preg_match('/^https?:\/\//i', $currentImageValue)
    ? $currentImageValue
    : (strpos($currentImageValue, 'assets/uploads/') === 0
        ? BASE_URL . $currentImageValue
        : ($currentImageValue !== '' ? 'https://images.unsplash.com/' . $currentImageValue . '?auto=format&fit=crop&w=300&q=80' : ''));
?>
<span class="eyebrow">HHDM FASHION · QUẢN LÝ</span>
<h1 class="page-title">Chỉnh sửa sản phẩm</h1>
<p class="page-intro">Cập nhật thông tin sản phẩm #<?= (int) $productForm['product_id'] ?>. Thay đổi được lưu trực tiếp vào database cửa hàng.</p>

<section class="panel">
  <div class="panel-head"><h2>Chỉnh sửa sản phẩm</h2><a href="index.php?page=admin-products">← DANH SÁCH SẢN PHẨM</a></div>
  <?php if ($productErrors): ?><div class="form-errors" role="alert"><?php foreach ($productErrors as $error): ?><div><?= $escapeProduct($error) ?></div><?php endforeach; ?></div><?php endif; ?>
  <?php if (!$categories): ?><div class="form-errors" role="alert">Chưa có danh mục. Hãy tạo danh mục trước khi thêm sản phẩm.</div><?php else: ?>
  <form method="post" action="index.php?page=admin-product-edit" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= $escape($csrfToken) ?>">
    <input type="hidden" name="action" value="save_product">
    <input type="hidden" name="product_id" value="<?= (int) $productForm['product_id'] ?>">
    <div class="form-grid">
      <div class="field"><label for="product_name">TÊN SẢN PHẨM</label><input id="product_name" name="product_name" maxlength="200" value="<?= $escapeProduct($productForm['product_name']) ?>" required></div>
      <div class="field"><label for="category_id">DANH MỤC</label><select id="category_id" name="category_id" required><option value="">Chọn danh mục</option><?php foreach ($categories as $category): ?><option value="<?= (int) $category['category_id'] ?>" <?= (string) $productForm['category_id'] === (string) $category['category_id'] ? 'selected' : '' ?>><?= $escapeProduct($category['category_name']) ?><?= $category['status'] === 'inactive' ? ' (đang ẩn)' : '' ?></option><?php endforeach; ?></select></div>
      <div class="field"><label for="price">GIÁ GỐC (₫)</label><input id="price" name="price" type="number" min="0.01" max="9999999999.99" step="0.01" value="<?= $escapeProduct($productForm['price']) ?>" required><span class="hint">Nhập không dấu phân cách hàng nghìn; database lưu DECIMAL(12,2), ví dụ 590000.00.</span></div>
      <div class="field"><label for="sale_price">GIÁ KHUYẾN MÃI (₫, KHÔNG BẮT BUỘC)</label><input id="sale_price" name="sale_price" type="number" min="0.01" max="9999999999.99" step="0.01" value="<?= $escapeProduct($productForm['sale_price']) ?>"></div>
      <div class="field"><label for="status">TRẠNG THÁI</label><select id="status" name="status"><option value="active" <?= $productForm['status'] === 'active' ? 'selected' : '' ?>>Đang bán</option><option value="inactive" <?= $productForm['status'] === 'inactive' ? 'selected' : '' ?>>Tạm ẩn</option><option value="out_of_stock" <?= $productForm['status'] === 'out_of_stock' ? 'selected' : '' ?>>Hết hàng</option></select></div>
      <div class="field full"><label for="product_image">TẢI ẢNH TỪ MÁY TÍNH</label><input id="product_image" name="product_image" type="file" accept="image/jpeg,image/png,image/gif,image/webp"><span class="hint">Hỗ trợ JPEG, PNG, GIF, WebP; tối đa 5 MB. Ảnh sẽ lưu trong assets/uploads/products/.</span></div>
      <div class="field full"><label for="image_url">HOẶC URL ẢNH / MÃ ẢNH UNSPLASH</label><input id="image_url" name="image_url" maxlength="500" value="<?= $escapeProduct($productForm['image_url']) ?>" placeholder="https://... hoặc photo-..."><span class="hint">Nếu chọn ảnh từ máy, ảnh tải lên sẽ được ưu tiên. Để trống cả hai nếu muốn bỏ ảnh.</span><?php if ($currentImageUrl !== ''): ?><div style="margin-top:10px"><img src="<?= $escapeProduct($currentImageUrl) ?>" alt="Ảnh sản phẩm hiện tại" style="width:90px;height:110px;object-fit:cover;border:1px solid #e5e5df"></div><?php endif; ?></div>
      <?php require __DIR__ . '/variant-fields.php'; ?>
      <div class="field full"><label for="description">MÔ TẢ</label><textarea id="description" name="description" rows="4" maxlength="60000" style="width:100%;padding:11px;border:1px solid #dcddd5;border-radius:3px;font:12px 'DM Sans',Arial,sans-serif;resize:vertical"><?= $escapeProduct($productForm['description']) ?></textarea></div>
      <div class="field full"><div class="actions"><button class="button" type="submit">LƯU THAY ĐỔI</button><a class="button button-light" href="index.php?page=admin-products" style="text-decoration:none">HỦY</a></div></div>
    </div>
  </form>
  <?php endif; ?>
</section>
