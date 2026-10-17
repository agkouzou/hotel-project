<?php
declare(strict_types=1);

namespace App\Hotel;

use App\Support\Db;

class UserService
{
    public static function current(): ?array
    {
        if (empty($_SESSION['user_id'])) {
            return null;
        }
        return Db::one(
            'SELECT user_id, name, email FROM user WHERE user_id = :id',
            ['id' => (int) $_SESSION['user_id']]
        );
    }

    public static function emailExists(string $email): bool
    {
        return Db::one('SELECT 1 FROM user WHERE email = :email', ['email' => $email]) !== null;
    }

    public static function register(string $name, string $email, string $password): int
    {
        Db::query(
            'INSERT INTO user (name, email, password) VALUES (:name, :email, :password)',
            [
                'name' => $name,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
            ]
        );
        return (int) Db::pdo()->lastInsertId();
    }

    /** Returns the user id on success, null on bad credentials. */
    public static function authenticate(string $email, string $password): ?int
    {
        $user = Db::one('SELECT user_id, password FROM user WHERE email = :email', ['email' => $email]);
        if ($user === null || !password_verify($password, $user['password'])) {
            return null;
        }
        return (int) $user['user_id'];
    }

    public static function login(int $userId): void
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $userId;
    }

    public static function logout(): void
    {
        $_SESSION = [];
        session_regenerate_id(true);
    }
}
