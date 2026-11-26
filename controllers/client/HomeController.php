<?php

require_once PATH_MODEL . "ProductModel.php";
require_once PATH_MODEL . "OrderModel.php";
require_once PATH_MODEL . "PaymentModel.php";

class HomeController
{
    public function createOrder(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['user']['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Vui lòng đăng nhập để tạo đơn hàng.']);
            return;
        }
        $token = (string) ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
        if (empty($_SESSION['csrf_token']) || !hash_equals((string) $_SESSION['csrf_token'], $token)) {
            http_response_code(419);
            echo json_encode(['error' => 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang.']);
            return;
        }
        $payload = json_decode((string) file_get_contents('php://input'), true);
        if (!is_array($payload) || !is_array($payload['items'] ?? null)) {
            http_response_code(400);
            echo json_encode(['error' => 'Dữ liệu đơn hàng không hợp lệ.']);
            return;
        }
        $paymentMethod = (string) ($payload['checkout']['payment_method'] ?? 'bank_transfer');
        if (!in_array($paymentMethod, ['bank_transfer', 'cod'], true)) {
            http_response_code(422);
            echo json_encode(['error' => 'Phương thức thanh toán không hợp lệ.'], JSON_UNESCAPED_UNICODE);
            return;
        }
        try {
            $orders = new OrderModel();
            $order = $orders->createPendingOrder((int) $_SESSION['user']['user_id'], $payload['items'], is_array($payload['checkout'] ?? null) ? $payload['checkout'] : [], $paymentMethod);
            if ($paymentMethod === 'cod') {
                echo json_encode(['success' => true, 'order' => $order], JSON_UNESCAPED_UNICODE);
                return;
            }
            if (function_exists('curl_init') && PAYOS_CLIENT_ID !== '' && PAYOS_API_KEY !== '' && PAYOS_CHECKSUM_KEY !== '') {
              try {
                $payments = new PaymentModel();
                $base = APP_PUBLIC_URL . 'index.php?page=';
                $payment = $payments->createPaymentLink(
                    $order,
                    $base . 'payment_return',
                    $base . 'payment_cancel&orderCode=' . $order['order_id']
                );
                $order['payment'] = $payment;
              } catch (Throwable $exception) {
                error_log('HHDM payment link creation failed for order ' . $order['order_id'] . ': ' . $exception->getMessage());
                $order['payment_error'] = 'Đơn hàng đã được lưu và đang chờ thanh toán, nhưng chưa tạo được mã QR. Vui lòng vào mục đơn hàng hoặc liên hệ cửa hàng để được hỗ trợ.';
              }
            } else {
                $order['payment_error'] = 'Đơn hàng đã được lưu và đang chờ thanh toán. Cửa hàng chưa cấu hình payOS để tạo mã QR.';
            }
            echo json_encode(['success' => true, 'order' => $order], JSON_UNESCAPED_UNICODE);
        } catch (InvalidArgumentException | DomainException $exception) {
            http_response_code(422);
            echo json_encode(['error' => $exception->getMessage()], JSON_UNESCAPED_UNICODE);
        } catch (Throwable $exception) {
            error_log('HHDM checkout failed: ' . $exception->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Không thể khởi tạo thanh toán. Vui lòng kiểm tra cấu hình payOS và thử lại.'], JSON_UNESCAPED_UNICODE);
        }
    }

    public function paymentWebhook(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }
        $payload = json_decode((string) file_get_contents('php://input'), true);
        try {
            if (!is_array($payload) || !(new PaymentModel())->handleWebhook($payload)) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid webhook']);
                return;
            }
            echo json_encode(['success' => true]);
        } catch (Throwable $exception) {
            http_response_code(500);
            echo json_encode(['error' => 'Webhook processing failed']);
        }
    }

    public function paymentReturn(): void
    {
        $userId = (int) ($_SESSION['user']['user_id'] ?? 0);
        $orderId = (int) ($_GET['orderCode'] ?? 0);
        if ($userId < 1 || $orderId < 1) {
            header('Location: index.php?page=login');
            exit;
        }
        $payments = new PaymentModel();
        $status = $payments->getOrderStatusForUser($orderId, $userId);
        if ($status === null) {
            http_response_code(404);
            exit('Không tìm thấy đơn hàng.');
        }
        if ($status === 'pending') {
            try { $payments->refreshStatus($orderId); } catch (Throwable $exception) { /* webhook can still confirm it */ }
        }
        if ($payments->isPaidForUser($orderId, $userId)) {
            header('Location: index.php?page=payment_success&order=' . $orderId);
            exit;
        }
        $orderIdForView = $orderId;
        require PATH_VIEW_CLIENT . 'payment-wait.php';
    }

    public function paymentStatus(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        $userId = (int) ($_SESSION['user']['user_id'] ?? 0);
        $orderId = (int) ($_GET['order'] ?? 0);
        if ($userId < 1 || $orderId < 1) {
            http_response_code(401);
            echo json_encode(['paid' => false]);
            return;
        }
        $payments = new PaymentModel();
        try { $payments->refreshStatus($orderId); } catch (Throwable $exception) { /* webhook remains the payment authority */ }
        echo json_encode(['paid' => $payments->isPaidForUser($orderId, $userId)]);
    }

    public function paymentSuccess(): void
    {
        $userId = (int) ($_SESSION['user']['user_id'] ?? 0);
        $orderId = (int) ($_GET['order'] ?? 0);
        if ($userId < 1 || $orderId < 1 || !(new PaymentModel())->isPaidForUser($orderId, $userId)) {
            header('Location: index.php?page=home');
            exit;
        }
        $orderIdForView = $orderId;
        require PATH_VIEW_CLIENT . 'payment-success.php';
    }

    public function myOrders(): void
    {
        $userId = (int) ($_SESSION['user']['user_id'] ?? 0);
        if ($userId < 1) {
            header('Location: index.php?page=login');
            exit;
        }
        if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $orders = (new OrderModel())->getOrdersForUser($userId);
        $csrfToken = (string) $_SESSION['csrf_token'];
        $flash = $_SESSION['client_order_flash'] ?? null;
        unset($_SESSION['client_order_flash']);
        $escape = static fn($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
        require PATH_VIEW_CLIENT . 'my-orders.php';
    }

    public function requestOrderCancellation(): void
    {
        $userId = (int) ($_SESSION['user']['user_id'] ?? 0);
        if ($userId < 1) {
            header('Location: index.php?page=login');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST'
            || empty($_SESSION['csrf_token'])
            || !hash_equals((string) $_SESSION['csrf_token'], (string) ($_POST['csrf_token'] ?? ''))) {
            http_response_code(400);
            exit('Yêu cầu không hợp lệ hoặc phiên làm việc đã hết hạn.');
        }
        try {
            $requested = (new OrderModel())->requestCancellationByUser(
                (int) ($_POST['order_id'] ?? 0),
                $userId,
                (string) ($_POST['reason'] ?? '')
            );
            $_SESSION['client_order_flash'] = $requested
                ? 'Đã gửi yêu cầu hủy. Đơn chỉ được hủy sau khi cửa hàng xem xét.'
                : 'Không thể gửi yêu cầu. Đơn có thể đã bàn giao hoặc đang có yêu cầu hủy.';
        } catch (InvalidArgumentException $exception) {
            $_SESSION['client_order_flash'] = $exception->getMessage();
        }
        header('Location: index.php?page=my_orders');
        exit;
    }

    public function updateOrderDetails(): void
    {
        $userId = (int) ($_SESSION['user']['user_id'] ?? 0);
        if ($userId < 1) {
            header('Location: index.php?page=login');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST'
            || empty($_SESSION['csrf_token'])
            || !hash_equals((string) $_SESSION['csrf_token'], (string) ($_POST['csrf_token'] ?? ''))) {
            http_response_code(400);
            exit('Yêu cầu không hợp lệ hoặc phiên làm việc đã hết hạn.');
        }
        try {
            $updated = (new OrderModel())->updateDeliveryDetailsByUser(
                (int) ($_POST['order_id'] ?? 0),
                $userId,
                [
                    'recipient' => $_POST['recipient'] ?? '',
                    'phone' => $_POST['phone'] ?? '',
                    'address' => $_POST['address'] ?? '',
                ]
            );
            $_SESSION['client_order_flash'] = $updated
                ? 'Đã cập nhật thông tin nhận hàng.'
                : 'Không thể sửa thông tin. Đơn có thể đã bàn giao hoặc đang chờ xử lý yêu cầu hủy.';
        } catch (InvalidArgumentException $exception) {
            $_SESSION['client_order_flash'] = $exception->getMessage();
        }
        header('Location: index.php?page=my_orders');
        exit;
    }

    public function paymentCancel(): void
    {
        $userId = (int) ($_SESSION['user']['user_id'] ?? 0);
        $orderId = (int) ($_GET['orderCode'] ?? 0);
        if ($userId > 0 && $orderId > 0) {
            $payments = new PaymentModel();
            if ($payments->getOrderStatusForUser($orderId, $userId) === 'pending') {
                try {
                    if ($payments->isCancelledAtProvider($orderId)) (new OrderModel())->cancelAndRestock($orderId);
                } catch (Throwable $exception) { /* preserve the pending order when provider status is unknown */ }
            }
        }
        header('Location: index.php?page=home');
        exit;
    }

    public function index(): void
    {
        if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $homepageData = (new ProductModel())->getHomepageData();
        require PATH_VIEW_CLIENT . "home.php";
    }

    public function detail(): void
    {
        if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $productId = (int) ($_GET["id"] ?? 0);
        $model = new ProductModel();
        $product = $productId > 0 ? $model->getClientProduct($productId) : null;
        if (!$product) {
            header("Location: index.php?page=home");
            exit();
        }
        $comments = $model->getProductComments($productId);
        require PATH_VIEW_CLIENT . "product_detail.php";
    }
}
