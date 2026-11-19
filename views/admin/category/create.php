<?php
$escapeCategory = static fn($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>
<span class="eyebrow">HHDM FASHION · QUẢN LÝ</span>
<h1 class="page-title">Thêm danh mục</h1>
<p class="page-intro">Tạo danh mục sản phẩm mới. Thay đổi được lưu trực tiếp vào database cửa hàng.</p>

<section class="panel">
  <div class="panel-head"><h2>Thêm danh mục mới</h2><a href="index.php?page=admin-categories">← DANH SÁCH DANH MỤC</a></div>
  <?php if ($categoryErrors): ?><div class="form-errors" role="alert"><?php foreach ($categoryErrors as $error): ?><div><?= $escapeCategory($error) ?></div><?php endforeach; ?></div><?php endif; ?>
  <form method="post" action="index.php?page=admin-category-create">
    <input type="hidden" name="csrf_token" value="<?= $escape($csrfToken) ?>">
    <input type="hidden" name="action" value="save_category">
    <input type="hidden" name="category_id" value="0">
    <div class="form-grid">
      <div class="field"><label for="category_name">TÊN DANH MỤC</label><input id="category_name" name="category_name" maxlength="150" value="<?= $escapeCategory($categoryForm['category_name']) ?>" required></div>
      <div class="field"><label for="status">TRẠNG THÁI</label><select id="status" name="status"><option value="active" <?= $categoryForm['status'] === 'active' ? 'selected' : '' ?>>Đang hiển thị</option><option value="inactive" <?= $categoryForm['status'] === 'inactive' ? 'selected' : '' ?>>Tạm ẩn</option></select></div>
      <div class="field full"><label for="slug">ĐƯỜNG DẪN (SLUG)</label><input id="slug" name="slug" maxlength="180" value="<?= $escapeCategory($categoryForm['slug']) ?>" placeholder="Tự tạo từ tên danh mục"><span class="hint">Slug tự cập nhật theo tên danh mục; bạn vẫn có thể chỉnh sửa. Slug phải duy nhất trong toàn cửa hàng.</span></div>
      <div class="field full"><label for="image_url">URL ẢNH DANH MỤC (KHÔNG BẮT BUỘC)</label><input id="image_url" name="image_url" maxlength="500" value="<?= $escapeCategory($categoryForm['image_url']) ?>" placeholder="https://..."></div>
      <div class="field full"><label for="description">MÔ TẢ</label><textarea id="description" name="description" rows="4" maxlength="60000" style="width:100%;padding:11px;border:1px solid #dcddd5;border-radius:3px;font:12px 'DM Sans',Arial,sans-serif;resize:vertical"><?= $escapeCategory($categoryForm['description']) ?></textarea></div>
      <div class="field full"><div class="actions"><button class="button" type="submit">THÊM DANH MỤC</button><a class="button button-light" href="index.php?page=admin-categories" style="text-decoration:none">HỦY</a></div></div>
    </div>
  </form>
</section>
<script>
  (function () {
    const categoryName = document.getElementById('category_name');
    const slug = document.getElementById('slug');
    let manuallyEdited = slug.value.trim() !== '' && slug.value !== slugifyCategoryName(categoryName.value);

    function slugifyCategoryName(value) {
      return value.trim().toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/đ/g, 'd')
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
    }

    categoryName.addEventListener('input', function () {
      if (!manuallyEdited) {
        slug.value = slugifyCategoryName(categoryName.value);
      }
    });
    slug.addEventListener('input', function () {
      manuallyEdited = slug.value.trim() !== '' && slug.value !== slugifyCategoryName(categoryName.value);
    });
  })();
</script>
