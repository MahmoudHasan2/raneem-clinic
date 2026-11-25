<?php
require_once __DIR__ . '/includes/header.php';
$pdo = db();
$operationTypes = require __DIR__ . '/includes/operation_types.php';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = $_POST['action'] ?? '';
    if ($action === 'add' || $action === 'edit') {
        $customerId = (int)$_POST['customer_id'];
        $type = trim($_POST['operation_type']);
        $desc = trim($_POST['description']);
        $price = (float)$_POST['price'];
        $cost = (float)($_POST['cost'] ?? 0);
        $employeeId = $_POST['employee_id'] !== '' ? (int)$_POST['employee_id'] : null;
        $date = $_POST['operation_date'];
        $notes = trim($_POST['notes'] ?? '');
        if ($action === 'add') {
            $pdo->prepare('INSERT INTO operations (customer_id, operation_type, description, price, cost, employee_id, operation_date, notes) VALUES (?,?,?,?,?,?,?,?)')
                ->execute([$customerId, $type, $desc, $price, $cost, $employeeId, $date, $notes]);
            $msg = 'تمت إضافة العملية';
        } else {
            $pdo->prepare('UPDATE operations SET customer_id=?, operation_type=?, description=?, price=?, cost=?, employee_id=?, operation_date=?, notes=? WHERE id=?')
                ->execute([$customerId, $type, $desc, $price, $cost, $employeeId, $date, $notes, (int)$_POST['id']]);
            $msg = 'تم الحفظ';
        }
    } elseif ($action === 'delete') {
        $pdo->prepare('DELETE FROM operations WHERE id=?')->execute([(int)$_POST['id']]);
        $msg = 'تم الحذف';
    }
}

// فلاتر
$filterType = trim($_GET['type'] ?? '');
$filterFrom = trim($_GET['from'] ?? '');
$filterTo = trim($_GET['to'] ?? '');

$sql = 'SELECT o.*, c.name AS customer_name, e.name AS employee_name FROM operations o
        JOIN customers c ON c.id = o.customer_id
        LEFT JOIN employees e ON e.id = o.employee_id WHERE 1=1';
$params = [];
if ($filterType !== '') { $sql .= ' AND o.operation_type = ?'; $params[] = $filterType; }
if ($filterFrom !== '') { $sql .= ' AND o.operation_date >= ?'; $params[] = $filterFrom; }
if ($filterTo !== '') { $sql .= ' AND o.operation_date <= ?'; $params[] = $filterTo; }
$sql .= ' ORDER BY o.operation_date DESC, o.id DESC LIMIT 300';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$operations = $stmt->fetchAll();

$customers = $pdo->query('SELECT id, name FROM customers ORDER BY name ASC')->fetchAll();
$employees = $pdo->query("SELECT id, name FROM employees WHERE status='active' ORDER BY name ASC")->fetchAll();
?>
<h3 class="mb-4">عمليات الزبائن</h3>
<?php if ($msg): ?><div class="alert alert-info"><?= e($msg) ?></div><?php endif; ?>

<div class="card">
    <div class="card-header">تسجيل عملية جديدة</div>
    <div class="card-body">
        <form method="post" class="row g-3">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="action" value="add">
            <div class="col-md-3">
                <label class="form-label">الزبونة</label>
                <select name="customer_id" class="form-select" required>
                    <option value="">اختاري...</option>
                    <?php foreach ($customers as $c): ?><option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">نوع العملية</label>
                <select name="operation_type" class="form-select" required>
                    <?php foreach ($operationTypes as $t): ?><option value="<?= e($t) ?>"><?= e($t) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">الموظفة المنفذة (اختياري)</label>
                <select name="employee_id" class="form-select">
                    <option value="">—</option>
                    <?php foreach ($employees as $emp): ?><option value="<?= $emp['id'] ?>"><?= e($emp['name']) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">التاريخ</label>
                <input type="date" name="operation_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">السعر المدفوع</label>
                <input type="number" step="0.01" name="price" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">تكلفة المواد المستخدمة (اختياري)</label>
                <input type="number" step="0.01" name="cost" class="form-control" value="0">
            </div>
            <div class="col-md-6">
                <label class="form-label">تفاصيل إضافية</label>
                <input type="text" name="description" class="form-control" placeholder="مثال: بوتوكس الجبين">
            </div>
            <div class="col-md-12">
                <label class="form-label">ملاحظات</label>
                <input type="text" name="notes" class="form-control">
            </div>
            <div class="col-12"><button class="btn btn-gold">تسجيل العملية</button></div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <form method="get" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small">تصفية حسب النوع</label>
                <select name="type" class="form-select form-select-sm">
                    <option value="">الكل</option>
                    <?php foreach ($operationTypes as $t): ?><option value="<?= e($t) ?>" <?= $filterType===$t?'selected':'' ?>><?= e($t) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3"><label class="form-label small">من تاريخ</label><input type="date" name="from" class="form-control form-control-sm" value="<?= e($filterFrom) ?>"></div>
            <div class="col-md-3"><label class="form-label small">إلى تاريخ</label><input type="date" name="to" class="form-control form-control-sm" value="<?= e($filterTo) ?>"></div>
            <div class="col-md-3"><button class="btn btn-sm btn-outline-secondary">تصفية</button> <a href="operations.php" class="btn btn-sm btn-link">إلغاء</a></div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>التاريخ</th><th>الزبونة</th><th>النوع</th><th>التفاصيل</th><th>الموظفة</th><th>السعر</th><th>تكلفة</th><th></th></tr></thead>
            <tbody>
            <?php if (!$operations): ?><tr><td colspan="8" class="text-center text-muted py-4">لا توجد عمليات</td></tr><?php endif; ?>
            <?php foreach ($operations as $op): ?>
                <tr>
                    <td><?= e($op['operation_date']) ?></td>
                    <td><?= e($op['customer_name']) ?></td>
                    <td><span class="badge badge-type"><?= e($op['operation_type']) ?></span></td>
                    <td class="text-muted small"><?= e($op['description']) ?></td>
                    <td><?= e($op['employee_name'] ?? '—') ?></td>
                    <td><?= number_format($op['price'], 0) ?></td>
                    <td><?= number_format($op['cost'], 0) ?></td>
                    <td class="text-nowrap">
                        <button type="button" class="btn btn-sm btn-outline-secondary edit-btn"
                            data-id="<?= $op['id'] ?>" data-customer_id="<?= $op['customer_id'] ?>" data-operation_type="<?= e($op['operation_type']) ?>"
                            data-description="<?= e($op['description']) ?>" data-price="<?= $op['price'] ?>" data-cost="<?= $op['cost'] ?>"
                            data-employee_id="<?= $op['employee_id'] ?>" data-operation_date="<?= e($op['operation_date']) ?>" data-notes="<?= e($op['notes']) ?>">تعديل</button>
                        <form method="post" class="d-inline" onsubmit="return confirm('تأكيد الحذف؟')">
                            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $op['id'] ?>">
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
  <div class="modal-dialog modal-lg">
    <form method="post" class="modal-content">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
      <input type="hidden" name="action" value="edit">
      <input type="hidden" name="id" id="edit_id">
      <div class="modal-header"><h5 class="modal-title">تعديل العملية</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body row g-3">
        <div class="col-md-4">
            <label class="form-label">الزبونة</label>
            <select name="customer_id" id="edit_customer_id" class="form-select">
                <?php foreach ($customers as $c): ?><option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">النوع</label>
            <select name="operation_type" id="edit_operation_type" class="form-select">
                <?php foreach ($operationTypes as $t): ?><option value="<?= e($t) ?>"><?= e($t) ?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">الموظفة</label>
            <select name="employee_id" id="edit_employee_id" class="form-select">
                <option value="">—</option>
                <?php foreach ($employees as $emp): ?><option value="<?= $emp['id'] ?>"><?= e($emp['name']) ?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4"><label class="form-label">التاريخ</label><input type="date" name="operation_date" id="edit_operation_date" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">السعر</label><input type="number" step="0.01" name="price" id="edit_price" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">التكلفة</label><input type="number" step="0.01" name="cost" id="edit_cost" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">التفاصيل</label><input type="text" name="description" id="edit_description" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">ملاحظات</label><input type="text" name="notes" id="edit_notes" class="form-control"></div>
      </div>
      <div class="modal-footer"><button class="btn btn-gold">حفظ التعديلات</button></div>
    </form>
  </div>
</div>
<script>
document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('edit_id').value = btn.dataset.id;
        document.getElementById('edit_customer_id').value = btn.dataset.customer_id;
        document.getElementById('edit_operation_type').value = btn.dataset.operation_type;
        document.getElementById('edit_employee_id').value = btn.dataset.employee_id || '';
        document.getElementById('edit_operation_date').value = btn.dataset.operation_date;
        document.getElementById('edit_price').value = btn.dataset.price;
        document.getElementById('edit_cost').value = btn.dataset.cost;
        document.getElementById('edit_description').value = btn.dataset.description;
        document.getElementById('edit_notes').value = btn.dataset.notes;
        new bootstrap.Modal(document.getElementById('editModal')).show();
    });
});
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
