<?php

require_once PATH_MODEL . 'UserModel.php';
require_once PATH_MODEL . 'ProductModel.php';

class AdminController
{
    private UserModel $users;
    private ProductModel $products;

    public function __construct()
    {
        $this->users = new UserModel();
        $this->products = new ProductModel();
    }

    public function handle(): void
    {
        $this->requireAdmin();
        $csrfToken = $this->csrfToken();
        $page = (string) ($_GET['page'] ?? 'admin');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($page === 'admin-products') {
                $this->handleProductAction();
            }
            if ($page === 'admin-accounts') {
                $this->handleAccountAction();
            }
        }

        if ($page === 'admin-accounts') {
            $this->showAccounts();
            return;
        }
        if ($page === 'admin-products') {
            $this->showProducts();
            return;
        }

        $stats = $this->users->getDashboardStats();
        $latestAccounts = $this->users->getLatestAccounts();
        $currentPage = 'dashboard';
        $flash = $this->takeFlash();
        $contentView = PATH_VIEW_ADMIN . 'dashboard.php';
        require PATH_VIEW_MAIN_ADMIN;
    }

    private function showProducts(): void
    {
        $productId = (int) ($_GET['id'] ?? 0);
        $productToEdit = $productId > 0 ? $this->products->findAdminProduct($productId) : null;
        if ($productId > 0 && !$productToEdit) {
            $_SESSION['admin_flash'] = 'Không tìm thấy sản phẩm cần chỉnh sửa.';
            header('Location: index.php?page=admin-products');
            exit;
        }

        $productForm = $_SESSION['product_form'] ?? $productToEdit ?? [
            'product_id' => 0,
            'category_id' => '',
            'product_name' => '',
            'description' => '',
            'price' => '',
            'sale_price' => '',
            'stock_quantity' => '0',
            'image_url' => '',
            'status' => 'active',
            'sizes' => 'S, M, L, XL',
        ];
        unset($_SESSION['product_form']);
        $productForm['product_id'] = (int) ($productForm['product_id'] ?? $productId);
        $productErrors = $_SESSION['product_errors'] ?? [];
        unset($_SESSION['product_errors']);
        $productList = $this->products->getAdminProducts();
        $categories = $this->products->getAdminCategories();
        $currentPage = 'products';
        $flash = $this->takeFlash();
        $csrfToken = $this->csrfToken();
        $contentView = PATH_VIEW_ADMIN . 'product/index.php';
        require PATH_VIEW_MAIN_ADMIN;
    }

    private function showAccounts(): void
    {
        $accounts = $this->users->getAccounts();
        $accountErrors = $_SESSION['account_errors'] ?? [];
        $accountOld = $_SESSION['account_old'] ?? ['full_name' => '', 'email' => '', 'phone' => '', 'role' => 'customer'];
        unset($_SESSION['account_errors'], $_SESSION['account_old']);

        $currentPage = 'accounts';
        $flash = $this->takeFlash();
        $csrfToken = $this->csrfToken();
        $contentView = PATH_VIEW_ADMIN . 'accounts/index.php';
        require PATH_VIEW_MAIN_ADMIN;
    }

    private function handleAccountAction(): void
    {
        if (!$this->validCsrfToken()) {
            http_response_code(400);
            exit('Yêu cầu không hợp lệ hoặc phiên làm việc đã hết hạn.');
        }

        $action = (string) ($_POST['action'] ?? '');
        if ($action === 'create_account') {
            $this->createAccount();
        } elseif ($action === 'update_role') {
            $this->updateRole();
        } elseif ($action === 'delete_account') {
            $this->deleteAccount();
        } else {
            http_response_code(400);
            exit('Thao tác không hợp lệ.');
        }
    }

    private function handleProductAction(): void
    {
        if (!$this->validCsrfToken()) {
            http_response_code(400);
            exit('Yêu cầu không hợp lệ hoặc phiên làm việc đã hết hạn.');
        }

        $action = (string) ($_POST['action'] ?? '');
        if ($action === 'save_product') {
            $this->saveProduct();
        } elseif ($action === 'delete_product') {
            $this->deleteProduct();
        } else {
            http_response_code(400);
            exit('Thao tác sản phẩm không hợp lệ.');
        }
    }

    private function saveProduct(): void
    {
        $productId = (int) ($_POST['product_id'] ?? 0);
        $existingProduct = $productId > 0 ? $this->products->findAdminProduct($productId) : null;
        $form = [
            'product_id' => $productId,
            'category_id' => trim((string) ($_POST['category_id'] ?? '')),
            'product_name' => trim((string) ($_POST['product_name'] ?? '')),
            'description' => trim((string) ($_POST['description'] ?? '')),
            'price' => trim((string) ($_POST['price'] ?? '')),
            'sale_price' => trim((string) ($_POST['sale_price'] ?? '')),
            'stock_quantity' => trim((string) ($_POST['stock_quantity'] ?? '')),
            'image_url' => trim((string) ($_POST['image_url'] ?? '')),
            'status' => (string) ($_POST['status'] ?? ''),
            'sizes' => trim((string) ($_POST['sizes'] ?? '')),
        ];
        $errors = [];
        $pricePattern = '/^\d{1,10}(?:\.\d{1,2})?$/';

        if ($productId > 0 && !$existingProduct) {
            $errors[] = 'Không tìm thấy sản phẩm cần chỉnh sửa.';
        }
        if ($form['product_name'] === '' || mb_strlen($form['product_name']) > 200) {
            $errors[] = 'Tên sản phẩm không được để trống và tối đa 200 ký tự.';
        }
        if (!ctype_digit($form['category_id']) || !$this->products->categoryExists((int) $form['category_id'])) {
            $errors[] = 'Vui lòng chọn danh mục hợp lệ.';
        }
        if (!preg_match($pricePattern, $form['price']) || (float) $form['price'] <= 0) {
            $errors[] = 'Giá bán phải lớn hơn 0 và có tối đa 2 chữ số thập phân.';
        }
        if ($form['sale_price'] !== '' && (
            !preg_match($pricePattern, $form['sale_price'])
            || (float) $form['sale_price'] <= 0
            || (float) $form['sale_price'] >= (float) $form['price']
        )) {
            $errors[] = 'Giá khuyến mãi phải lớn hơn 0 và thấp hơn giá gốc.';
        }
        if (!ctype_digit($form['stock_quantity']) || (float) $form['stock_quantity'] > 4294967295) {
            $errors[] = 'Số lượng tồn kho phải là số nguyên không âm hợp lệ.';
        }
        if (!in_array($form['status'], ['active', 'inactive', 'out_of_stock'], true)) {
            $errors[] = 'Trạng thái sản phẩm không hợp lệ.';
        }
        if (mb_strlen($form['description']) > 60000) {
            $errors[] = 'Mô tả sản phẩm quá dài.';
        }
        if (mb_strlen($form['image_url']) > 500 || !$this->isValidProductImage($form['image_url'])) {
            $errors[] = 'Ảnh cần là URL http/https, mã ảnh Unsplash hoặc ảnh đã tải lên.';
        }

        $uploadedFile = $_FILES['product_image'] ?? null;
        if ($uploadedFile !== null && !is_array($uploadedFile)) {
            $errors[] = 'Tệp ảnh tải lên không hợp lệ.';
            $uploadError = UPLOAD_ERR_NO_FILE;
        } else {
            $uploadError = (int) ($uploadedFile['error'] ?? UPLOAD_ERR_NO_FILE);
        }
        if ($uploadError !== UPLOAD_ERR_NO_FILE && $uploadError !== UPLOAD_ERR_OK) {
            $errors[] = $this->productUploadErrorMessage($uploadError);
        }

        $sizes = array_values(array_filter(array_map(
            'trim',
            explode(',', $form['sizes'])
        ), static fn(string $size): bool => $size !== ''));
        $uniqueSizes = [];
        foreach ($sizes as $size) {
            $uniqueSizes[mb_strtolower($size)] = $size;
        }
        $sizes = array_values($uniqueSizes);
        if (!$sizes || count($sizes) > 20) {
            $errors[] = 'Nhập ít nhất một size và không quá 20 size, phân cách bằng dấu phẩy.';
        } else {
            foreach ($sizes as $size) {
                if (!preg_match('/^[A-Za-z0-9][A-Za-z0-9 -]{0,29}$/', $size)) {
                    $errors[] = 'Size chỉ được chứa chữ cái không dấu, chữ số, dấu cách hoặc dấu gạch ngang.';
                    break;
                }
            }
        }

        if ($errors) {
            $_SESSION['product_errors'] = $errors;
            $_SESSION['product_form'] = $form;
            $this->redirectToProducts($productId);
        }

        $uploadedImagePath = null;
        if ($uploadError === UPLOAD_ERR_OK) {
            try {
                $uploadedImagePath = $this->storeProductImage($uploadedFile);
            } catch (InvalidArgumentException $exception) {
                $_SESSION['product_errors'] = [$exception->getMessage()];
                $_SESSION['product_form'] = $form;
                $this->redirectToProducts($productId);
            }
        }

        $product = [
            'category_id' => (int) $form['category_id'],
            'product_name' => $form['product_name'],
            'description' => $form['description'] !== '' ? $form['description'] : null,
            'price' => $form['price'],
            'sale_price' => $form['sale_price'] !== '' ? $form['sale_price'] : null,
            'stock_quantity' => (int) $form['stock_quantity'],
            'image_url' => $uploadedImagePath ?? ($form['image_url'] !== '' ? $form['image_url'] : null),
            'status' => $form['status'],
        ];
        try {
            $this->products->saveAdminProduct($product, $sizes, $productId > 0 ? $productId : null);
        } catch (Throwable $exception) {
            if ($uploadedImagePath !== null) {
                $this->removeUploadedProductImage($uploadedImagePath);
            }
            throw $exception;
        }
        if ($uploadedImagePath !== null && $existingProduct) {
            $this->removeUploadedProductImage((string) $existingProduct['image_url']);
        }
        $_SESSION['admin_flash'] = $productId > 0 ? 'Đã cập nhật sản phẩm trong database.' : 'Đã thêm sản phẩm vào database.';
        $this->redirectToProducts();
    }

    private function deleteProduct(): void
    {
        $productId = (int) ($_POST['product_id'] ?? 0);
        if ($productId < 1 || !$this->products->findAdminProduct($productId)) {
            $_SESSION['admin_flash'] = 'Không tìm thấy sản phẩm cần xóa.';
            $this->redirectToProducts();
        }

        $product = $this->products->findAdminProduct($productId);
        try {
            $this->products->deleteAdminProduct($productId);
        } catch (PDOException $exception) {
            if ($exception->getCode() !== '23000') {
                throw $exception;
            }
            $_SESSION['admin_flash'] = 'Không thể xóa sản phẩm đã có trong đơn hàng.';
            $this->redirectToProducts();
        }

        $this->removeUploadedProductImage((string) $product['image_url']);
        $_SESSION['admin_flash'] = 'Đã xóa sản phẩm khỏi database.';
        $this->redirectToProducts();
    }

    private function isValidProductImage(string $image): bool
    {
        if ($image === '') {
            return true;
        }
        if (filter_var($image, FILTER_VALIDATE_URL)) {
            $scheme = strtolower((string) parse_url($image, PHP_URL_SCHEME));

            return in_array($scheme, ['http', 'https'], true);
        }

        return preg_match('/^[A-Za-z0-9][A-Za-z0-9_-]{0,150}$/', $image) === 1
            || preg_match('/^assets\/uploads\/products\/[a-f0-9]{32}\.(?:jpg|png|gif|webp)$/', $image) === 1;
    }

    private function productUploadErrorMessage(int $error): string
    {
        if ($error === UPLOAD_ERR_INI_SIZE || $error === UPLOAD_ERR_FORM_SIZE) {
            return 'Ảnh vượt quá giới hạn tải lên của máy chủ (tối đa 5 MB).';
        }
        if ($error === UPLOAD_ERR_PARTIAL) {
            return 'Ảnh tải lên chưa hoàn tất. Vui lòng thử lại.';
        }
        if ($error === UPLOAD_ERR_NO_TMP_DIR || $error === UPLOAD_ERR_CANT_WRITE) {
            return 'Máy chủ chưa thể lưu ảnh tải lên. Vui lòng kiểm tra cấu hình thư mục tạm.';
        }

        return 'Không thể nhận ảnh tải lên. Vui lòng chọn ảnh khác.';
    }

    private function storeProductImage(array $file): string
    {
        $temporaryPath = (string) ($file['tmp_name'] ?? '');
        if (!is_uploaded_file($temporaryPath)) {
            throw new InvalidArgumentException('Tệp ảnh tải lên không hợp lệ.');
        }

        $fileSize = filesize($temporaryPath);
        if ($fileSize === false || $fileSize < 1 || $fileSize > 5 * 1024 * 1024) {
            throw new InvalidArgumentException('Ảnh phải có dung lượng tối đa 5 MB.');
        }

        $fileInfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $fileInfo->file($temporaryPath);
        $imageInfo = @getimagesize($temporaryPath);
        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
        ];
        if (!isset($extensions[$mimeType]) || $imageInfo === false || $imageInfo['mime'] !== $mimeType) {
            throw new InvalidArgumentException('Chỉ chấp nhận ảnh JPEG, PNG, GIF hoặc WebP hợp lệ.');
        }
        if (
            $imageInfo[0] < 1
            || $imageInfo[1] < 1
            || $imageInfo[0] > intdiv(25000000, $imageInfo[1])
        ) {
            throw new InvalidArgumentException('Kích thước ảnh quá lớn.');
        }

        $directory = PATH_ASSETS_UPLOADS . 'products' . DIRECTORY_SEPARATOR;
        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            throw new RuntimeException('Không thể tạo thư mục lưu ảnh sản phẩm: ' . $directory);
        }
        if (!is_writable($directory)) {
            throw new RuntimeException('Thư mục ảnh sản phẩm không có quyền ghi: ' . $directory);
        }

        $filename = bin2hex(random_bytes(16)) . '.' . $extensions[$mimeType];
        if (!move_uploaded_file($temporaryPath, $directory . $filename)) {
            throw new RuntimeException('Không thể lưu ảnh sản phẩm vào thư mục tải lên.');
        }

        return 'assets/uploads/products/' . $filename;
    }

    private function removeUploadedProductImage(string $imagePath): void
    {
        if (!preg_match('/^assets\/uploads\/products\/[a-f0-9]{32}\.(?:jpg|png|gif|webp)$/', $imagePath)) {
            return;
        }

        $filename = basename($imagePath);
        $path = PATH_ASSETS_UPLOADS . 'products' . DIRECTORY_SEPARATOR . $filename;
        if (is_file($path) && !unlink($path)) {
            error_log('Unable to remove replaced product image: ' . $path);
        }
    }

    private function createAccount(): void
    {
        $old = [
            'full_name' => trim((string) ($_POST['full_name'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
            'phone' => trim((string) ($_POST['phone'] ?? '')),
            'role' => (string) ($_POST['role'] ?? 'customer'),
        ];
        $password = (string) ($_POST['password'] ?? '');
        $confirmation = (string) ($_POST['password_confirmation'] ?? '');
        $errors = [];

        if ($old['full_name'] === '' || mb_strlen($old['full_name']) > 150) {
            $errors[] = 'Vui lòng nhập họ tên (tối đa 150 ký tự).';
        }
        if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($old['email']) > 191) {
            $errors[] = 'Địa chỉ email chưa hợp lệ.';
        }
        if ($old['phone'] !== '' && !preg_match('/^[0-9+().\s-]{8,20}$/', $old['phone'])) {
            $errors[] = 'Số điện thoại chưa hợp lệ.';
        }
        if (!in_array($old['role'], ['admin', 'customer'], true)) {
            $errors[] = 'Vai trò tài khoản không hợp lệ.';
        }
        if (strlen($password) < 8) {
            $errors[] = 'Mật khẩu cần có ít nhất 8 ký tự.';
        }
        if ($password !== $confirmation) {
            $errors[] = 'Mật khẩu xác nhận không khớp.';
        }
        if (!$errors && $this->users->findByEmail($old['email'])) {
            $errors[] = 'Email này đã được sử dụng.';
        }

        if ($errors) {
            $_SESSION['account_errors'] = $errors;
            $_SESSION['account_old'] = $old;
            $this->redirectToAccounts();
        }

        try {
            $this->users->createAccount(
                $old['full_name'],
                $old['email'],
                $password,
                $old['phone'],
                $old['role']
            );
        } catch (PDOException $exception) {
            if ($exception->getCode() !== '23000') {
                throw $exception;
            }
            $_SESSION['account_errors'] = ['Email này đã được sử dụng.'];
            $_SESSION['account_old'] = $old;
            $this->redirectToAccounts();
        }

        $_SESSION['admin_flash'] = 'Đã tạo tài khoản ' . ($old['role'] === 'admin' ? 'quản trị viên.' : 'khách hàng.');
        $this->redirectToAccounts();
    }

    private function updateRole(): void
    {
        $userId = (int) ($_POST['user_id'] ?? 0);
        $role = (string) ($_POST['role'] ?? '');
        $account = $userId > 0 ? $this->users->findById($userId) : null;

        if (!$account || !in_array($role, ['admin', 'customer'], true)) {
            $_SESSION['admin_flash'] = 'Tài khoản hoặc vai trò không hợp lệ.';
            $this->redirectToAccounts();
        }
        if ($userId === (int) $_SESSION['user']['user_id']) {
            $_SESSION['admin_flash'] = 'Bạn không thể tự thay đổi vai trò của mình.';
            $this->redirectToAccounts();
        }
        if ($account['role'] === 'admin' && $role === 'customer' && $this->users->countAdmins() <= 1) {
            $_SESSION['admin_flash'] = 'Không thể hạ quyền quản trị viên cuối cùng.';
            $this->redirectToAccounts();
        }

        $this->users->updateRole($userId, $role);
        $_SESSION['admin_flash'] = 'Đã cập nhật vai trò tài khoản.';
        $this->redirectToAccounts();
    }

    private function deleteAccount(): void
    {
        $userId = (int) ($_POST['user_id'] ?? 0);
        $account = $userId > 0 ? $this->users->findById($userId) : null;

        if (!$account) {
            $_SESSION['admin_flash'] = 'Không tìm thấy tài khoản cần xóa.';
            $this->redirectToAccounts();
        }
        if ($userId === (int) $_SESSION['user']['user_id']) {
            $_SESSION['admin_flash'] = 'Bạn không thể xóa tài khoản đang đăng nhập.';
            $this->redirectToAccounts();
        }
        if ($account['role'] === 'admin' && $this->users->countAdmins() <= 1) {
            $_SESSION['admin_flash'] = 'Không thể xóa quản trị viên cuối cùng.';
            $this->redirectToAccounts();
        }

        try {
            $this->users->deleteAccount($userId);
        } catch (PDOException $exception) {
            if ($exception->getCode() !== '23000') {
                throw $exception;
            }
            $_SESSION['admin_flash'] = 'Không thể xóa tài khoản đã phát sinh đơn hàng.';
            $this->redirectToAccounts();
        }

        $_SESSION['admin_flash'] = 'Đã xóa tài khoản.';
        $this->redirectToAccounts();
    }

    private function requireAdmin(): void
    {
        if (empty($_SESSION['user']['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }

        $user = $this->users->findById((int) $_SESSION['user']['user_id']);
        if (!$user) {
            unset($_SESSION['user']);
            header('Location: index.php?page=login');
            exit;
        }
        if ($user['role'] !== 'admin') {
            $_SESSION['user'] = $user;
            http_response_code(403);
            exit('Bạn không có quyền truy cập trang quản trị.');
        }
        $_SESSION['user'] = $user;
    }

    private function csrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    private function validCsrfToken(): bool
    {
        return isset($_POST['csrf_token'], $_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], (string) $_POST['csrf_token']);
    }

    private function takeFlash(): ?string
    {
        $flash = $_SESSION['admin_flash'] ?? null;
        unset($_SESSION['admin_flash']);

        return $flash;
    }

    private function redirectToAccounts(): void
    {
        header('Location: index.php?page=admin-accounts');
        exit;
    }

    private function redirectToProducts(int $productId = 0): void
    {
        $query = $productId > 0 ? '&id=' . $productId : '';
        header('Location: index.php?page=admin-products' . $query);
        exit;
    }
}
