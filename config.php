<?php
/**
 * إعدادات الاتصال بقاعدة البيانات
 * عدّلي القيم التالية بمعلومات قاعدة البيانات من لوحة تحكم Hostinger (hPanel > Databases)
 */
define('DB_HOST', 'localhost');       // غالباً تبقى localhost في Hostinger
define('DB_NAME', 'your_database'); // اسم قاعدة البيانات
define('DB_USER', 'your_db_user');  // اسم مستخدم قاعدة البيانات
define('DB_PASS', 'your_db_password');         // كلمة سر قاعدة البيانات

// رابط الموقع بدون / في النهاية (مثال: https://raneemclinic.com)
define('SITE_URL', 'https://example.com');

// مسار مجلد الرفع (لا تغيّريه إلا إذا لزم الأمر)
define('UPLOAD_DIR', __DIR__ . '/uploads');
define('UPLOAD_URL', 'uploads');

session_start();
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
date_default_timezone_set('Asia/Damascus');
