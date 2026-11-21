<?php
$escapeComment = static fn($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$rating = max(0, min(5, (int) $comment['rating']));
?>
<span class="eyebrow">HHDM FASHION · QUẢN LÝ</span>
<h1 class="page-title">Chi tiết đánh giá</h1>
<p class="page-intro">Xem thông tin đánh giá của khách hàng và sản phẩm tương ứng.</p>

<section class="panel">
  <div class="panel-head"><h2>Đánh giá #<?= (int) $comment['comment_id'] ?></h2><a class="text-link" href="index.php?page=admin-comments">← Quay lại danh sách</a></div>
  <div class="table-wrap"><table>
    <tbody>
      <tr><th>SẢN PHẨM</th><td class="person"><?= $escapeComment($comment['product_name']) ?><div class="muted">#<?= (int) $comment['product_id'] ?></div><a class="text-link" href="index.php?page=product_detail&amp;id=<?= (int) $comment['product_id'] ?>">Xem sản phẩm tại cửa hàng →</a></td></tr>
      <tr><th>NGƯỜI ĐÁNH GIÁ</th><td class="person"><?= $escapeComment($comment['full_name']) ?><div class="muted"><?= $escapeComment($comment['email']) ?></div></td></tr>
      <tr><th>ĐIỂM ĐÁNH GIÁ</th><td style="color:#b8860b;font-size:16px"><?= str_repeat('★', $rating) . str_repeat('☆', 5 - $rating) ?> <span class="muted">(<?= $rating ?>/5)</span></td></tr>
      <tr><th>NGÀY ĐÁNH GIÁ</th><td class="muted"><?= $escapeComment(date('d/m/Y H:i', strtotime((string) $comment['created_at']))) ?></td></tr>
      <tr><th>NỘI DUNG</th><td><?= nl2br($escapeComment($comment['content'])) ?></td></tr>
    </tbody>
  </table></div>
  <div class="actions" style="margin-top:18px"><form method="post" action="index.php?page=admin-comments" data-confirm="Gỡ đánh giá này khỏi database?"><input type="hidden" name="csrf_token" value="<?= $escapeComment($csrfToken) ?>"><input type="hidden" name="action" value="delete_comment"><input type="hidden" name="comment_id" value="<?= (int) $comment['comment_id'] ?>"><button class="button button-danger" type="submit">Gỡ đánh giá</button></form></div>
</section>
