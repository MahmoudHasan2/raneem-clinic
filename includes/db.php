<?php
require_once __DIR__ . '/../config.php';

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            die('تعذر الاتصال بقاعدة البيانات. تأكدي من إعدادات config.php — تفاصيل الخطأ: ' . htmlspecialchars($e->getMessage()));
        }
    }
    return $pdo;
}

/** جلب كل إعدادات/محتوى الموقع كمصفوفة key => value */
function get_settings(): array {
    static $settings = null;
    if ($settings === null) {
        $settings = [];
        $stmt = db()->query('SELECT setting_key, setting_value FROM site_settings');
        foreach ($stmt->fetchAll() as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
    return $settings;
}

function setting(string $key, string $default = ''): string {
    $s = get_settings();
    return $s[$key] ?? $default;
}

function update_setting(string $key, string $value): void {
    $stmt = db()->prepare('INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?)
        ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
    $stmt->execute([$key, $value]);
}

function e(?string $v): string {
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}
