<?php
require_once __DIR__ . '/db.php';

function current_user(): ?array {
    return $_SESSION['admin_user'] ?? null;
}

function require_login(): void {
    if (!current_user()) {
        header('Location: login.php');
        exit;
    }
}

function attempt_login(string $username, string $password): bool {
    $stmt = db()->prepare('SELECT * FROM admin_users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['admin_user'] = ['id' => $user['id'], 'username' => $user['username']];
        return true;
    }
    return false;
}

function logout(): void {
    unset($_SESSION['admin_user']);
    session_destroy();
}

/** حماية CSRF بسيطة */
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_check(): void {
    $token = $_POST['csrf'] ?? '';
    if (!hash_equals($_SESSION['csrf'] ?? '', $token)) {
        die('طلب غير صالح (CSRF). حدّثي الصفحة وحاولي مجدداً.');
    }
}
