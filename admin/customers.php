<?php
require_once __DIR__ . '/includes/header.php';
$pdo = db();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $pdo->prepare('INSERT INTO customers (name, phone, notes) VALUES (?,?,?)')
            ->execute([trim($_POST['name']), trim($_POST['phone']), trim($_POST['notes'])]);
        $msg = 'تمت إضافة الزبونة';
    } elseif ($action === 'edit') {
        $pdo->prepare('UPDATE customers SET name=?, phone=?, notes=? WHERE id=?')
            ->execute([trim($_POST['name']), trim($_POST['phone']), trim($_POST['notes']), (int)$_POST['id']]);
        $msg = 'تم الحفظ';
    } elseif ($action === 'delete') {
        $pdo->prepare('DELETE FROM customers WHERE id=?')->execute([(int)$_POST['id']]);
        $msg = 'تم الحذف';
    }
}

$search = trim($_GET['q'] ?? '');
if ($search !== '') {
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE name LIKE ? OR phone LIKE ? ORDER BY id DESC");
    $like = '%' . $search . '%';
    $stmt->execute([$like, $like]);
} else {
    $stmt = $pdo->query('SELECT * FROM customers ORDER BY id DESC');
}
$customers = $stmt->fetchAll();

// إجمالي ما أنفقته كل زبونة (لإظهاره في الجدول)
$totalsStmt = $pdo->query('SELECT customer_id, COALESCE(SUM(price),0) total, COUNT(*) visits FROM operations GROUP BY customer_id');
$totals = [];
foreach ($totalsStmt->fetchAll() as $row) { $totals[$row['customer_id']] = $row; }
?>
<h3 class="mb-4">الزبائن</h3>
<?php if ($msg): ?><div class="alert alert-info"><?= e($msg) ?></div><?php endif; ?>

<div class="card">
    <div class="card-header">إضافة زبونة جديدة</div>
    <div class="card-body">
        <form method="post" class="row g-3">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="action" value="add">
            <div class="col-md-4"><label class="form-label">الاسم</label><input type="text" name="name" class="form-control" required></div>
            <div class="col-md-3"><label class="form-label">رقم الهاتف</label><input type="text" name="phone" class="form-control"></div>
            <div class="col-md-5"><label class="form-label">ملاحظات</label><input type="text" name="notes" class="form-control"></div>
            <div class="col-12"><button class="btn btn-gold">إضافة</button></div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>قائمة الزبائن</span>
        <form method="get" class="d-flex gap-2">
            <input type="text" name="q" class="form-control form-control-sm" placeholder="بحث بالاسم أو الهاتف" value="<?= e($search) ?>">
            <button class="btn btn-sm btn-outline-secondary">بحث</button>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>الاسم</th><th>الهاتف</th><th>ملاحظات</th><th>عدد الزيارات</th><th>إجمالي المصروف</th><th></th></tr></thead>
            <tbody>
            <?php if (!$customers): ?><tr><td colspan="6" class="text-center text-muted py-4">لا يوجد زبائن</td></tr><?php endif; ?>
            <?php foreach ($customers as $c): $t = $totals[$c['id']] ?? ['total'=>0,'visits'=>0]; ?>
                <tr>
                    <td><?= e($c['name']) ?></td>
                    <td><?= e($c['phone']) ?></td>
                    <td class="text-muted small"><?= e($c['notes']) ?></td>
                    <td><?= (int)$t['visits'] ?></td>
                    <td><?= number_format((float)$t['total'], 0) ?></td>
                    <td class="text-nowrap" style="width:170px">
                        <button type="button" class="btn btn-sm btn-outline-secondary edit-btn"
                            data-id="<?= $c['id'] ?>" data-name="<?= e($c['name']) ?>" data-phone="<?= e($c['phone']) ?>" data-notes="<?= e($c['notes']) ?>">تعديل</button>
                        <form method="post" class="d-inline" onsubmit="return confirm('تأكيد حذف الزبونة؟ سيتم حذف عملياتها المرتبطة أيضاً.')">
                            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $c['id'] ?>">
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
      <div class="modal-header"><h5 class="modal-title">تعديل بيانات الزبونة</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="mb-2"><label class="form-label">الاسم</label><input type="text" name="name" id="edit_name" class="form-control"></div>
        <div class="mb-2"><label class="form-label">الهاتف</label><input type="text" name="phone" id="edit_phone" class="form-control"></div>
        <div class="mb-2"><label class="form-label">ملاحظات</label><input type="text" name="notes" id="edit_notes" class="form-control"></div>
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
        document.getElementById('edit_phone').value = btn.dataset.phone;
        document.getElementById('edit_notes').value = btn.dataset.notes;
        new bootstrap.Modal(document.getElementById('editModal')).show();
    });
});
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
