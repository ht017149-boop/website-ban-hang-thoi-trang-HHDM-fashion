<?php

require_once __DIR__ . '/BaseModel.php';

class PaymentModel extends BaseModel
{
    private function ensureConfigured(): void
    {
        if (PAYOS_CLIENT_ID === '' || PAYOS_API_KEY === '' || PAYOS_CHECKSUM_KEY === '') {
            throw new RuntimeException('Chưa cấu hình khóa payOS trên máy chủ.');
        }
    }

    private function request(string $method, string $path, ?array $payload = null): array
    {
        $this->ensureConfigured();
        $curl = curl_init('https://api-merchant.payos.vn' . $path);
        $headers = [
            'x-client-id: ' . PAYOS_CLIENT_ID,
            'x-api-key: ' . PAYOS_API_KEY,
            'Content-Type: application/json',
        ];
        curl_setopt_array($curl, [
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_TIMEOUT => 20,
        ]);
        if ($payload !== null) curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $body = curl_exec($curl);
        $status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);
        if ($body === false || $status < 200 || $status >= 300) {
            throw new RuntimeException('payOS API request failed: ' . ($error ?: 'HTTP ' . $status));
        }
        $result = json_decode($body, true);
        if (!is_array($result) || ($result['code'] ?? '') !== '00' || !is_array($result['data'] ?? null)) {
            throw new RuntimeException('payOS rejected the payment request.');
        }
        return $result['data'];
    }

    public function createPaymentLink(array $order, string $returnUrl, string $cancelUrl): array
    {
        $orderId = (int) $order['order_id'];
        $amount = (int) $order['amount'];
        $description = 'HHDM ' . $orderId;
        $signatureData = 'amount=' . $amount . '&cancelUrl=' . $cancelUrl
            . '&description=' . $description . '&orderCode=' . $orderId . '&returnUrl=' . $returnUrl;
        $items = array_map(static function (array $item): array {
            return [
                'name' => mb_substr($item['name'] . ' - ' . $item['size'], 0, 50),
                'quantity' => (int) $item['quantity'],
                'price' => (int) round((float) $item['price']),
            ];
        }, $order['items']);
        $data = $this->request('POST', '/v2/payment-requests', [
            'orderCode' => $orderId,
            'amount' => $amount,
            'description' => $description,
            'items' => $items,
            'cancelUrl' => $cancelUrl,
            'returnUrl' => $returnUrl,
            'signature' => hash_hmac('sha256', $signatureData, PAYOS_CHECKSUM_KEY),
        ]);
        $store = $this->pdo->prepare(
            'INSERT INTO order_payments (order_id, payment_link_id, checkout_url, qr_code, status)
             VALUES (:order_id, :payment_link_id, :checkout_url, :qr_code, \'pending\')'
        );
        $store->execute([
            'order_id' => $orderId,
            'payment_link_id' => (string) ($data['paymentLinkId'] ?? ''),
            'checkout_url' => (string) ($data['checkoutUrl'] ?? ''),
            'qr_code' => (string) ($data['qrCode'] ?? ''),
        ]);
        return [
            'checkout_url' => (string) ($data['checkoutUrl'] ?? ''),
            'qr_code' => (string) ($data['qrCode'] ?? ''),
            'bank_bin' => (string) ($data['bin'] ?? ''),
            'account_number' => (string) ($data['accountNumber'] ?? ''),
            'account_name' => (string) ($data['accountName'] ?? ''),
        ];
    }

    public function refreshStatus(int $orderId): bool
    {
        $payment = $this->findPayment($orderId);
        if (!$payment) return false;
        if ($payment['status'] === 'paid') return true;
        if (!empty($payment['last_checked_at']) && strtotime($payment['last_checked_at']) > time() - 8) return false;
        $this->pdo->prepare('UPDATE order_payments SET last_checked_at = CURRENT_TIMESTAMP WHERE order_id = :order_id')->execute(['order_id' => $orderId]);
        $data = $this->request('GET', '/v2/payment-requests/' . rawurlencode((string) $orderId));
        if (($data['status'] ?? '') !== 'PAID' || (int) ($data['amountPaid'] ?? 0) !== (int) round((float) $payment['total_amount'])) {
            return false;
        }
        $this->markPaid($orderId, (int) $payment['total_amount']);
        return true;
    }

    public function isCancelledAtProvider(int $orderId): bool
    {
        if (!$this->findPayment($orderId)) return false;
        $data = $this->request('GET', '/v2/payment-requests/' . rawurlencode((string) $orderId));
        return ($data['status'] ?? '') === 'CANCELLED';
    }

    public function handleWebhook(array $payload): bool
    {
        $this->ensureConfigured();
        $data = $payload['data'] ?? null;
        if (!is_array($data) || empty($payload['signature'])) return false;
        $parts = [];
        $dataKeys = array_keys($data);
        sort($dataKeys, SORT_STRING);
        foreach ($dataKeys as $key) {
            $value = $data[$key];
            $parts[] = $key . '=' . (is_bool($value) ? ($value ? '1' : '0') : (is_scalar($value) ? (string) $value : ''));
        }
        $expected = hash_hmac('sha256', implode('&', $parts), PAYOS_CHECKSUM_KEY);
        if (!hash_equals($expected, (string) $payload['signature'])) return false;
        if (($payload['success'] ?? false) !== true || ($data['code'] ?? '') !== '00') return true;

        $orderId = (int) ($data['orderCode'] ?? 0);
        $payment = $this->findPayment($orderId);
        if (!$payment) return true; // payOS webhook URL verification may send a sample transaction.
        if ((int) ($data['amount'] ?? 0) !== (int) round((float) $payment['total_amount'])) return false;
        $this->markPaid($orderId, (int) $payment['total_amount']);
        return true;
    }

    public function isPaidForUser(int $orderId, int $userId): bool
    {
        $statement = $this->pdo->prepare(
            "SELECT 1 FROM orders o INNER JOIN order_payments op ON op.order_id = o.order_id
             WHERE o.order_id = :order_id AND o.user_id = :user_id AND o.status = 'completed' AND op.status = 'paid' LIMIT 1"
        );
        $statement->execute(['order_id' => $orderId, 'user_id' => $userId]);
        return (bool) $statement->fetchColumn();
    }

    public function getOrderStatusForUser(int $orderId, int $userId): ?string
    {
        $statement = $this->pdo->prepare('SELECT status FROM orders WHERE order_id = :order_id AND user_id = :user_id LIMIT 1');
        $statement->execute(['order_id' => $orderId, 'user_id' => $userId]);
        $status = $statement->fetchColumn();
        return $status === false ? null : (string) $status;
    }

    private function findPayment(int $orderId): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT op.order_id, op.status, op.last_checked_at, o.total_amount FROM order_payments op
             INNER JOIN orders o ON o.order_id = op.order_id WHERE op.order_id = :order_id LIMIT 1'
        );
        $statement->execute(['order_id' => $orderId]);
        $payment = $statement->fetch();
        return $payment ?: null;
    }

    private function markPaid(int $orderId, int $amount): void
    {
        $this->pdo->beginTransaction();
        try {
            $lock = $this->pdo->prepare('SELECT total_amount FROM orders WHERE order_id = :order_id FOR UPDATE');
            $lock->execute(['order_id' => $orderId]);
            $order = $lock->fetch();
            if (!$order || !in_array($order['status'], ['pending', 'completed'], true)
                || (int) round((float) $order['total_amount']) !== $amount) {
                throw new DomainException('Payment amount does not match the order.');
            }
            $this->pdo->prepare("UPDATE orders SET status = 'completed' WHERE order_id = :order_id AND status = 'pending'")
                ->execute(['order_id' => $orderId]);
            $this->pdo->prepare("UPDATE order_payments SET status = 'paid', paid_at = CURRENT_TIMESTAMP WHERE order_id = :order_id AND status <> 'paid'")
                ->execute(['order_id' => $orderId]);
            $this->pdo->commit();
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            throw $exception;
        }
    }
}
