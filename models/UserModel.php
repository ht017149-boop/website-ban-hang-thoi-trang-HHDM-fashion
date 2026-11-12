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

    public function updatePasswordHash(int $userId, string $password): void
    {
        $statement = $this->pdo->prepare('UPDATE users SET password = :password WHERE user_id = :user_id');
        $statement->execute([
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'user_id' => $userId,
        ]);
    }
}
