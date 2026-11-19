(function () {
  const modal = document.getElementById('payment-modal');
  if (!modal) return;

  const dialog = modal.querySelector('.payment-dialog');
  const qr = document.getElementById('payment-qr');
  const amountLabel = document.getElementById('payment-amount');
  const itemsList = document.getElementById('payment-order-items');
  const referenceLabel = document.getElementById('payment-reference');
  const feedback = document.getElementById('payment-feedback');
  const money = (value) => new Intl.NumberFormat('vi-VN').format(value) + '₫';

  function closePayment() {
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('payment-open');
    window.setTimeout(() => { modal.hidden = true; }, 220);
  }

  function openPayment({ items = [], amount = 0 } = {}) {
    const safeItems = items.filter((item) => item && Number(item.price) > 0);
    const total = Math.round(Number(amount) || safeItems.reduce((sum, item) => sum + Number(item.price) * Number(item.quantity || 1), 0));
    if (!safeItems.length || total < 1000) return;

    const reference = 'HHDM' + Date.now().toString().slice(-8) + Math.floor(Math.random() * 90 + 10);
    itemsList.replaceChildren(...safeItems.map((item) => {
      const row = document.createElement('p');
      const quantity = Number(item.quantity || 1);
      row.textContent = `${item.name}${item.size ? ` · Size ${item.size}` : ''}${quantity > 1 ? ` × ${quantity}` : ''}`;
      return row;
    }));
    amountLabel.textContent = money(total);
    document.getElementById('payment-account').textContent = dialog.dataset.account;
    referenceLabel.textContent = reference;
    feedback.textContent = '';

    const query = new URLSearchParams({ amount: String(total), addInfo: reference });
    qr.src = `https://img.vietqr.io/image/${encodeURIComponent(dialog.dataset.bank)}-${encodeURIComponent(dialog.dataset.account)}-compact2.svg?${query.toString()}`;
    qr.alt = `VietQR BIDV, tài khoản ${dialog.dataset.account}, số tiền ${money(total)}`;
    modal.hidden = false;
    requestAnimationFrame(() => {
      modal.classList.add('is-open');
      modal.setAttribute('aria-hidden', 'false');
      document.body.classList.add('payment-open');
      modal.querySelector('.payment-close').focus();
    });
  }

  modal.addEventListener('click', (event) => {
    if (event.target.closest('[data-payment-close]')) closePayment();
    const copyButton = event.target.closest('[data-copy]');
    if (!copyButton) return;
    const value = copyButton.dataset.copy === 'account' ? dialog.dataset.account : referenceLabel.textContent;
    if (navigator.clipboard?.writeText) {
      navigator.clipboard.writeText(value).then(() => {
        feedback.textContent = 'Đã sao chép.';
      }).catch(() => {
        feedback.textContent = value;
      });
    } else {
      feedback.textContent = value;
    }
  });
  qr.addEventListener('error', () => {
    feedback.textContent = 'Chưa tải được ảnh QR. Bạn có thể chuyển khoản thủ công bằng thông tin tài khoản ở bên trái.';
  });
  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && !modal.hidden) closePayment();
  });

  window.HHDMPayment = { open: openPayment, close: closePayment };
})();
