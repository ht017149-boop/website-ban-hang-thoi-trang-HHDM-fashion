<div class="payment-modal" id="payment-modal" aria-hidden="true" hidden>
  <div class="payment-backdrop" data-payment-close></div>
  <section class="payment-dialog" role="dialog" aria-modal="true" aria-labelledby="payment-title" data-bank="<?= $escape(
      PAYMENT_BANK_ID,
  ) ?>" data-account="<?= $escape(PAYMENT_ACCOUNT_NO) ?>">
    <button class="payment-close" type="button" aria-label="Đóng thanh toán" data-payment-close>×</button>
    <div class="payment-copy">
      <span class="section-kicker">HHDM · THANH TOÁN AN TOÀN</span>
      <h2 id="payment-title">Quét mã để chuyển khoản</h2>
      <p class="payment-lead">Dùng ứng dụng ngân hàng để quét VietQR. Số tiền và nội dung chuyển khoản đã được điền sẵn.</p>
      <div class="payment-order-items" id="payment-order-items"></div>
      <div class="payment-total"><span>Tổng thanh toán</span><strong id="payment-amount">0₫</strong></div>
      <dl class="payment-details">
        <div><dt>Ngân hàng</dt><dd>BIDV</dd></div>
        <div><dt>Số tài khoản</dt><dd><span id="payment-account">8855252740</span><button type="button" class="copy-payment-value" data-copy="account">Sao chép</button></dd></div>
        <div><dt>Nội dung chuyển khoản</dt><dd><span id="payment-reference"></span><button type="button" class="copy-payment-value" data-copy="reference">Sao chép</button></dd></div>
      </dl>
      <p class="payment-note">Đơn hàng được xác nhận sau khi HHDM đối chiếu giao dịch BIDV.</p>
      <p class="payment-feedback" id="payment-feedback" aria-live="polite"></p>
    </div>
    <div class="payment-qr-wrap"><img class="payment-qr" id="payment-qr" alt="Mã VietQR thanh toán qua BIDV" loading="eager"><span>VietQR · BIDV</span></div>
  </section>
</div>
<script src="assets/js/payment.js?v=20261119" ></script>
