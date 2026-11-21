<?php
$variantSizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
?>
<div class="field full" data-variant-sizes="<?= $escapeProduct(json_encode($variantSizes, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)) ?>">
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
<script src="assets/js/variant-fields.js?v=20261121" defer></script>
