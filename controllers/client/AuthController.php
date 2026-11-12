<?php

require_once PATH_MODEL . 'UserModel.php';

class AuthController
{
    private UserModel $users;

    public function __construct()
    {
        $this->users = new UserModel();
    }

    public function register(): void
    {
        if (!empty($_SESSION['user'])) {
            $this->redirectHome();
        }

        $errors = [];
        $old = ['full_name' => '', 'email' => '', 'phone' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old = [
                'full_name' => trim((string) ($_POST['full_name'] ?? '')),
                'email' => trim((string) ($_POST['email'] ?? '')),
                'phone' => trim((string) ($_POST['phone'] ?? '')),
            ];
            $password = (string) ($_POST['password'] ?? '');
            $confirm = (string) ($_POST['password_confirmation'] ?? '');

            if (!$this->validCsrfToken()) {
                $errors[] = 'Phiên đăng ký đã hết hạn. Vui lòng thử lại.';
            }
            if ($old['full_name'] === '' || mb_strlen($old['full_name']) > 150) {
                $errors[] = 'Vui lòng nhập họ tên (tối đa 150 ký tự).';
            }
            if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($old['email']) > 191) {
                $errors[] = 'Địa chỉ email chưa hợp lệ.';
            }
            if ($old['phone'] !== '' && !preg_match('/^[0-9+().\s-]{8,20}$/', $old['phone'])) {
                $errors[] = 'Số điện thoại chưa hợp lệ.';
            }
            if (strlen($password) < 8) {
                $errors[] = 'Mật khẩu cần có ít nhất 8 ký tự.';
            }
            if ($password !== $confirm) {
                $errors[] = 'Mật khẩu xác nhận không khớp.';
            }

            if (!$errors && $this->users->findByEmail($old['email'])) {
                $errors[] = 'Email này đã được đăng ký.';
            }

            if (!$errors) {
                try {
                    $userId = $this->users->createCustomer($old['full_name'], $old['email'], $password, $old['phone']);
                    session_regenerate_id(true);
                    $_SESSION['user'] = [
                        'user_id' => $userId,
                        'full_name' => $old['full_name'],
                        'email' => mb_strtolower($old['email']),
                        'role' => 'customer',
                    ];
                    $this->redirectHome();
                } catch (PDOException $exception) {
                    if ($exception->getCode() === '23000') {
                        $errors[] = 'Email này đã được đăng ký.';
                    } else {
                        $errors[] = 'Chưa thể tạo tài khoản lúc này. Vui lòng thử lại sau.';
                    }
                }
            }
        }

        $csrfToken = $this->csrfToken();
        require PATH_VIEW_CLIENT . 'auth/register.php';
    }

    public function login(): void
    {
        if (!empty($_SESSION['user'])) {
            $this->redirectHome();
        }

        $errors = [];
        $email = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim((string) ($_POST['email'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');

            if (!$this->validCsrfToken()) {
                $errors[] = 'Phiên đăng nhập đã hết hạn. Vui lòng thử lại.';
            } else {
                $user = $this->users->findByEmail($email);
                if (!$user || !password_verify($password, $user['password'])) {
                    $errors[] = 'Email hoặc mật khẩu chưa chính xác.';
                } else {
                    if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
                        $this->users->updatePasswordHash((int) $user['user_id'], $password);
                    }
                    session_regenerate_id(true);
                    $_SESSION['user'] = [
                        'user_id' => (int) $user['user_id'],
                        'full_name' => $user['full_name'],
                        'email' => $user['email'],
                        'role' => $user['role'],
                    ];
                    $this->redirectHome();
                }
            }
        }

        $csrfToken = $this->csrfToken();
        require PATH_VIEW_CLIENT . 'auth/login.php';
    }

    public function logout(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->validCsrfToken()) {
            http_response_code(405);
            exit('Yêu cầu không hợp lệ.');
        }

        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $cookie = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $cookie['path'], $cookie['domain'], $cookie['secure'], $cookie['httponly']);
        }
        session_destroy();
        $this->redirectHome();
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

    private function redirectHome(): void
    {
        header('Location: index.php');
        exit;
    }
}
