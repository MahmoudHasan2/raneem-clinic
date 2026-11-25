<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/uploads.php';
$pdo = db();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'add') {
            $path = handle_image_upload('image_file', 'gallery');
            if (!$path) { throw new RuntimeException('يرجى اختيار صورة'); }
            $pdo->prepare('INSERT INTO gallery (image_path, title, subtitle, sort_order) VALUES (?,?,?,?)')
                ->execute([$path, trim($_POST['title']), trim($_POST['subtitle']), (int)($_POST['sort_order'] ?? 0)]);
            $msg = 'تمت الإضافة';
        } elseif ($action === 'edit') {
            $id = (int)$_POST['id'];
            $newPath = handle_image_upload('image_file', 'gallery');
            if ($newPath) {
                $pdo->prepare('UPDATE gallery SET image_path=?, title=?, subtitle=?, sort_order=? WHERE id=?')
                    ->execute([$newPath, trim($_POST['title']), trim($_POST['subtitle']), (int)($_POST['sort_order'] ?? 0), $id]);
            } else {
                $pdo->prepare('UPDATE gallery SET title=?, subtitle=?, sort_order=? WHERE id=?')
                    ->execute([trim($_POST['title']), trim($_POST['subtitle']), (int)($_POST['sort_order'] ?? 0), $id]);
            }
            $msg = 'تم الحفظ';
        } elseif ($action === 'delete') {
            $pdo->prepare('DELETE FROM gallery WHERE id=?')->execute([(int)$_POST['id']]);
            $msg = 'تم الحذف';
        }
    } catch (RuntimeException $ex) {
        $msg = $ex->getMessage();
    }
}
$items = $pdo->query('SELECT * FROM gallery ORDER BY sort_order ASC, id ASC')->fetchAll();
?>
<h3 class="mb-4">معرض الصور</h3>
<?php if ($msg): ?><div class="alert alert-info"><?= e($msg) ?></div><?php endif; ?>

<div class="card">
    <div class="card-header">إضافة صورة جديدة</div>
    <div class="card-body">
        <form method="post" enctype="multipart/form-data" class="row g-3">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="action" value="add">
            <div class="col-md-3">
                <label class="form-label">الصورة</label>
                <input type="file" name="image_file" class="form-control" accept="image/*" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">العنوان</label>
                <input type="text" name="title" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">وصف فرعي</label>
                <input type="text" name="subtitle" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label">الترتيب</label>
                <input type="number" name="sort_order" class="form-control" value="0">
            </div>
            <div class="col-12"><button class="btn btn-gold">إضافة</button></div>
        </form>
    </div>
</div>

<div class="row g-3">
    <?php foreach ($items as $g): ?>
    <div class="col-6 col-md-3">
        <div class="card h-100">
            <img src="../<?= e($g['image_path']) ?>" class="card-img-top" style="height:150px;object-fit:cover;border-radius:16px 16px 0 0">
            <div class="card-body">
                <div class="fw-bold small"><?= e($g['title']) ?></div>
                <div class="text-muted small mb-2"><?= e($g['subtitle']) ?></div>
                <button type="button" class="btn btn-sm btn-outline-secondary edit-btn w-100 mb-1"
                    data-id="<?= $g['id'] ?>" data-title="<?= e($g['title']) ?>" data-subtitle="<?= e($g['subtitle']) ?>" data-sort_order="<?= (int)$g['sort_order'] ?>">تعديل</button>
                <form method="post" onsubmit="return confirm('تأكيد حذف الصورة؟')">
                    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $g['id'] ?>">
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
      <div class="modal-header"><h5 class="modal-title">تعديل الصورة</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="mb-2"><label class="form-label">استبدال الصورة (اختياري)</label><input type="file" name="image_file" class="form-control" accept="image/*"></div>
        <div class="mb-2"><label class="form-label">العنوان</label><input type="text" name="title" id="edit_title" class="form-control"></div>
        <div class="mb-2"><label class="form-label">وصف فرعي</label><input type="text" name="subtitle" id="edit_subtitle" class="form-control"></div>
        <div class="mb-2"><label class="form-label">الترتيب</label><input type="number" name="sort_order" id="edit_sort_order" class="form-control"></div>
      </div>
      <div class="modal-footer"><button class="btn btn-gold">حفظ التعديلات</button></div>
    </form>
  </div>
</div>

<script>
document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('edit_id').value = btn.dataset.id;
        document.getElementById('edit_title').value = btn.dataset.title;
        document.getElementById('edit_subtitle').value = btn.dataset.subtitle;
        document.getElementById('edit_sort_order').value = btn.dataset.sort_order;
        new bootstrap.Modal(document.getElementById('editModal')).show();
    });
});
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
