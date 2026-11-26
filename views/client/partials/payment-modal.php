<div class="payment-modal" id="payment-modal" aria-hidden="true" hidden>
  <div class="payment-backdrop" data-payment-close></div>
  <section class="payment-dialog" role="dialog" aria-modal="true" aria-labelledby="payment-title">
    <button class="payment-close" type="button" aria-label="Đóng thanh toán" data-payment-close>×</button>
    <div class="payment-copy">
      <span class="section-kicker">HHDM · THANH TOÁN AN TOÀN</span>
      <h2 id="payment-title">Thanh toán đơn hàng</h2>
      <p class="payment-lead">Nhập thông tin giao hàng. Mã QR sẽ được tạo riêng cho đơn hàng sau khi xác nhận.</p>
      <form class="checkout-details" id="checkout-details">
        <label>Người nhận<input name="recipient" maxlength="150" autocomplete="name" required></label>
        <label>Số điện thoại<input name="phone" type="tel" maxlength="20" autocomplete="tel" pattern="[0-9+(). -]{8,20}" required></label>
        <label class="checkout-address">Địa chỉ giao hàng<textarea name="address" maxlength="500" rows="2" autocomplete="street-address" required></textarea></label>
        <label class="checkout-address">Mã voucher (nếu có)<input name="voucher_code" maxlength="40" autocomplete="off" placeholder="Voucher chỉ dùng cho sản phẩm được ghi trên mã"></label>
        <button class="checkout-confirm" type="submit">TẠO ĐƠN VÀ HIỆN MÃ QR</button>
      </form>
      <div class="order-created-notice" id="order-created-notice" role="status" aria-live="polite" hidden>
        <span aria-hidden="true">✓</span><div><strong>Tạo đơn thành công</strong><small id="order-created-number"></small></div>
      </div>
      <div class="payment-order-items" id="payment-order-items"></div>
      <div class="payment-total"><span>Tổng thanh toán</span><strong id="payment-amount">0₫</strong></div>
      <dl class="payment-details" id="payment-details" hidden>
        <div><dt>Ngân hàng</dt><dd><span id="payment-bank">—</span></dd></div>
        <div><dt>Số tài khoản</dt><dd><span id="payment-account">—</span><button type="button" class="copy-payment-value" data-copy="account">Sao chép</button></dd></div>
        <div><dt>Nội dung chuyển khoản</dt><dd><span id="payment-reference"></span><button type="button" class="copy-payment-value" data-copy="reference">Sao chép</button></dd></div>
      </dl>
      <p class="payment-note" id="payment-note" hidden>Đơn hàng sẽ được xác nhận tự động sau khi payOS ghi nhận giao dịch thành công.</p>
      <p class="payment-feedback" id="payment-feedback" aria-live="polite"></p>
    </div>
    <div class="payment-qr-wrap" id="payment-qr-wrap" hidden><img class="payment-qr" id="payment-qr" alt="Mã QR thanh toán payOS" loading="eager"><span>Quét mã bằng ứng dụng ngân hàng để thanh toán trực tiếp</span></div>
  </section>
</div>
<script src="assets/js/payment.js?v=20261126-qr-only" defer></script>
