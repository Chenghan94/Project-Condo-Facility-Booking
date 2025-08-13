<?php
// src/Auth.php
require_once __DIR__ . '/../config/db.php';

class Auth {
    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function login(string $email, string $password): bool {
        self::start();

        $pdo = get_pdo();
        // Fetch user by email and ensure account is active
        $stmt = $pdo->prepare("SELECT user_id, user_name, role, password_hash 
                               FROM management_users 
                               WHERE email = ? AND is_active = 1
                               LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id']   = (int)$user['user_id'];
            $_SESSION['user_name'] = $user['user_name'];
            $_SESSION['role']      = $user['role'];
            return true;
        }
        return false;
    }

    public static function check(): bool {
        self::start();
        return isset($_SESSION['user_id']);
    }

    public static function require_login(): void {
        if (!self::check()) {
            header('Location: /Project/public/admin/login.php?msg=Please+login');
            exit;
        }
    }

    public static function logout(): void {
        self::start();
        session_destroy();
    }
}
