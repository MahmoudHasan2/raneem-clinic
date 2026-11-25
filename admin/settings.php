<?php
require_once __DIR__ . '/includes/header.php';
$pdo = db();
$msg = '';
$error = '';
$user = current_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $currentPassword = $_POST['current_password'] ?? '';
    $newUsername = trim($_POST['username'] ?? '');
    $newPassword = $_POST['new_password'] ?? '';
    $newPassword2 = $_POST['new_password_confirm'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM admin_users WHERE id = ?');
    $stmt->execute([$user['id']]);
    $row = $stmt->fetch();

    if (!$row || !password_verify($currentPassword, $row['password_hash'])) {
        $error = 'كلمة السر الحالية غير صحيحة';
    } elseif ($newUsername === '') {
        $error = 'اسم المستخدم لا يمكن أن يكون فارغاً';
    } elseif ($newPassword !== '' && $newPassword !== $newPassword2) {
        $error = 'كلمة السر الجديدة وتأكيدها غير متطابقين';
    } elseif ($newPassword !== '' && strlen($newPassword) < 6) {
        $error = 'كلمة السر الجديدة يجب أن تكون 6 أحرف على الأقل';
    } else {
        if ($newPassword !== '') {
            $pdo->prepare('UPDATE admin_users SET username=?, password_hash=? WHERE id=?')
                ->execute([$newUsername, password_hash($newPassword, PASSWORD_BCRYPT), $user['id']]);
        } else {
            $pdo->prepare('UPDATE admin_users SET username=? WHERE id=?')->execute([$newUsername, $user['id']]);
        }
        $_SESSION['admin_user']['username'] = $newUsername;
        $user = current_user();
        $msg = 'تم تحديث بيانات الدخول بنجاح';
    }
}
?>
<h3 class="mb-4">إعدادات الدخول للوحة التحكم</h3>
<?php if ($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>

<div class="card" style="max-width: 500px">
    <div class="card-header">تغيير اسم المستخدم / كلمة السر</div>
    <div class="card-body">
        <form method="post">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <div class="mb-3">
                <label class="form-label">اسم المستخدم الحالي</label>
                <input type="text" name="username" class="form-control" value="<?= e($user['username']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">كلمة السر الحالية (مطلوبة للتأكيد)</label>
                <input type="password" name="current_password" class="form-control" required>
            </div>
            <hr>
            <div class="mb-3">
                <label class="form-label">كلمة السر الجديدة (اتركيها فارغة إن لم ترغبي بتغييرها)</label>
                <input type="password" name="new_password" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">تأكيد كلمة السر الجديدة</label>
                <input type="password" name="new_password_confirm" class="form-control">
            </div>
            <button class="btn btn-gold">حفظ التعديلات</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
