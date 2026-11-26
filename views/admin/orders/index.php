<?php
$escapeOrder = static fn($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$orderMoney = static fn($value): string => number_format((float) $value, 0, ',', '.') . ' ₫';
$orderDate = static fn($value): string => $value ? date('d/m/Y', strtotime((string) $value)) : '—';
$orderDateTime = static fn($value): string => $value ? date('d/m/Y H:i', strtotime((string) $value)) : '—';
$orderStatus = ['pending' => 'Chờ thanh toán', 'confirmed' => 'Đã xác nhận', 'shipping' => 'Đang giao', 'completed' => 'Đã giao', 'cancelled' => 'Đã hủy'];
$orderCounts = array_count_values(array_column($orders, 'status'));
?>
<style>
.orders-intro{margin:-10px 0 20px;color:#73776f;font-size:12px}.order-tools{display:flex;gap:10px;flex-wrap:wrap;margin:14px 0}.order-tools input,.order-tools select{height:38px;min-width:190px;border:1px solid #dedfd8;background:#fff;padding:0 10px}.order-counts{display:flex;gap:8px;flex-wrap:wrap;margin:12px 0}.order-counts span{background:#f0f2ed;border-radius:20px;padding:6px 10px;font-size:10px}.order-card{background:#fff;border:1px solid #e8e9e3;border-radius:5px;padding:18px;margin:12px 0}.order-card-head{display:flex;justify-content:space-between;gap:12px;align-items:center;border-bottom:1px solid #eee;padding-bottom:12px}.order-card h2{font-size:15px;margin:0}.order-meta{color:#777;font-size:11px;line-height:1.7}.order-fields{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin:14px 0}.order-items{font-size:11px;line-height:1.8}.order-action{display:flex;gap:8px;align-items:end;flex-wrap:wrap;padding-top:12px;border-top:1px solid #eee}.order-action label{display:grid;gap:5px;font-size:10px;color:#686c62}.order-action input{height:35px;border:1px solid #dedfd8;padding:0 9px;font:11px Arial}.order-badge{padding:6px 9px;border-radius:20px;background:#f0f2ed;color:#59654d;font-size:10px;font-weight:700}.order-voucher{margin-top:10px;color:#85620d;font-size:11px}@media(max-width:600px){.order-fields{grid-template-columns:1fr}}
</style>
<span class="eyebrow">VẬN HÀNH CỬA HÀNG</span>
<h1 class="page-title"><?= $showArchivedOrders ? 'Đơn hàng đã lưu trữ' : 'Đơn hàng &amp; vận chuyển' ?></h1>
<p class="orders-intro">Theo dõi đơn hàng và vận chuyển. Đơn đã giao hoặc đã hủy có thể lưu trữ để gọn danh sách, lịch sử vẫn được giữ và có thể khôi phục.</p>
<div class="order-tools"><a class="button" href="index.php?page=admin-orders">ĐƠN ĐANG HIỂN THỊ</a><a class="button" href="index.php?page=admin-orders&amp;archived=1">ĐƠN ĐÃ LƯU TRỮ</a></div>
<div class="order-counts"><span>Chờ thanh toán: <?= (int) ($orderCounts['pending'] ?? 0) ?></span><span>Chờ xử lý: <?= (int) ($orderCounts['confirmed'] ?? 0) ?></span><span>Đang giao: <?= (int) ($orderCounts['shipping'] ?? 0) ?></span><span>Đã giao: <?= (int) ($orderCounts['completed'] ?? 0) ?></span><span>Đã hủy: <?= (int) ($orderCounts['cancelled'] ?? 0) ?></span></div>
<div class="order-tools"><input id="order-search" type="search" placeholder="Tìm mã đơn, khách, email, mã vận đơn"><select id="order-filter"><option value="all">Tất cả trạng thái</option><?php foreach ($orderStatus as $statusKey => $statusLabel): ?><option value="<?= $escapeOrder($statusKey) ?>"><?= $escapeOrder($statusLabel) ?></option><?php endforeach; ?></select><span id="order-visible-count" class="order-meta"></span></div>
<?php if (!$orders): ?><section class="panel"><div class="empty"><?= $showArchivedOrders ? 'Chưa có đơn nào được lưu trữ.' : 'Chưa có đơn hàng.' ?></div></section><?php endif; ?>
<?php foreach ($orders as $order): ?>
<?php $searchText = implode(' ', [$order['order_id'], $order['full_name'], $order['email'], $order['recipient_name'], $order['recipient_phone'], $order['tracking_number'] ?? '', implode(' ', array_column($order['items'], 'product_name'))]); ?>
<article class="order-card" data-status="<?= $escapeOrder($order['status']) ?>" data-search="<?= $escapeOrder($searchText) ?>">
  <div class="order-card-head">
    <div>
      <h2>Đơn #<?= (int) $order['order_id'] ?> · <?= $orderMoney($order['total_amount']) ?></h2>
      <div class="order-meta">
        <strong>Người đặt:</strong> <?= $escapeOrder($order['full_name']) ?> · <?= $escapeOrder($order['email']) ?><br>
        <strong>Ngày giờ đặt:</strong> <?= $escapeOrder($orderDateTime($order['order_date'])) ?>
      </div>
    </div>
    <span class="order-badge"><?= $escapeOrder($orderStatus[$order['status']] ?? $order['status']) ?></span>
  </div>
  <?php if (!empty($order['archived_at'])): ?><div class="order-meta">Đã lưu trữ lúc <?= $escapeOrder($orderDateTime($order['archived_at'])) ?></div><?php endif; ?>
  <div class="order-fields">
    <div class="order-meta">
      <strong>NGƯỜI MUA / NHẬN HÀNG</strong><br>
      <?= $escapeOrder($order['recipient_name']) ?> · <?= $escapeOrder($order['recipient_phone']) ?><br>
      <strong>THANH TOÁN</strong><br><?= ($order['payment_method'] ?? '') === 'cod' ? (!empty($order['cod_collected_at']) ? 'COD · đã thu lúc ' . $escapeOrder($orderDateTime($order['cod_collected_at'])) : 'COD · thu khi giao hàng') : (($order['payment_status'] ?? '') === 'paid' ? 'Chuyển khoản · đã thanh toán' : (($order['payment_status'] ?? '') === 'cancelled' ? 'Chuyển khoản · link đã hủy' : 'Chuyển khoản · chờ thanh toán')) ?><br>
      <strong>ĐỊA CHỈ GIAO HÀNG</strong><br><?= nl2br($escapeOrder($order['shipping_address'])) ?>
    </div>
    <div class="order-meta">
      <strong>HẸN GIAO</strong><br><?= $orderDate($order['expected_delivery_from']) ?> – <?= $orderDate($order['expected_delivery_to']) ?><br>
      <strong>VẬN CHUYỂN</strong><br><?= $escapeOrder($order['shipping_company'] ?: 'Chưa nhập hãng') ?> · <?= $escapeOrder($order['tracking_number'] ?: 'Chưa có mã vận đơn') ?>
    </div>
  </div>
  <?php if ($order['status'] === 'shipping' || $order['status'] === 'completed'): ?>
    <div class="order-meta"><strong>XÁC NHẬN BÀN GIAO HÃNG:</strong>
      <?= !empty($order['shipped_at']) ? 'Đã xác nhận lúc ' . $escapeOrder($orderDateTime($order['shipped_at'])) : 'Chưa có thời điểm xác nhận' ?>
    </div>
  <?php endif; ?>
  <?php if (($order['cancel_request_status'] ?? '') === 'requested'): ?>
    <section class="order-voucher"><strong>KHÁCH YÊU CẦU HỦY</strong><br>
      Lý do: <?= $escapeOrder($order['cancel_reason'] ?? '') ?> · Gửi lúc <?= $escapeOrder($orderDateTime($order['cancel_requested_at'] ?? null)) ?>
      <form class="order-action" method="post" action="index.php?page=admin-orders">
        <input type="hidden" name="csrf_token" value="<?= $escapeOrder($csrfToken) ?>"><input type="hidden" name="order_id" value="<?= (int) $order['order_id'] ?>"><input type="hidden" name="action" value="review_cancellation">
        <label>Ghi chú xử lý<input name="admin_note" maxlength="500" placeholder="Lý do từ chối hoặc ghi chú hoàn tiền"></label>
        <button class="button" type="submit" name="decision" value="approve">DUYỆT HỦY &amp; HOÀN TỒN</button>
        <button class="button" type="submit" name="decision" value="reject">TỪ CHỐI YÊU CẦU</button>
      </form>
    </section>
  <?php elseif (($order['cancel_request_status'] ?? '') === 'rejected'): ?>
    <div class="order-meta">Yêu cầu hủy bị từ chối: <?= $escapeOrder($order['cancel_admin_note'] ?? '') ?></div>
  <?php elseif (($order['cancel_request_status'] ?? '') === 'admin_cancelled'): ?>
    <div class="order-meta">Đơn đã được admin hủy. Lý do: <?= $escapeOrder($order['cancel_reason'] ?? '') ?></div>
  <?php endif; ?>
  <?php if (in_array($order['status'], ['pending', 'confirmed'], true) && ($order['cancel_request_status'] ?? 'none') !== 'requested'): ?>
    <form class="order-action" method="post" action="index.php?page=admin-orders"><input type="hidden" name="csrf_token" value="<?= $escapeOrder($csrfToken) ?>"><input type="hidden" name="order_id" value="<?= (int) $order['order_id'] ?>"><input type="hidden" name="action" value="cancel_order"><label>Lý do admin hủy<input name="cancel_reason" maxlength="500" placeholder="Nhập lý do hủy đơn" required></label><button class="button" type="submit">HỦY ĐƠN &amp; HOÀN TỒN</button></form>
  <?php endif; ?>
  <?php if (($order['refund_status'] ?? '') === 'pending'): ?>
    <form class="order-action" method="post" action="index.php?page=admin-orders"><input type="hidden" name="csrf_token" value="<?= $escapeOrder($csrfToken) ?>"><input type="hidden" name="order_id" value="<?= (int) $order['order_id'] ?>"><input type="hidden" name="action" value="mark_refunded"><label>Mã giao dịch / ghi chú hoàn tiền<input name="refund_note" maxlength="500" placeholder="Nhập mã giao dịch hoàn tiền" required></label><button class="button" type="submit">GHI NHẬN ĐÃ HOÀN TIỀN</button></form>
  <?php elseif (($order['refund_status'] ?? '') === 'refunded'): ?>
    <div class="order-meta">Đã ghi nhận hoàn tiền: <?= $escapeOrder($order['refund_note'] ?? '') ?></div>
  <?php endif; ?>
  <div class="order-items"><strong>SẢN PHẨM ĐÃ ĐẶT</strong><br>
    <?php foreach ($order['items'] as $item): ?>
      • <?= $escapeOrder($item['product_name']) ?> / Size <?= $escapeOrder($item['size']) ?> × <?= (int) $item['quantity'] ?> — <?= $orderMoney($item['subtotal']) ?><br>
    <?php endforeach; ?>
  </div>
  <?php if ($order['vouchers']): ?><div class="order-voucher">Voucher giao trễ đã phát: <?php foreach ($order['vouchers'] as $voucher): ?><?= $escapeOrder($voucher['code']) ?> cho <?= $escapeOrder($voucher['product_name']) ?> · <?= (int) $voucher['discount_percent'] ?>% &nbsp;<?php endforeach; ?></div><?php endif; ?>
  <?php if ($order['status'] === 'confirmed'): ?><form class="order-action" method="post" action="index.php?page=admin-orders"><input type="hidden" name="csrf_token" value="<?= $escapeOrder($csrfToken) ?>"><input type="hidden" name="order_id" value="<?= (int) $order['order_id'] ?>"><input type="hidden" name="status" value="shipping"><label>Đơn vị vận chuyển<input name="shipping_company" maxlength="100" placeholder="Ví dụ: GHN, GHTK" required></label><label>Mã vận đơn<input name="tracking_number" maxlength="100" placeholder="Mã vận đơn" required></label><button class="button" type="submit">XÁC NHẬN ĐÃ BÀN GIAO</button></form>
  <?php elseif ($order['status'] === 'shipping'): ?><form class="order-action" method="post" action="index.php?page=admin-orders"><input type="hidden" name="csrf_token" value="<?= $escapeOrder($csrfToken) ?>"><input type="hidden" name="order_id" value="<?= (int) $order['order_id'] ?>"><input type="hidden" name="status" value="completed"><button class="button" type="submit"><?= ($order['payment_method'] ?? '') === 'cod' ? 'XÁC NHẬN ĐÃ GIAO &amp; THU COD' : 'XÁC NHẬN ĐÃ GIAO' ?></button></form><?php endif; ?>
  <?php if (in_array($order['status'], ['cancelled', 'completed'], true)): ?><form class="order-action" method="post" action="index.php?page=admin-orders"><input type="hidden" name="csrf_token" value="<?= $escapeOrder($csrfToken) ?>"><input type="hidden" name="order_id" value="<?= (int) $order['order_id'] ?>"><input type="hidden" name="action" value="<?= !empty($order['archived_at']) ? 'restore_order' : 'archive_order' ?>"><button class="button" type="submit"><?= !empty($order['archived_at']) ? 'KHÔI PHỤC ĐƠN' : 'LƯU TRỮ ĐƠN' ?></button></form><?php endif; ?>
</article>
<?php endforeach; ?>
<script>
(() => { const cards = [...document.querySelectorAll('.order-card')]; const search = document.getElementById('order-search'); const filter = document.getElementById('order-filter'); const count = document.getElementById('order-visible-count'); const update = () => { const q = search.value.trim().toLocaleLowerCase('vi'); let visible = 0; cards.forEach(card => { const show = (filter.value === 'all' || card.dataset.status === filter.value) && card.dataset.search.toLocaleLowerCase('vi').includes(q); card.hidden = !show; if (show) visible++; }); count.textContent = `${visible} / ${cards.length} đơn`; }; search.addEventListener('input', update); filter.addEventListener('change', update); update(); })();
</script>
