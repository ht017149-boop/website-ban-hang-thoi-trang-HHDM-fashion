<div class="field full">
  <div class="panel-head"><h2>Quản lý kích cỡ & tồn kho</h2><button class="button button-light" id="add-variant" type="button">＋ THÊM KÍCH CỠ</button></div>
  <p class="hint">Mỗi size có tồn kho riêng. Giá điều chỉnh được cộng vào giá gốc hoặc giá khuyến mãi; nhập số tiền theo DECIMAL(12,2), ví dụ <strong>15000.00</strong> hoặc <strong>-5000.00</strong>.</p>
  <div class="table-wrap">
    <table class="variant-table">
      <thead><tr><th>KÍCH CỠ</th><th>TỒN KHO</th><th>GIÁ ĐIỀU CHỈNH (₫)</th><th></th></tr></thead>
      <tbody id="variant-list">
        <?php foreach ($productForm['variants'] as $index => $variant): ?>
          <tr class="variant-row">
            <td><input name="variants[<?= (int) $index ?>][size]" value="<?= $escapeProduct($variant['size'] ?? '') ?>" maxlength="30" placeholder="S, M, L, XL" required></td>
            <td><input name="variants[<?= (int) $index ?>][stock_quantity]" type="number" min="0" max="4294967295" step="1" value="<?= $escapeProduct($variant['stock_quantity'] ?? '0') ?>" required></td>
            <td><input name="variants[<?= (int) $index ?>][additional_price]" type="number" min="-9999999999.99" max="9999999999.99" step="0.01" value="<?= $escapeProduct($variant['additional_price'] ?? '0.00') ?>" required></td>
            <td><button class="button button-danger remove-variant" type="button" aria-label="Xóa kích cỡ">Xóa</button></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot><tr><th colspan="2">TỔNG TỒN KHO</th><th colspan="2"><output id="variant-stock-total">0</output></th></tr></tfoot>
    </table>
  </div>
</div>
<script>
  (function () {
    const list = document.getElementById('variant-list');
    const total = document.getElementById('variant-stock-total');
    const money = new Intl.NumberFormat('vi-VN');
    function updateTotal() {
      const quantity = [...list.querySelectorAll('input[name$="[stock_quantity]"]')]
        .reduce((sum, input) => sum + (Number.parseInt(input.value, 10) || 0), 0);
      total.textContent = money.format(quantity);
    }
    document.getElementById('add-variant').addEventListener('click', function () {
      const index = Date.now() + Math.floor(Math.random() * 1000);
      const row = document.createElement('tr');
      row.className = 'variant-row';
      row.innerHTML = '<td><input name="variants[' + index + '][size]" maxlength="30" placeholder="XXL" required></td>' +
        '<td><input name="variants[' + index + '][stock_quantity]" type="number" min="0" max="4294967295" step="1" value="0" required></td>' +
        '<td><input name="variants[' + index + '][additional_price]" type="number" min="-9999999999.99" max="9999999999.99" step="0.01" value="0.00" required></td>' +
        '<td><button class="button button-danger remove-variant" type="button" aria-label="Xóa kích cỡ">Xóa</button></td>';
      list.appendChild(row);
      row.querySelector('input[name$="[size]"]').focus();
    });
    list.addEventListener('click', function (event) {
      const button = event.target.closest('.remove-variant');
      if (button) {
        button.closest('.variant-row').remove();
        updateTotal();
      }
    });
    list.addEventListener('input', updateTotal);
    updateTotal();
  })();
</script>
