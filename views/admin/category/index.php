<?php
$escapeCategory = static fn($value): string => htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
$statusLabels = [
    "active" => "Đang hiển thị",
    "inactive" => "Tạm ẩn",
];
?>
<span class="eyebrow">HHDM FASHION · QUẢN LÝ</span>
<h1 class="page-title">Danh mục</h1>
<p class="page-intro">Thêm, chỉnh sửa và quản lý danh mục sản phẩm. Thay đổi được lưu trực tiếp vào database cửa hàng.</p>

<section class="panel">
  <div class="panel-head"><h2>Danh sách danh mục <span class="muted">(<?= count($categoryList) ?>)</span></h2></div>
  <div class="actions" style="margin-bottom:15px"><a class="button" href="index.php?page=admin-category-create" style="text-decoration:none">＋ THÊM DANH MỤC</a></div>
  <?php if (!$categoryList): ?>
    <div class="empty">Chưa có danh mục nào trong database.</div>
  <?php else: ?>
    <div class="table-wrap"><table>
      <thead><tr><th>DANH MỤC</th><th>SLUG</th><th>MÔ TẢ</th><th>SẢN PHẨM</th><th>TRẠNG THÁI</th><th>THAO TÁC</th></tr></thead>
      <tbody><?php foreach ($categoryList as $category): ?>
        <tr>
          <td class="person"><?= $escapeCategory($category["category_name"]) ?><div class="muted">#<?= (int) $category[
    "category_id"
] ?></div></td>
          <td class="muted"><?= $escapeCategory($category["slug"]) ?></td>
          <td class="muted"><?= $escapeCategory(
              $category["description"] !== null
                  ? mb_substr((string) $category["description"], 0, 80) .
                      (mb_strlen((string) $category["description"]) > 80 ? "…" : "")
                  : "—",
          ) ?></td>
          <td><?= number_format((int) $category["product_count"]) ?></td>
          <td><span class="badge <?= $category["status"] === "active"
              ? "badge-admin"
              : "badge-customer" ?>"><?= $escapeCategory(
    $statusLabels[$category["status"]] ?? $category["status"],
) ?></span></td>
          <td><div class="actions"><a class="button button-light" href="index.php?page=admin-category-edit&amp;id=<?= (int) $category[
              "category_id"
          ] ?>" style="text-decoration:none">Sửa</a><form method="post" action="index.php?page=admin-categories" data-confirm="Xóa danh mục này khỏi database?"><input type="hidden" name="csrf_token" value="<?= $escape(
    $csrfToken,
) ?>"><input type="hidden" name="action" value="delete_category"><input type="hidden" name="category_id" value="<?= (int) $category[
    "category_id"
] ?>"><button class="button button-danger" type="submit">Xóa</button></form></div></td>
        </tr>
      <?php endforeach; ?></tbody>
    </table></div>
  <?php endif; ?>
</section>
