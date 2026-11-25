<?php
$formatMoney = static fn($value): string => number_format((float) $value, 0, ",", ".") . "&#8363;"; ?>
<span class="eyebrow">HHDM FASHION · QUẢN TRỊ</span>
<h1 class="page-title">Dashboard</h1>
<p class="page-intro">Tổng quan nhanh về cửa hàng và tài khoản của bạn.</p>

<section class="revenue-overview" aria-labelledby="revenue-title">
  <div class="revenue-overview-copy">
    <span>DOANH THU C&#7916;A H&#192;NG</span>
    <h2 id="revenue-title">T&#7893;ng doanh thu</h2>
    <p>Doanh thu &#273;&#432;&#7907;c t&#237;nh t&#7915; c&#225;c &#273;&#417;n h&#224;ng &#273;&#227; ho&#224;n t&#7845;t.</p>
  </div>
  <div class="revenue-overview-data">
    <strong class="revenue-total"><?= $formatMoney($stats["completed_revenue"]) ?></strong>
    <div class="revenue-metrics">
      <div><span>Th&#225;ng n&#224;y</span><strong><?= $formatMoney($stats["monthly_revenue"]) ?></strong></div>
      <div><span>&#272;&#417;n ho&#224;n t&#7845;t</span><strong><?= number_format((int) $stats["completed_orders"]) ?></strong></div>
    </div>
  </div>
</section>

<section class="cards" aria-label="Thống kê cửa hàng">
  <article class="card"><div class="card-label">TỔNG TÀI KHOẢN</div><div class="card-value"><?= number_format(
      (int) $stats["total_users"],
  ) ?></div><div class="card-note"><?= (int) $stats["total_admins"] ?> quản trị viên · <?= (int) $stats[
     "total_customers"
 ] ?> khách hàng</div></article>
  <article class="card"><div class="card-label">SẢN PHẨM</div><div class="card-value"><?= number_format(
      (int) $stats["total_products"],
  ) ?></div><div class="card-note"><?= (int) $stats["total_categories"] ?> danh mục</div></article>
  <article class="card"><div class="card-label">ĐƠN HÀNG</div><div class="card-value"><?= number_format(
      (int) $stats["total_orders"],
  ) ?></div><div class="card-note">Theo dữ liệu hiện có</div></article>
  <article class="card"><div class="card-label">&#272;&#416;N HO&#192;N T&#7844;T</div><div class="card-value"><?= number_format((int) $stats["completed_orders"]) ?></div><div class="card-note">&#272;&#227; ghi nh&#7853;n doanh thu</div></article>
</section>

<section class="panel">
  <div class="panel-head"><h2>Tài khoản mới</h2><a href="index.php?page=admin-accounts">QUẢN LÝ TÀI KHOẢN →</a></div>
  <?php if (!$latestAccounts): ?>
    <div class="empty">Chưa có tài khoản nào.</div>
  <?php else: ?>
    <div class="table-wrap"><table>
      <thead><tr><th>HỌ TÊN</th><th>EMAIL</th><th>VAI TRÒ</th><th>NGÀY TẠO</th></tr></thead>
      <tbody><?php foreach ($latestAccounts as $account): ?>
        <tr><td class="person"><?= $escape($account["full_name"]) ?></td><td class="muted"><?= $escape(
    $account["email"],
) ?></td><td><span class="badge <?= $account["role"] === "admin" ? "badge-admin" : "badge-customer" ?>"><?= $account[
    "role"
] === "admin"
    ? "Quản trị viên"
    : "Khách hàng" ?></span></td><td class="muted"><?= $escape(
    date("d/m/Y", strtotime($account["created_at"])),
) ?></td></tr>
      <?php endforeach; ?></tbody>
    </table></div>
  <?php endif; ?>
</section>
