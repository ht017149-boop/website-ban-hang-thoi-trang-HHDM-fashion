<?php
$formatMoney = static fn($value): string => number_format((float) $value, 0, ',', '.') . '₫';
?>
<span class="eyebrow">HHDM FASHION · QUẢN TRỊ</span>
<h1 class="page-title">Dashboard</h1>
<p class="page-intro">Tổng quan nhanh về cửa hàng và tài khoản của bạn.</p>

<section class="cards" aria-label="Thống kê cửa hàng">
  <article class="card"><div class="card-label">TỔNG TÀI KHOẢN</div><div class="card-value"><?= number_format((int) $stats['total_users']) ?></div><div class="card-note"><?= (int) $stats['total_admins'] ?> quản trị viên · <?= (int) $stats['total_customers'] ?> khách hàng</div></article>
  <article class="card"><div class="card-label">SẢN PHẨM</div><div class="card-value"><?= number_format((int) $stats['total_products']) ?></div><div class="card-note"><?= (int) $stats['total_categories'] ?> danh mục</div></article>
  <article class="card"><div class="card-label">ĐƠN HÀNG</div><div class="card-value"><?= number_format((int) $stats['total_orders']) ?></div><div class="card-note">Theo dữ liệu hiện có</div></article>
  <article class="card"><div class="card-label">DOANH THU HOÀN TẤT</div><div class="card-value revenue"><?= $formatMoney($stats['completed_revenue']) ?></div><div class="card-note">Đơn hàng có trạng thái hoàn tất</div></article>
</section>

<section class="panel">
  <div class="panel-head"><h2>Tài khoản mới</h2><a href="index.php?page=admin-accounts">QUẢN LÝ TÀI KHOẢN →</a></div>
  <?php if (!$latestAccounts): ?>
    <div class="empty">Chưa có tài khoản nào.</div>
  <?php else: ?>
    <div class="table-wrap"><table>
      <thead><tr><th>HỌ TÊN</th><th>EMAIL</th><th>VAI TRÒ</th><th>NGÀY TẠO</th></tr></thead>
      <tbody><?php foreach ($latestAccounts as $account): ?>
        <tr><td class="person"><?= $escape($account['full_name']) ?></td><td class="muted"><?= $escape($account['email']) ?></td><td><span class="badge <?= $account['role'] === 'admin' ? 'badge-admin' : 'badge-customer' ?>"><?= $account['role'] === 'admin' ? 'Quản trị viên' : 'Khách hàng' ?></span></td><td class="muted"><?= $escape(date('d/m/Y', strtotime($account['created_at']))) ?></td></tr>
      <?php endforeach; ?></tbody>
    </table></div>
  <?php endif; ?>
</section>