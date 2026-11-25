<?php
require_once __DIR__ . '/includes/header.php';
$pdo = db();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = $_POST['action'] ?? '';
    if ($action === 'add' || $action === 'edit') {
        $icon = trim($_POST['icon']) ?: 'fas fa-spa';
        $title = trim($_POST['title']);
        $desc = trim($_POST['description']);
        $sort = (int)($_POST['sort_order'] ?? 0);
        if ($action === 'add') {
            $pdo->prepare('INSERT INTO services (icon, title, description, sort_order) VALUES (?,?,?,?)')
                ->execute([$icon, $title, $desc, $sort]);
        } else {
            $pdo->prepare('UPDATE services SET icon=?, title=?, description=?, sort_order=? WHERE id=?')
                ->execute([$icon, $title, $desc, $sort, (int)$_POST['id']]);
        }
        $msg = 'تم الحفظ بنجاح';
    } elseif ($action === 'delete') {
        $pdo->prepare('DELETE FROM services WHERE id=?')->execute([(int)$_POST['id']]);
        $msg = 'تم الحذف';
    }
}
$services = $pdo->query('SELECT * FROM services ORDER BY sort_order ASC, id ASC')->fetchAll();
?>
<h3 class="mb-4">إدارة الخدمات</h3>
<?php if ($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

<div class="card">
    <div class="card-header">إضافة خدمة جديدة</div>
    <div class="card-body">
        <form method="post" class="row g-3">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="action" value="add">
            <div class="col-md-3">
                <label class="form-label">أيقونة (Font Awesome class)</label>
                <input type="text" name="icon" class="form-control" placeholder="fas fa-syringe">
            </div>
            <div class="col-md-3">
                <label class="form-label">عنوان الخدمة</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">الوصف</label>
                <input type="text" name="description" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label">ترتيب الظهور</label>
                <input type="number" name="sort_order" class="form-control" value="0">
            </div>
            <div class="col-12"><button class="btn btn-gold">إضافة</button></div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">الخدمات الحالية</div>
    <div class="card-body p-0">
        <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th></th><th>العنوان</th><th>الوصف</th><th>ترتيب</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($services as $svc): ?>
                <tr>
                    <td style="width:40px"><i class="<?= e($svc['icon']) ?>"></i></td>
                    <td><?= e($svc['title']) ?></td>
                    <td class="text-muted small"><?= e($svc['description']) ?></td>
                    <td><?= (int)$svc['sort_order'] ?></td>
                    <td class="text-nowrap" style="width:160px">
                        <button type="button" class="btn btn-sm btn-outline-secondary edit-btn"
                            data-id="<?= $svc['id'] ?>" data-icon="<?= e($svc['icon']) ?>"
                            data-title="<?= e($svc['title']) ?>" data-description="<?= e($svc['description']) ?>"
                            data-sort_order="<?= (int)$svc['sort_order'] ?>">تعديل</button>
                        <form method="post" class="d-inline" onsubmit="return confirm('تأكيد حذف الخدمة؟')">
                            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $svc['id'] ?>">
                            <button class="btn btn-sm btn-outline-danger">حذف</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="post" class="modal-content">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
      <input type="hidden" name="action" value="edit">
      <input type="hidden" name="id" id="edit_id">
      <div class="modal-header"><h5 class="modal-title">تعديل الخدمة</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="mb-2"><label class="form-label">أيقونة</label><input type="text" name="icon" id="edit_icon" class="form-control"></div>
        <div class="mb-2"><label class="form-label">العنوان</label><input type="text" name="title" id="edit_title" class="form-control"></div>
        <div class="mb-2"><label class="form-label">الوصف</label><input type="text" name="description" id="edit_description" class="form-control"></div>
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
        document.getElementById('edit_icon').value = btn.dataset.icon;
        document.getElementById('edit_title').value = btn.dataset.title;
        document.getElementById('edit_description').value = btn.dataset.description;
        document.getElementById('edit_sort_order').value = btn.dataset.sort_order;
        new bootstrap.Modal(document.getElementById('editModal')).show();
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
