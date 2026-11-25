<?php
require_once __DIR__ . '/../../includes/auth.php';
require_login();
$current = basename($_SERVER['PHP_SELF']);
function nav_active($file, $current) { return $file === $current ? 'active' : ''; }
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>لوحة تحكم عيادة رنيم</title>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="assets/admin.css">
</head>
<body>
<div class="admin-wrap">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-spa"></i> Raneem <span>Dashboard</span>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="<?= nav_active('dashboard.php', $current) ?>"><i class="fas fa-gauge"></i> الرئيسية</a>
            <div class="nav-group-label">محتوى الموقع</div>
            <a href="content.php" class="<?= nav_active('content.php', $current) ?>"><i class="fas fa-file-lines"></i> النصوص والتواصل</a>
            <a href="services.php" class="<?= nav_active('services.php', $current) ?>"><i class="fas fa-list-check"></i> الخدمات</a>
            <a href="gallery.php" class="<?= nav_active('gallery.php', $current) ?>"><i class="fas fa-images"></i> معرض الصور</a>
            <a href="testimonials.php" class="<?= nav_active('testimonials.php', $current) ?>"><i class="fas fa-star"></i> آراء العملاء</a>
            <div class="nav-group-label">الإدارة والمحاسبة</div>
            <a href="customers.php" class="<?= nav_active('customers.php', $current) ?>"><i class="fas fa-users"></i> الزبائن</a>
            <a href="operations.php" class="<?= nav_active('operations.php', $current) ?>"><i class="fas fa-cash-register"></i> عمليات الزبائن</a>
            <a href="employees.php" class="<?= nav_active('employees.php', $current) ?>"><i class="fas fa-id-badge"></i> الموظفين والرواتب</a>
            <a href="expenses.php" class="<?= nav_active('expenses.php', $current) ?>"><i class="fas fa-receipt"></i> مصاريف المواد</a>
            <a href="reports.php" class="<?= nav_active('reports.php', $current) ?>"><i class="fas fa-chart-line"></i> تقرير الأرباح</a>
            <div class="nav-group-label">النظام</div>
            <a href="settings.php" class="<?= nav_active('settings.php', $current) ?>"><i class="fas fa-gear"></i> إعدادات الدخول</a>
            <a href="../index.php" target="_blank"><i class="fas fa-arrow-up-right-from-square"></i> عرض الموقع</a>
            <a href="logout.php" class="text-danger"><i class="fas fa-right-from-bracket"></i> تسجيل الخروج</a>
        </nav>
    </aside>
    <main class="content">
        <div class="topbar">
            <div class="topbar-user"><i class="fas fa-user-circle"></i> مرحباً، <?= e(current_user()['username']) ?></div>
        </div>
        <div class="content-inner">
