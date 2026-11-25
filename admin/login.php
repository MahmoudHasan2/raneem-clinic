<?php
require_once __DIR__ . '/../includes/auth.php';
if (current_user()) {
    header('Location: dashboard.php');
    exit;
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if (attempt_login($username, $password)) {
        header('Location: dashboard.php');
        exit;
    }
    $error = 'اسم المستخدم أو كلمة السر غير صحيحة';
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>تسجيل الدخول | لوحة تحكم عيادة رنيم</title>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
body { font-family: 'Cairo', sans-serif; background: linear-gradient(135deg,#2C2416,#5C4D3A); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
.login-card { background: #FAF7F2; border-radius: 20px; padding: 40px; width: 100%; max-width: 380px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
.login-card h1 { font-size: 1.3rem; font-weight: 800; color: #C5A059; text-align: center; margin-bottom: 6px; }
.login-card p.sub { text-align: center; color: #8B7D6B; font-size: 0.85rem; margin-bottom: 25px; }
.btn-gold { background: #C5A059; border-color: #C5A059; color: #fff; font-weight: 700; }
.btn-gold:hover { background: #9A7B3D; border-color: #9A7B3D; color: #fff; }
</style>
</head>
<body>
<div class="login-card">
    <h1><i class="fas fa-spa"></i> Raneem Clinic</h1>
    <p class="sub">تسجيل الدخول إلى لوحة التحكم</p>
    <?php if ($error): ?>
        <div class="alert alert-danger py-2"><?= e($error) ?></div>
    <?php endif; ?>
    <form method="post">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <div class="mb-3">
            <label class="form-label">اسم المستخدم</label>
            <input type="text" name="username" class="form-control" required autofocus>
        </div>
        <div class="mb-4">
            <label class="form-label">كلمة السر</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-gold w-100">دخول</button>
    </form>
</div>
</body>
</html>
