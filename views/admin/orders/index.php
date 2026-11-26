<?php
$escapeOrder = static fn($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$orderMoney = static fn($value): string => number_format((float) $value, 0, ',', '.') . '₫';
$orderDate = static fn($value): string => $value ? date('d/m/Y', strtotime((string) $value)) : '—';
$orderStatus = ['pending' => 'Chờ thanh toán', 'confirmed' => 'Đã xác nhận', 'shipping' => 'Đang giao', 'completed' => 'Đã giao', 'cancelled' => 'Đã hủy'];
?>
<style>
.orders-intro{margin:-10px 0 20px;color:#73776f;font-size:12px}.order-card{background:#fff;border:1px solid #e8e9e3;border-radius:5px;padding:18px;margin:12px 0}.order-card-head{display:flex;justify-content:space-between;gap:12px;align-items:center;border-bottom:1px solid #eee;padding-bottom:12px}.order-card h2{font-size:15px;margin:0}.order-meta{color:#777;font-size:11px;line-height:1.7}.order-fields{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin:14px 0}.order-items{font-size:11px;line-height:1.8}.order-action{display:flex;gap:8px;align-items:end;flex-wrap:wrap;padding-top:12px;border-top:1px solid #eee}.order-action label{display:grid;gap:5px;font-size:10px;color:#686c62}.order-action input{height:35px;border:1px solid #dedfd8;padding:0 9px;font:11px Arial}.order-badge{padding:6px 9px;border-radius:20px;background:#f0f2ed;color:#59654d;font-size:10px;font-weight:700}.order-voucher{margin-top:10px;color:#85620d;font-size:11px}@media(max-width:600px){.order-fields{grid-template-columns:1fr}}
</style>
<span class="eyebrow">VẬN HÀNH CỬA HÀNG</span>
<h1 class="page-title">Đơn hàng & vận chuyển</h1>
<p class="orders-intro">Cập nhật mốc giao hàng để khách theo dõi. Voucher trễ hẹn 15% được phát tự động theo từng sản phẩm khi giao sau ngày hẹn.</p>
<?php if (!$orders): ?><section class="panel"><div class="empty">Chưa có đơn hàng.</div></section><?php endif; ?>
<?php foreach ($orders as $order): ?>
<article class="order-card">
  <div class="order-card-head"><div><h2>Đơn #<?= (int) $order['order_id'] ?> · <?= $orderMoney($order['total_amount']) ?></h2><div class="order-meta"><?= $escape($order['full_name']) ?> · <?= $escape($order['email']) ?> · <?= $escape(date('d/m/Y H:i', strtotime($order['order_date']))) ?></div></div><span class="order-badge"><?= $escape($orderStatus[$order['status']] ?? $order['status']) ?></span></div>
  <div class="order-fields"><div class="order-meta"><strong>NGƯỜI NHẬN</strong><br><?= $escape($order['recipient_name']) ?> · <?= $escape($order['recipient_phone']) ?><br><strong>ĐỊA CHỈ</strong><br><?= nl2br($escape($order['shipping_address'])) ?></div><div class="order-meta"><strong>HẸN GIAO</strong><br><?= $orderDate($order['expected_delivery_from']) ?> – <?= $orderDate($order['expected_delivery_to']) ?><br><strong>VẬN CHUYỂN</strong><br><?= $escape($order['shipping_company'] ?: 'Chưa nhập hãng') ?> · <?= $escape($order['tracking_number'] ?: 'Chưa có mã vận đơn') ?></div></div>
  <div class="order-items"><?php foreach ($order['items'] as $item): ?>• <?= $escape($item['product_name']) ?> / <?= $escape($item['size']) ?> × <?= (int) $item['quantity'] ?> — <?= $orderMoney($item['subtotal']) ?><br><?php endforeach; ?></div>
  <?php if ($order['vouchers']): ?><div class="order-voucher">Voucher giao trễ đã phát: <?php foreach ($order['vouchers'] as $voucher): ?><?= $escape($voucher['code']) ?> cho <?= $escape($voucher['product_name']) ?> · <?= (int) $voucher['discount_percent'] ?>% &nbsp;<?php endforeach; ?></div><?php endif; ?>
  <?php if ($order['status'] === 'confirmed'): ?><form class="order-action" method="post" action="index.php?page=admin-orders"><input type="hidden" name="csrf_token" value="<?= $escapeOrder($csrfToken) ?>"><input type="hidden" name="order_id" value="<?= (int) $order['order_id'] ?>"><input type="hidden" name="status" value="shipping"><label>Đơn vị vận chuyển<input name="shipping_company" maxlength="100" placeholder="Ví dụ: GHN, GHTK"></label><label>Mã vận đơn<input name="tracking_number" maxlength="100" placeholder="Mã vận đơn"></label><button class="button" type="submit">BÀN GIAO ĐƠN</button></form>
  <?php elseif ($order['status'] === 'shipping'): ?><form class="order-action" method="post" action="index.php?page=admin-orders"><input type="hidden" name="csrf_token" value="<?= $escapeOrder($csrfToken) ?>"><input type="hidden" name="order_id" value="<?= (int) $order['order_id'] ?>"><input type="hidden" name="status" value="completed"><button class="button" type="submit">XÁC NHẬN ĐÃ GIAO</button></form><?php endif; ?>
</article>
<?php endforeach; ?>
