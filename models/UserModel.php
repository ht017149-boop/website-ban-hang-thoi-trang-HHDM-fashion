<?php

require_once __DIR__ . '/BaseModel.php';

class UserModel extends BaseModel
{
    public function findByEmail(string $email): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT user_id, full_name, email, password, role FROM users WHERE email = :email LIMIT 1'
        );
        $statement->execute(['email' => mb_strtolower(trim($email))]);
        $user = $statement->fetch();

        return $user ?: null;
    }

    public function createCustomer(string $fullName, string $email, string $password, string $phone = ''): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO users (full_name, email, password, phone, role) '
            . 'VALUES (:full_name, :email, :password, :phone, \'customer\')'
        );
        $statement->execute([
            'full_name' => trim($fullName),
            'email' => mb_strtolower(trim($email)),
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'phone' => trim($phone) !== '' ? trim($phone) : null,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function createAccount(string $fullName, string $email, string $password, string $phone, string $role): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO users (full_name, email, password, phone, role) '
            . 'VALUES (:full_name, :email, :password, :phone, :role)'
        );
        $statement->execute([
            'full_name' => trim($fullName),
            'email' => mb_strtolower(trim($email)),
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'phone' => trim($phone) !== '' ? trim($phone) : null,
            'role' => $role,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function findById(int $userId): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT user_id, full_name, email, phone, role FROM users WHERE user_id = :user_id LIMIT 1'
        );
        $statement->execute(['user_id' => $userId]);
        $user = $statement->fetch();

        return $user ?: null;
    }

    public function getAccounts(): array
    {
        return $this->pdo->query(
            'SELECT user_id, full_name, email, phone, role, created_at FROM users ORDER BY created_at DESC, user_id DESC'
        )->fetchAll();
    }

    public function getLatestAccounts(int $limit = 6): array
    {
        $statement = $this->pdo->prepare(
            'SELECT user_id, full_name, email, role, created_at FROM users ORDER BY created_at DESC, user_id DESC LIMIT :limit'
        );
        $statement->bindValue('limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function getDashboardStats(): array
    {
        return $this->pdo->query(
            "SELECT
                (SELECT COUNT(*) FROM users) AS total_users,
                (SELECT COUNT(*) FROM users WHERE role = 'admin') AS total_admins,
                (SELECT COUNT(*) FROM users WHERE role = 'customer') AS total_customers,
                (SELECT COUNT(*) FROM products) AS total_products,
                (SELECT COUNT(*) FROM categories) AS total_categories,
                (SELECT COUNT(*) FROM orders) AS total_orders,
                (SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status = 'completed') AS completed_revenue"
        )->fetch();
    }

    public function countAdmins(): int
    {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
    }

    public function updateRole(int $userId, string $role): void
    {
        $statement = $this->pdo->prepare('UPDATE users SET role = :role WHERE user_id = :user_id');
        $statement->execute(['role' => $role, 'user_id' => $userId]);
    }

    public function deleteAccount(int $userId): void
    {
        $statement = $this->pdo->prepare('DELETE FROM users WHERE user_id = :user_id');
        $statement->execute(['user_id' => $userId]);
    }

    public function updatePasswordHash(int $userId, string $password): void
    {
        $statement = $this->pdo->prepare('UPDATE users SET password = :password WHERE user_id = :user_id');
        $statement->execute([
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'user_id' => $userId,
        ]);
    }
}
