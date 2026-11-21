<?php
$escapeProduct = static fn($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$statusLabels = [
    'active' => 'Đang bán',
    'inactive' => 'Tạm ẩn',
    'out_of_stock' => 'Hết hàng',
];
?>
<span class="eyebrow">HHDM FASHION · QUẢN LÝ</span>
<h1 class="page-title">Sản phẩm</h1>
<p class="page-intro">Thêm, chỉnh sửa và quản lý sản phẩm. Thay đổi được lưu trực tiếp vào database cửa hàng.</p>

<section class="panel">
  <div class="panel-head"><h2>Danh sách sản phẩm <span class="muted">(<?= count($productList) ?>)</span></h2></div>
  <div class="actions" style="margin-bottom:15px"><a class="button" href="index.php?page=admin-product-create" style="text-decoration:none">＋ THÊM SẢN PHẨM</a></div>
  <?php if (!$productList): ?>
    <div class="empty">Chưa có sản phẩm nào trong database.</div>
  <?php else: ?>
    <div class="table-wrap"><table>
      <thead><tr><th>SẢN PHẨM</th><th>DANH MỤC</th><th>GIÁ</th><th>TỒN KHO</th><th>SIZE</th><th>TRẠNG THÁI</th><th>THAO TÁC</th></tr></thead>
      <tbody><?php foreach ($productList as $product): ?>
        <tr>
          <td class="person"><?= $escapeProduct($product['product_name']) ?><div class="muted">#<?= (int) $product['product_id'] ?></div></td>
          <td class="muted"><?= $escapeProduct($product['category_name']) ?></td>
          <td><?php if ($product['sale_price'] !== null): ?><div class="person"><?= number_format((float) $product['sale_price'], 2, ',', '.') ?>₫</div><div class="muted"><s><?= number_format((float) $product['price'], 2, ',', '.') ?>₫</s></div><?php else: ?><span class="person"><?= number_format((float) $product['price'], 2, ',', '.') ?>₫</span><?php endif; ?></td>
          <td><?= number_format((int) $product['stock_quantity']) ?></td>
          <td class="muted"><?= $escapeProduct($product['sizes'] ?: '—') ?></td>
          <td><span class="badge <?= $product['status'] === 'active' ? 'badge-admin' : 'badge-customer' ?>"><?= $escapeProduct($statusLabels[$product['status']] ?? $product['status']) ?></span></td>
          <td><div class="actions"><a class="button button-light" href="index.php?page=admin-product-edit&amp;id=<?= (int) $product['product_id'] ?>" style="text-decoration:none">Sửa</a><form method="post" action="index.php?page=admin-products" data-confirm="Xóa sản phẩm này khỏi database?"><input type="hidden" name="csrf_token" value="<?= $escape($csrfToken) ?>"><input type="hidden" name="action" value="delete_product"><input type="hidden" name="product_id" value="<?= (int) $product['product_id'] ?>"><button class="button button-danger" type="submit">Xóa</button></form></div></td>
        </tr>
      <?php endforeach; ?></tbody>
    </table></div>
  <?php endif; ?>
</section>
