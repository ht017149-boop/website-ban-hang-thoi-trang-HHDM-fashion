<?php

require_once PATH_MODEL . 'UserModel.php';

class AdminController
{
    private UserModel $users;

    public function __construct()
    {
        $this->users = new UserModel();
    }

    public function handle(): void
    {
        $this->requireAdmin();
        $csrfToken = $this->csrfToken();
        $page = (string) ($_GET['page'] ?? 'admin');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleAccountAction();
        }

        if ($page === 'admin-accounts') {
            $this->showAccounts();
            return;
        }

        $stats = $this->users->getDashboardStats();
        $latestAccounts = $this->users->getLatestAccounts();
        $currentPage = 'dashboard';
        $flash = $this->takeFlash();
        $contentView = PATH_VIEW_ADMIN . 'dashboard.php';
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
}
