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
        $voucherCode = strtoupper(trim((string) ($shipping['voucher_code'] ?? '')));
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
            $totalDiscount = 0;
            $voucher = null;
            if ($voucherCode !== '') {
                $voucherLookup = $this->pdo->prepare(
                    "SELECT voucher_id, product_id, discount_percent FROM late_delivery_vouchers
                     WHERE code = :code AND user_id = :user_id AND status = 'available'
                       AND expires_at >= CURRENT_TIMESTAMP FOR UPDATE"
                );
                $voucherLookup->execute(['code' => $voucherCode, 'user_id' => $userId]);
                $voucher = $voucherLookup->fetch();
                if (!$voucher) throw new DomainException('Mã voucher không hợp lệ, đã dùng hoặc đã hết hạn.');
            }
            $voucherMatched = false;
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
                $grossPrice = (float) ($product['sale_price'] ?? $product['price']) + (float) $product['additional_price'];
                $unitDiscount = $voucher && (int) $voucher['product_id'] === (int) $product['product_id']
                    ? (int) round($grossPrice * (int) $voucher['discount_percent'] / 100)
                    : 0;
                $price = max(0, (int) round($grossPrice) - $unitDiscount);
                $discount = $unitDiscount * $quantity;
                $voucherMatched = $voucherMatched || $discount > 0;
                $subtotal = $price * $quantity;
                $totalDiscount += $discount;
                $total += $subtotal;
                $lines[] = [
                    'product_id' => (int) $product['product_id'],
                    'variant_id' => (int) $product['variant_id'],
                    'name' => $product['product_name'],
                    'size' => $product['size'],
                    'quantity' => $quantity,
                    'price' => $price,
                    'discount' => $discount,
                    'subtotal' => $subtotal,
                ];
            }
            if ($voucher && !$voucherMatched) {
                throw new DomainException('Voucher này chỉ dùng được cho sản phẩm tương ứng trong đơn.');
            }
            if ($total < 1000) {
                throw new InvalidArgumentException('Tổng thanh toán tối thiểu là 1.000₫.');
            }

            $order = $this->pdo->prepare(
                "INSERT INTO orders (user_id, recipient_name, recipient_phone, total_amount, status, payment_method, shipping_address, note)
                 VALUES (:user_id, :recipient_name, :recipient_phone, :total_amount, 'pending', 'bank_transfer', :shipping_address, :note)"
            );
            $order->execute([
                'user_id' => $userId,
                'recipient_name' => $recipient,
                'recipient_phone' => $phone,
                'total_amount' => $total,
                'shipping_address' => $address,
                'note' => $voucher ? 'Đã áp dụng voucher ' . $voucherCode . ' (giảm ' . $totalDiscount . '₫).' : null,
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
            if ($voucher) {
                $reserve = $this->pdo->prepare(
                    "UPDATE late_delivery_vouchers SET status = 'reserved', reserved_order_id = :order_id
                     WHERE voucher_id = :voucher_id AND status = 'available'"
                );
                $reserve->execute(['order_id' => $orderId, 'voucher_id' => $voucher['voucher_id']]);
                if ($reserve->rowCount() !== 1) throw new DomainException('Voucher vừa được sử dụng ở đơn hàng khác.');
            }
            $this->pdo->commit();
            return ['order_id' => $orderId, 'reference' => 'HHDM' . str_pad((string) $orderId, 8, '0', STR_PAD_LEFT), 'amount' => $total, 'discount' => $totalDiscount, 'items' => $lines];
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
            $this->pdo->prepare("UPDATE late_delivery_vouchers SET status = 'available', reserved_order_id = NULL WHERE reserved_order_id = :order_id AND status = 'reserved'")->execute(['order_id' => $orderId]);
            $this->pdo->commit();
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            throw $exception;
        }
    }

    public function getOrdersForUser(int $userId): array
    {
        $this->issueLateDeliveryVouchers();
        $statement = $this->pdo->prepare(
            'SELECT o.order_id, o.order_date, o.total_amount, o.status, o.shipping_address, o.recipient_name, o.recipient_phone,
                    o.expected_delivery_from, o.expected_delivery_to, o.shipping_company, o.tracking_number,
                    o.shipped_at, o.delivered_at, o.note, op.paid_at
             FROM orders o LEFT JOIN order_payments op ON op.order_id = o.order_id
             WHERE o.user_id = :user_id ORDER BY o.order_date DESC, o.order_id DESC'
        );
        $statement->execute(['user_id' => $userId]);
        $orders = $statement->fetchAll();
        return $this->attachOrderDetails($orders);
    }

    public function getAdminOrders(): array
    {
        $this->issueLateDeliveryVouchers();
        $orders = $this->pdo->query(
            'SELECT o.order_id, o.order_date, o.total_amount, o.status, o.shipping_address, o.recipient_name,
                    o.recipient_phone, o.expected_delivery_from, o.expected_delivery_to, o.shipping_company,
                    o.tracking_number, o.shipped_at, o.delivered_at, o.note, op.paid_at, u.full_name, u.email
             FROM orders o INNER JOIN users u ON u.user_id = o.user_id
             LEFT JOIN order_payments op ON op.order_id = o.order_id
             ORDER BY FIELD(o.status, \'pending\', \'confirmed\', \'shipping\', \'completed\', \'cancelled\'), o.order_date DESC'
        )->fetchAll();
        return $this->attachOrderDetails($orders);
    }

    public function updateDeliveryStatus(int $orderId, string $status, string $carrier = '', string $trackingNumber = ''): bool
    {
        if ($status === 'shipping') {
            $statement = $this->pdo->prepare(
                "UPDATE orders SET status = 'shipping', shipping_company = :carrier, tracking_number = :tracking,
                        shipped_at = COALESCE(shipped_at, CURRENT_TIMESTAMP)
                 WHERE order_id = :order_id AND status = 'confirmed'"
            );
            $statement->execute([
                'carrier' => trim($carrier) !== '' ? trim($carrier) : null,
                'tracking' => trim($trackingNumber) !== '' ? trim($trackingNumber) : null,
                'order_id' => $orderId,
            ]);
            return $statement->rowCount() === 1;
        }
        if ($status === 'completed') {
            $statement = $this->pdo->prepare(
                "UPDATE orders SET status = 'completed', delivered_at = CURRENT_TIMESTAMP
                 WHERE order_id = :order_id AND status IN ('confirmed', 'shipping')"
            );
            $statement->execute(['order_id' => $orderId]);
            if ($statement->rowCount() === 1) $this->issueLateDeliveryVouchers();
            return $statement->rowCount() === 1;
        }
        return false;
    }

    private function attachOrderDetails(array $orders): array
    {
        if (!$orders) return [];
        $orderIds = array_map('intval', array_column($orders, 'order_id'));
        $idList = implode(',', $orderIds);
        $items = $this->pdo->query(
            'SELECT oi.order_id, oi.product_id, oi.quantity, oi.unit_price, oi.subtotal, pv.size, p.product_name
             FROM order_items oi INNER JOIN products p ON p.product_id = oi.product_id
             INNER JOIN product_variants pv ON pv.variant_id = oi.variant_id
             WHERE oi.order_id IN (' . $idList . ') ORDER BY oi.order_id, oi.order_item_id'
        )->fetchAll();
        $vouchers = $this->pdo->query(
            'SELECT v.origin_order_id AS order_id, v.code, v.discount_percent, v.status, v.expires_at, p.product_name
             FROM late_delivery_vouchers v INNER JOIN products p ON p.product_id = v.product_id
             WHERE v.origin_order_id IN (' . $idList . ') ORDER BY v.issued_at'
        )->fetchAll();
        $itemsByOrder = $vouchersByOrder = [];
        foreach ($items as $item) $itemsByOrder[(int) $item['order_id']][] = $item;
        foreach ($vouchers as $voucher) $vouchersByOrder[(int) $voucher['order_id']][] = $voucher;
        foreach ($orders as &$order) {
            $id = (int) $order['order_id'];
            $order['items'] = $itemsByOrder[$id] ?? [];
            $order['vouchers'] = $vouchersByOrder[$id] ?? [];
        }
        unset($order);
        return $orders;
    }

    private function issueLateDeliveryVouchers(): void
    {
        $this->pdo->exec("UPDATE late_delivery_vouchers SET status = 'expired' WHERE status = 'available' AND expires_at < CURRENT_TIMESTAMP");
        $lateItems = $this->pdo->query(
            "SELECT DISTINCT o.order_id, o.user_id, oi.product_id
             FROM orders o INNER JOIN order_items oi ON oi.order_id = o.order_id
             WHERE o.status IN ('confirmed', 'shipping', 'completed')
               AND o.expected_delivery_to IS NOT NULL
               AND ((o.status = 'completed' AND DATE(o.delivered_at) > o.expected_delivery_to)
                    OR (o.status <> 'completed' AND CURRENT_DATE() > o.expected_delivery_to))"
        )->fetchAll();
        if (!$lateItems) return;
        $insert = $this->pdo->prepare(
            "INSERT IGNORE INTO late_delivery_vouchers
                (code, origin_order_id, user_id, product_id, discount_percent, status, expires_at)
             VALUES (:code, :order_id, :user_id, :product_id, 15, 'available', DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 90 DAY))"
        );
        foreach ($lateItems as $item) {
            $insert->execute([
                'code' => 'HHDM15' . strtoupper(bin2hex(random_bytes(4))),
                'order_id' => $item['order_id'],
                'user_id' => $item['user_id'],
                'product_id' => $item['product_id'],
            ]);
        }
    }
}
