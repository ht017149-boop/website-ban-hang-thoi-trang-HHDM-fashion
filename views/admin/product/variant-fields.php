<?php
$variantSizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
?>
<div class="field full">
  <div class="panel-head"><h2>Quản lý kích cỡ & tồn kho</h2><button class="button button-light" id="add-variant" type="button">＋ THÊM KÍCH CỠ</button></div>
  <p class="hint">Mỗi size có tồn kho và giá bán cuối cùng riêng. Số tiền được phân tách hàng nghìn để dễ đọc; nhập <strong>0</strong> để dùng giá gốc hoặc giá khuyến mãi của sản phẩm.</p>
  <div class="table-wrap">
    <table class="variant-table">
      <thead><tr><th>KÍCH CỠ</th><th>TỒN KHO</th><th>GIÁ BÁN THEO SIZE (₫)</th><th></th></tr></thead>
      <tbody id="variant-list">
        <?php foreach ($productForm['variants'] as $index => $variant): ?>
          <?php $currentSize = (string) ($variant['size'] ?? ''); ?>
          <tr class="variant-row">
            <td>
              <select name="variants[<?= (int) $index ?>][size]" required>
                <option value="">Chọn size</option>
                <?php foreach ($variantSizes as $size): ?>
                  <option value="<?= $escapeProduct($size) ?>" <?= $currentSize === $size ? 'selected' : '' ?>><?= $escapeProduct($size) ?></option>
                <?php endforeach; ?>
                <?php if ($currentSize !== '' && !in_array($currentSize, $variantSizes, true)): ?>
                  <option value="<?= $escapeProduct($currentSize) ?>" selected><?= $escapeProduct($currentSize) ?> (size hiện tại)</option>
                <?php endif; ?>
              </select>
            </td>
            <td><input name="variants[<?= (int) $index ?>][stock_quantity]" type="number" min="0" max="4294967295" step="1" value="<?= $escapeProduct($variant['stock_quantity'] ?? '0') ?>" required></td>
            <td><input class="variant-money-input" name="variants[<?= (int) $index ?>][additional_price]" type="text" inputmode="decimal" value="<?= $escapeProduct($variant['additional_price'] ?? '0.00') ?>" required></td>
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
    const sizes = <?= json_encode($variantSizes, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
    const basePrice = document.getElementById('price');
    function formatBasePrice(input) {
      const raw = input.value.replace(/,/g, '');
      const match = raw.match(/^(\d*)(?:\.(\d*))?$/);
      if (!match) return;
      const integer = match[1].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
      input.value = integer + (match[2] !== undefined ? '.' + match[2] : '');
    }
    function unformatMoney(input) {
      const value = input.value.trim();
      if (value.includes(',')) {
        input.value = value.replace(/\./g, '').replace(',', '.');
      } else if (/^-?\d{1,3}(?:\.\d{3})+$/.test(value)) {
        input.value = value.replace(/\./g, '');
      } else {
        input.value = value;
      }
    }
    function formatMoney(input) {
      const value = input.value.trim();
      const match = value.match(/^(-?)(\d+)(?:\.(\d{1,2}))?$/);
      if (!match) return;
      const integer = match[2].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
      input.value = match[1] + integer + (match[3] ? ',' + match[3] : '');
    }
    function formatMoneyInputs() {
      list.querySelectorAll('.variant-money-input').forEach(formatMoney);
    }
    basePrice.addEventListener('focus', function () {
      basePrice.value = basePrice.value.replace(/,/g, '');
    });
    basePrice.addEventListener('blur', function () {
      formatBasePrice(basePrice);
    });
    function updateTotal() {
      const quantity = [...list.querySelectorAll('input[name$="[stock_quantity]"]')]
        .reduce((sum, input) => sum + (Number.parseInt(input.value, 10) || 0), 0);
      total.textContent = money.format(quantity);
    }
    list.addEventListener('focusin', function (event) {
      if (event.target.matches('.variant-money-input')) unformatMoney(event.target);
    });
    list.addEventListener('focusout', function (event) {
      if (event.target.matches('.variant-money-input')) formatMoney(event.target);
    });
    list.closest('form').addEventListener('submit', function () {
      basePrice.value = basePrice.value.replace(/,/g, '');
      list.querySelectorAll('.variant-money-input').forEach(unformatMoney);
    });
    document.getElementById('add-variant').addEventListener('click', function () {
      const index = Date.now() + Math.floor(Math.random() * 1000);
      const row = document.createElement('tr');
      row.className = 'variant-row';
      const sizeSelect = document.createElement('select');
      sizeSelect.name = 'variants[' + index + '][size]';
      sizeSelect.required = true;
      sizeSelect.add(new Option('Chọn size', ''));
      sizes.forEach(function (size) {
        sizeSelect.add(new Option(size, size));
      });
      row.innerHTML = '<td></td>' +
        '<td><input name="variants[' + index + '][stock_quantity]" type="number" min="0" max="4294967295" step="1" value="0" required></td>' +
        '<td><input class="variant-money-input" name="variants[' + index + '][additional_price]" type="text" inputmode="decimal" value="0.00" required></td>' +
        '<td><button class="button button-danger remove-variant" type="button" aria-label="Xóa kích cỡ">Xóa</button></td>';
      row.cells[0].appendChild(sizeSelect);
      list.appendChild(row);
      formatMoneyInputs();
      sizeSelect.focus();
    });
    list.addEventListener('click', function (event) {
      const button = event.target.closest('.remove-variant');
      if (button) {
        button.closest('.variant-row').remove();
        updateTotal();
      }
    });
    list.addEventListener('input', updateTotal);
    formatBasePrice(basePrice);
    formatMoneyInputs();
    updateTotal();
  })();
</script>
