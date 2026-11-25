<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/uploads.php';
$pdo = db();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = $_POST['action'] ?? '';
    try {
        $name = trim($_POST['name'] ?? '');
        $role = trim($_POST['role'] ?? '');
        $text = trim($_POST['testimonial_text'] ?? '');
        $stars = max(1, min(5, (int)($_POST['stars'] ?? 5)));
        $sort = (int)($_POST['sort_order'] ?? 0);
        if ($action === 'add') {
            $avatar = handle_image_upload('avatar_file', 'testimonials') ?: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=200&q=80';
            $pdo->prepare('INSERT INTO testimonials (name, role, testimonial_text, stars, avatar_path, sort_order) VALUES (?,?,?,?,?,?)')
                ->execute([$name, $role, $text, $stars, $avatar, $sort]);
            $msg = 'تمت الإضافة';
        } elseif ($action === 'edit') {
            $id = (int)$_POST['id'];
            $newAvatar = handle_image_upload('avatar_file', 'testimonials');
            if ($newAvatar) {
                $pdo->prepare('UPDATE testimonials SET name=?, role=?, testimonial_text=?, stars=?, avatar_path=?, sort_order=? WHERE id=?')
                    ->execute([$name, $role, $text, $stars, $newAvatar, $sort, $id]);
            } else {
                $pdo->prepare('UPDATE testimonials SET name=?, role=?, testimonial_text=?, stars=?, sort_order=? WHERE id=?')
                    ->execute([$name, $role, $text, $stars, $sort, $id]);
            }
            $msg = 'تم الحفظ';
        } elseif ($action === 'delete') {
            $pdo->prepare('DELETE FROM testimonials WHERE id=?')->execute([(int)$_POST['id']]);
            $msg = 'تم الحذف';
        }
    } catch (RuntimeException $ex) {
        $msg = $ex->getMessage();
    }
}
$items = $pdo->query('SELECT * FROM testimonials ORDER BY sort_order ASC, id ASC')->fetchAll();
?>
<h3 class="mb-4">آراء العملاء</h3>
<?php if ($msg): ?><div class="alert alert-info"><?= e($msg) ?></div><?php endif; ?>

<div class="card">
    <div class="card-header">إضافة رأي جديد</div>
    <div class="card-body">
        <form method="post" enctype="multipart/form-data" class="row g-3">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="action" value="add">
            <div class="col-md-3"><label class="form-label">اسم العميلة</label><input type="text" name="name" class="form-control" required></div>
            <div class="col-md-3"><label class="form-label">الصفة (عميلة دائمة...)</label><input type="text" name="role" class="form-control"></div>
            <div class="col-md-2"><label class="form-label">عدد النجوم</label><input type="number" name="stars" class="form-control" min="1" max="5" value="5"></div>
            <div class="col-md-2"><label class="form-label">الترتيب</label><input type="number" name="sort_order" class="form-control" value="0"></div>
            <div class="col-md-2"><label class="form-label">صورة العميلة</label><input type="file" name="avatar_file" class="form-control" accept="image/*"></div>
            <div class="col-12"><label class="form-label">نص الرأي</label><textarea name="testimonial_text" class="form-control" rows="2" required></textarea></div>
            <div class="col-12"><button class="btn btn-gold">إضافة</button></div>
        </form>
    </div>
</div>

<div class="row g-3">
    <?php foreach ($items as $t): ?>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <img src="<?= e($t['avatar_path']) ?>" style="width:50px;height:50px;border-radius:50%;object-fit:cover">
                    <div>
                        <div class="fw-bold"><?= e($t['name']) ?></div>
                        <div class="text-muted small"><?= e($t['role']) ?></div>
                    </div>
                </div>
                <div class="text-warning mb-2"><?php for ($k=0;$k<(int)$t['stars'];$k++): ?><i class="fas fa-star"></i><?php endfor; ?></div>
                <p class="small"><?= e($t['testimonial_text']) ?></p>
                <button type="button" class="btn btn-sm btn-outline-secondary edit-btn w-100 mb-1"
                    data-id="<?= $t['id'] ?>" data-name="<?= e($t['name']) ?>" data-role="<?= e($t['role']) ?>"
                    data-testimonial_text="<?= e($t['testimonial_text']) ?>" data-stars="<?= (int)$t['stars'] ?>" data-sort_order="<?= (int)$t['sort_order'] ?>">تعديل</button>
                <form method="post" onsubmit="return confirm('تأكيد الحذف؟')">
                    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $t['id'] ?>">
                    <button class="btn btn-sm btn-outline-danger w-100">حذف</button>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="post" enctype="multipart/form-data" class="modal-content">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
      <input type="hidden" name="action" value="edit">
      <input type="hidden" name="id" id="edit_id">
      <div class="modal-header"><h5 class="modal-title">تعديل الرأي</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="mb-2"><label class="form-label">اسم العميلة</label><input type="text" name="name" id="edit_name" class="form-control"></div>
        <div class="mb-2"><label class="form-label">الصفة</label><input type="text" name="role" id="edit_role" class="form-control"></div>
        <div class="mb-2"><label class="form-label">عدد النجوم</label><input type="number" name="stars" id="edit_stars" class="form-control" min="1" max="5"></div>
        <div class="mb-2"><label class="form-label">الترتيب</label><input type="number" name="sort_order" id="edit_sort_order" class="form-control"></div>
        <div class="mb-2"><label class="form-label">استبدال الصورة (اختياري)</label><input type="file" name="avatar_file" class="form-control" accept="image/*"></div>
        <div class="mb-2"><label class="form-label">النص</label><textarea name="testimonial_text" id="edit_testimonial_text" class="form-control" rows="2"></textarea></div>
      </div>
      <div class="modal-footer"><button class="btn btn-gold">حفظ التعديلات</button></div>
    </form>
  </div>
</div>

<script>
document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('edit_id').value = btn.dataset.id;
        document.getElementById('edit_name').value = btn.dataset.name;
        document.getElementById('edit_role').value = btn.dataset.role;
        document.getElementById('edit_stars').value = btn.dataset.stars;
        document.getElementById('edit_sort_order').value = btn.dataset.sort_order;
        document.getElementById('edit_testimonial_text').value = btn.dataset.testimonial_text;
        new bootstrap.Modal(document.getElementById('editModal')).show();
    });
});
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
