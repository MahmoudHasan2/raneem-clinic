<?php
require_once __DIR__ . '/includes/header.php';
$pdo = db();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = $_POST['action'] ?? '';
    if ($action === 'add_employee') {
        $pdo->prepare('INSERT INTO employees (name, position, phone, monthly_salary, hire_date, status, notes) VALUES (?,?,?,?,?,?,?)')
            ->execute([trim($_POST['name']), trim($_POST['position']), trim($_POST['phone']), (float)$_POST['monthly_salary'],
                $_POST['hire_date'] ?: null, $_POST['status'], trim($_POST['notes'])]);
        $msg = 'تمت إضافة الموظفة';
    } elseif ($action === 'edit_employee') {
        $pdo->prepare('UPDATE employees SET name=?, position=?, phone=?, monthly_salary=?, hire_date=?, status=?, notes=? WHERE id=?')
            ->execute([trim($_POST['name']), trim($_POST['position']), trim($_POST['phone']), (float)$_POST['monthly_salary'],
                $_POST['hire_date'] ?: null, $_POST['status'], trim($_POST['notes']), (int)$_POST['id']]);
        $msg = 'تم الحفظ';
    } elseif ($action === 'delete_employee') {
        $pdo->prepare('DELETE FROM employees WHERE id=?')->execute([(int)$_POST['id']]);
        $msg = 'تم الحذف';
    } elseif ($action === 'add_payment') {
        $pdo->prepare('INSERT INTO salary_payments (employee_id, amount, pay_month, paid_date, notes) VALUES (?,?,?,?,?)')
            ->execute([(int)$_POST['employee_id'], (float)$_POST['amount'], $_POST['pay_month'], $_POST['paid_date'], trim($_POST['notes'] ?? '')]);
        $msg = 'تم تسجيل صرف الراتب';
    } elseif ($action === 'delete_payment') {
        $pdo->prepare('DELETE FROM salary_payments WHERE id=?')->execute([(int)$_POST['id']]);
        $msg = 'تم حذف سجل الراتب';
    }
}

$employees = $pdo->query('SELECT * FROM employees ORDER BY status ASC, name ASC')->fetchAll();
$payments = $pdo->query('SELECT sp.*, e.name AS employee_name FROM salary_payments sp JOIN employees e ON e.id = sp.employee_id ORDER BY sp.paid_date DESC LIMIT 50')->fetchAll();
?>
<h3 class="mb-4">الموظفين والرواتب</h3>
<?php if ($msg): ?><div class="alert alert-info"><?= e($msg) ?></div><?php endif; ?>

<div class="card">
    <div class="card-header">إضافة موظفة جديدة</div>
    <div class="card-body">
        <form method="post" class="row g-3">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="action" value="add_employee">
            <div class="col-md-3"><label class="form-label">الاسم</label><input type="text" name="name" class="form-control" required></div>
            <div class="col-md-3"><label class="form-label">الوظيفة</label><input type="text" name="position" class="form-control" placeholder="فنية تجميل / استقبال..."></div>
            <div class="col-md-2"><label class="form-label">الهاتف</label><input type="text" name="phone" class="form-control"></div>
            <div class="col-md-2"><label class="form-label">الراتب الشهري</label><input type="number" step="0.01" name="monthly_salary" class="form-control"></div>
            <div class="col-md-2"><label class="form-label">تاريخ التعيين</label><input type="date" name="hire_date" class="form-control"></div>
            <div class="col-md-3">
                <label class="form-label">الحالة</label>
                <select name="status" class="form-select"><option value="active">تعمل حالياً</option><option value="inactive">متوقفة</option></select>
            </div>
            <div class="col-md-9"><label class="form-label">ملاحظات</label><input type="text" name="notes" class="form-control"></div>
            <div class="col-12"><button class="btn btn-gold">إضافة</button></div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">قائمة الموظفين</div>
    <div class="card-body p-0">
        <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>الاسم</th><th>الوظيفة</th><th>الهاتف</th><th>الراتب الشهري</th><th>الحالة</th><th></th></tr></thead>
            <tbody>
            <?php if (!$employees): ?><tr><td colspan="6" class="text-center text-muted py-4">لا يوجد موظفون</td></tr><?php endif; ?>
            <?php foreach ($employees as $emp): ?>
                <tr>
                    <td><?= e($emp['name']) ?></td>
                    <td><?= e($emp['position']) ?></td>
                    <td><?= e($emp['phone']) ?></td>
                    <td><?= number_format($emp['monthly_salary'], 0) ?></td>
                    <td><?= $emp['status']==='active' ? '<span class="badge bg-success">تعمل</span>' : '<span class="badge bg-secondary">متوقفة</span>' ?></td>
                    <td class="text-nowrap">
                        <button type="button" class="btn btn-sm btn-outline-secondary edit-btn"
                            data-id="<?= $emp['id'] ?>" data-name="<?= e($emp['name']) ?>" data-position="<?= e($emp['position']) ?>"
                            data-phone="<?= e($emp['phone']) ?>" data-monthly_salary="<?= $emp['monthly_salary'] ?>"
                            data-hire_date="<?= e($emp['hire_date']) ?>" data-status="<?= e($emp['status']) ?>" data-notes="<?= e($emp['notes']) ?>">تعديل</button>
                        <form method="post" class="d-inline" onsubmit="return confirm('تأكيد الحذف؟')">
                            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                            <input type="hidden" name="action" value="delete_employee">
                            <input type="hidden" name="id" value="<?= $emp['id'] ?>">
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

<div class="card">
    <div class="card-header">تسجيل صرف راتب</div>
    <div class="card-body">
        <form method="post" class="row g-3">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="action" value="add_payment">
            <div class="col-md-4">
                <label class="form-label">الموظفة</label>
                <select name="employee_id" class="form-select" required>
                    <option value="">اختاري...</option>
                    <?php foreach ($employees as $emp): ?><option value="<?= $emp['id'] ?>"><?= e($emp['name']) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2"><label class="form-label">المبلغ</label><input type="number" step="0.01" name="amount" class="form-control" required></div>
            <div class="col-md-2"><label class="form-label">شهر الراتب</label><input type="month" name="pay_month" class="form-control" value="<?= date('Y-m') ?>" required></div>
            <div class="col-md-2"><label class="form-label">تاريخ الصرف</label><input type="date" name="paid_date" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
            <div class="col-md-2"><label class="form-label">ملاحظات</label><input type="text" name="notes" class="form-control"></div>
            <div class="col-12"><button class="btn btn-gold">تسجيل الصرف</button></div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">آخر عمليات صرف الرواتب</div>
    <div class="card-body p-0">
        <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>الموظفة</th><th>الشهر</th><th>تاريخ الصرف</th><th>المبلغ</th><th>ملاحظات</th><th></th></tr></thead>
            <tbody>
            <?php if (!$payments): ?><tr><td colspan="6" class="text-center text-muted py-4">لا توجد سجلات</td></tr><?php endif; ?>
            <?php foreach ($payments as $p): ?>
                <tr>
                    <td><?= e($p['employee_name']) ?></td>
                    <td><?= e($p['pay_month']) ?></td>
                    <td><?= e($p['paid_date']) ?></td>
                    <td><?= number_format($p['amount'], 0) ?></td>
                    <td class="text-muted small"><?= e($p['notes']) ?></td>
                    <td>
                        <form method="post" onsubmit="return confirm('تأكيد حذف السجل؟')">
                            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                            <input type="hidden" name="action" value="delete_payment">
                            <input type="hidden" name="id" value="<?= $p['id'] ?>">
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
      <input type="hidden" name="action" value="edit_employee">
      <input type="hidden" name="id" id="edit_id">
      <div class="modal-header"><h5 class="modal-title">تعديل بيانات الموظفة</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="mb-2"><label class="form-label">الاسم</label><input type="text" name="name" id="edit_name" class="form-control"></div>
        <div class="mb-2"><label class="form-label">الوظيفة</label><input type="text" name="position" id="edit_position" class="form-control"></div>
        <div class="mb-2"><label class="form-label">الهاتف</label><input type="text" name="phone" id="edit_phone" class="form-control"></div>
        <div class="mb-2"><label class="form-label">الراتب الشهري</label><input type="number" step="0.01" name="monthly_salary" id="edit_monthly_salary" class="form-control"></div>
        <div class="mb-2"><label class="form-label">تاريخ التعيين</label><input type="date" name="hire_date" id="edit_hire_date" class="form-control"></div>
        <div class="mb-2"><label class="form-label">الحالة</label>
            <select name="status" id="edit_status" class="form-select"><option value="active">تعمل حالياً</option><option value="inactive">متوقفة</option></select>
        </div>
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
        document.getElementById('edit_position').value = btn.dataset.position;
        document.getElementById('edit_phone').value = btn.dataset.phone;
        document.getElementById('edit_monthly_salary').value = btn.dataset.monthly_salary;
        document.getElementById('edit_hire_date').value = btn.dataset.hire_date;
        document.getElementById('edit_status').value = btn.dataset.status;
        document.getElementById('edit_notes').value = btn.dataset.notes;
        new bootstrap.Modal(document.getElementById('editModal')).show();
    });
});
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
