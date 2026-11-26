<?php

require_once __DIR__ . '/BaseModel.php';

class OrderModel extends BaseModel
{
    public function createPendingOrder(int $userId, array $items, array $shipping): array
    {
        if ($userId < 1 || !$items || count($items) > 50) {
            throw new InvalidArgumentException('Giỏ hàng không hợp lệ.');
        }
        $recipient = trim((string) ($shipping['recipient'] ?? ''));
        $phone = trim((string) ($shipping['phone'] ?? ''));
        $address = trim((string) ($shipping['address'] ?? ''));
        if ($recipient === '' || mb_strlen($recipient) > 150
            || !preg_match('/^[0-9+().\s-]{8,20}$/', $phone)
            || $address === '' || mb_strlen($address) > 500) {
            throw new InvalidArgumentException('Vui lòng nhập tên người nhận, số điện thoại và địa chỉ giao hàng hợp lệ.');
        }

        $combined = [];
        foreach ($items as $item) {
            if (!is_array($item)) throw new InvalidArgumentException('Thông tin sản phẩm không hợp lệ.');
            $key = (string) ($item['product_id'] ?? '') . ':' . trim((string) ($item['size'] ?? ''));
            if (!isset($combined[$key])) $combined[$key] = $item;
            else $combined[$key]['quantity'] = (int) ($combined[$key]['quantity'] ?? 0) + (int) ($item['quantity'] ?? 0);
        }
        $items = array_values($combined);

        $this->pdo->beginTransaction();
        try {
            $lines = [];
            $total = 0;
            $lookup = $this->pdo->prepare(
                "SELECT p.product_id, p.product_name, p.price, p.sale_price, pv.variant_id,
                        pv.size, pv.stock_quantity, pv.additional_price
                 FROM products p
                 INNER JOIN product_variants pv ON pv.product_id = p.product_id AND pv.status = 'active'
                 INNER JOIN categories c ON c.category_id = p.category_id AND c.status = 'active'
                 WHERE p.product_id = :product_id AND p.status = 'active' AND pv.size = :size
                 FOR UPDATE"
            );
            foreach ($items as $item) {
                $productId = filter_var($item['product_id'] ?? null, FILTER_VALIDATE_INT);
                $size = trim((string) ($item['size'] ?? ''));
                $quantity = filter_var($item['quantity'] ?? null, FILTER_VALIDATE_INT);
                if (!$productId || !$quantity || $quantity < 1 || $quantity > 100 || $size === '') {
                    throw new InvalidArgumentException('Thông tin sản phẩm hoặc số lượng không hợp lệ.');
                }
                $lookup->execute(['product_id' => $productId, 'size' => $size]);
                $product = $lookup->fetch();
                if (!$product) {
                    throw new DomainException('Một sản phẩm hoặc kích cỡ đã không còn kinh doanh. Vui lòng tải lại giỏ hàng.');
                }
                if ((int) $product['stock_quantity'] < $quantity) {
                    throw new DomainException('Size ' . $size . ' của ' . $product['product_name'] . ' không đủ tồn kho.');
                }
                $price = (float) ($product['sale_price'] ?? $product['price']) + (float) $product['additional_price'];
                $subtotal = (int) round($price * $quantity);
                $total += $subtotal;
                $lines[] = [
                    'product_id' => (int) $product['product_id'],
                    'variant_id' => (int) $product['variant_id'],
                    'name' => $product['product_name'],
                    'size' => $product['size'],
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $subtotal,
                ];
            }
            if ($total < 1000) {
                throw new InvalidArgumentException('Tổng thanh toán tối thiểu là 1.000₫.');
            }

            $order = $this->pdo->prepare(
                "INSERT INTO orders (user_id, total_amount, status, payment_method, shipping_address, note)
                 VALUES (:user_id, :total_amount, 'pending', 'bank_transfer', :shipping_address, :note)"
            );
            $order->execute([
                'user_id' => $userId,
                'total_amount' => $total,
                'shipping_address' => $address,
                'note' => 'Người nhận: ' . $recipient . ' | SĐT: ' . $phone,
            ]);
            $orderId = (int) $this->pdo->lastInsertId();
            $lineInsert = $this->pdo->prepare(
                'INSERT INTO order_items (order_id, product_id, variant_id, quantity, unit_price, subtotal)
                 VALUES (:order_id, :product_id, :variant_id, :quantity, :unit_price, :subtotal)'
            );
            $stockUpdate = $this->pdo->prepare(
                'UPDATE product_variants SET stock_quantity = stock_quantity - :quantity WHERE variant_id = :variant_id'
            );
            foreach ($lines as $line) {
                $lineInsert->execute([
                    'order_id' => $orderId,
                    'product_id' => $line['product_id'],
                    'variant_id' => $line['variant_id'],
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['price'],
                    'subtotal' => $line['subtotal'],
                ]);
                $stockUpdate->execute(['quantity' => $line['quantity'], 'variant_id' => $line['variant_id']]);
            }
            $this->pdo->exec(
                'UPDATE products p SET p.stock_quantity = (
                    SELECT COALESCE(SUM(pv.stock_quantity), 0) FROM product_variants pv
                    WHERE pv.product_id = p.product_id AND pv.status = \'active\'
                ) WHERE p.product_id IN (' . implode(',', array_unique(array_column($lines, 'product_id'))) . ')'
            );
            $this->pdo->commit();
            return ['order_id' => $orderId, 'reference' => 'HHDM' . str_pad((string) $orderId, 8, '0', STR_PAD_LEFT), 'amount' => $total, 'items' => $lines];
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            throw $exception;
        }
    }

    public function cancelAndRestock(int $orderId): void
    {
        $this->pdo->beginTransaction();
        try {
            $orderLock = $this->pdo->prepare('SELECT status FROM orders WHERE order_id = :order_id FOR UPDATE');
            $orderLock->execute(['order_id' => $orderId]);
            if ($orderLock->fetchColumn() !== 'pending') {
                $this->pdo->commit();
                return;
            }
            $items = $this->pdo->prepare('SELECT variant_id, product_id, quantity FROM order_items WHERE order_id = :order_id');
            $items->execute(['order_id' => $orderId]);
            $stock = $this->pdo->prepare('UPDATE product_variants SET stock_quantity = stock_quantity + :quantity WHERE variant_id = :variant_id');
            $productIds = [];
            foreach ($items->fetchAll() as $item) {
                $stock->execute(['quantity' => $item['quantity'], 'variant_id' => $item['variant_id']]);
                $productIds[] = (int) $item['product_id'];
            }
            if ($productIds) {
                $this->pdo->exec('UPDATE products p SET p.stock_quantity = (SELECT COALESCE(SUM(pv.stock_quantity),0) FROM product_variants pv WHERE pv.product_id=p.product_id AND pv.status=\'active\') WHERE p.product_id IN (' . implode(',', array_unique($productIds)) . ')');
            }
            $this->pdo->prepare("UPDATE orders SET status = 'cancelled' WHERE order_id = :order_id AND status = 'pending'")->execute(['order_id' => $orderId]);
            $this->pdo->prepare("UPDATE order_payments SET status = 'cancelled' WHERE order_id = :order_id AND status = 'pending'")->execute(['order_id' => $orderId]);
            $this->pdo->commit();
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            throw $exception;
        }
    }
}
