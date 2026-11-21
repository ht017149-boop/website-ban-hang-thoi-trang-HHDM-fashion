<?php
$escapeComment = static fn($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>
<span class="eyebrow">HHDM FASHION · QUẢN LÝ</span>
<h1 class="page-title">Đánh giá</h1>
<p class="page-intro">Xem và gỡ các đánh giá của khách hàng. Chọn "Xem" để biết đánh giá thuộc sản phẩm nào.</p>

<section class="panel">
  <div class="panel-head"><h2>Danh sách đánh giá <span class="muted">(<?= count($commentList) ?>)</span></h2></div>
  <?php if (!$commentList): ?>
    <div class="empty">Chưa có đánh giá nào trong database.</div>
  <?php else: ?>
    <div class="table-wrap"><table>
      <thead><tr><th>NGƯỜI ĐÁNH GIÁ</th><th>SẢN PHẨM</th><th>ĐIỂM</th><th>NỘI DUNG</th><th>NGÀY</th><th>THAO TÁC</th></tr></thead>
      <tbody><?php foreach ($commentList as $comment): ?>
        <?php $rating = max(0, min(5, (int) $comment['rating'])); ?>
        <tr>
          <td class="person"><?= $escapeComment($comment['full_name']) ?></td>
          <td><?= $escapeComment($comment['product_name']) ?><div class="muted">#<?= (int) $comment['product_id'] ?></div></td>
          <td class="muted" style="color:#b8860b"><?= str_repeat('★', $rating) . str_repeat('☆', 5 - $rating) ?></td>
          <td class="muted"><?= $escapeComment(mb_substr((string) $comment['content'], 0, 80) . (mb_strlen((string) $comment['content']) > 80 ? '…' : '')) ?></td>
          <td class="muted"><?= $escapeComment(date('d/m/Y H:i', strtotime((string) $comment['created_at']))) ?></td>
          <td><div class="actions"><a class="button button-light" href="index.php?page=admin-comment-show&amp;id=<?= (int) $comment['comment_id'] ?>" style="text-decoration:none">Xem</a><form method="post" action="index.php?page=admin-comments" data-confirm="Gỡ đánh giá này khỏi database?"><input type="hidden" name="csrf_token" value="<?= $escapeComment($csrfToken) ?>"><input type="hidden" name="action" value="delete_comment"><input type="hidden" name="comment_id" value="<?= (int) $comment['comment_id'] ?>"><button class="button button-danger" type="submit">Gỡ</button></form></div></td>
        </tr>
      <?php endforeach; ?></tbody>
    </table></div>
  <?php endif; ?>
</section>
