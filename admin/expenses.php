<?php
require_once __DIR__ . '/includes/header.php';
$pdo = db();
$msg = '';
$categories = ['مواد تجميل', 'مواد ليزر', 'أدوات وأجهزة', 'إيجار', 'كهرباء وماء', 'صيانة', 'تسويق وإعلان', 'أخرى'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = $_POST['action'] ?? '';
    if ($action === 'add' || $action === 'edit') {
        $item = trim($_POST['item_name']);
        $cat = trim($_POST['category']);
        $qty = (float)$_POST['quantity'];
        $unit = (float)$_POST['unit_price'];
        $total = $qty > 0 && $unit > 0 ? $qty * $unit : (float)$_POST['total_amount'];
        $date = $_POST['expense_date'];
        $notes = trim($_POST['notes'] ?? '');
        if ($action === 'add') {
            $pdo->prepare('INSERT INTO expenses (item_name, category, quantity, unit_price, total_amount, expense_date, notes) VALUES (?,?,?,?,?,?,?)')
                ->execute([$item, $cat, $qty, $unit, $total, $date, $notes]);
            $msg = 'تمت إضافة المصروف';
        } else {
            $pdo->prepare('UPDATE expenses SET item_name=?, category=?, quantity=?, unit_price=?, total_amount=?, expense_date=?, notes=? WHERE id=?')
                ->execute([$item, $cat, $qty, $unit, $total, $date, $notes, (int)$_POST['id']]);
            $msg = 'تم الحفظ';
        }
    } elseif ($action === 'delete') {
        $pdo->prepare('DELETE FROM expenses WHERE id=?')->execute([(int)$_POST['id']]);
        $msg = 'تم الحذف';
    }
}

$filterFrom = trim($_GET['from'] ?? '');
$filterTo = trim($_GET['to'] ?? '');
$sql = 'SELECT * FROM expenses WHERE 1=1';
$params = [];
if ($filterFrom !== '') { $sql .= ' AND expense_date >= ?'; $params[] = $filterFrom; }
if ($filterTo !== '') { $sql .= ' AND expense_date <= ?'; $params[] = $filterTo; }
$sql .= ' ORDER BY expense_date DESC, id DESC LIMIT 300';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$expenses = $stmt->fetchAll();
$totalShown = array_sum(array_column($expenses, 'total_amount'));
?>
<h3 class="mb-4">مصاريف المواد والمركز</h3>
<?php if ($msg): ?><div class="alert alert-info"><?= e($msg) ?></div><?php endif; ?>

<div class="card">
    <div class="card-header">إضافة مصروف جديد</div>
    <div class="card-body">
        <form method="post" class="row g-3">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="action" value="add">
            <div class="col-md-3"><label class="form-label">اسم المادة / المصروف</label><input type="text" name="item_name" class="form-control" required></div>
            <div class="col-md-2">
                <label class="form-label">التصنيف</label>
                <select name="category" class="form-select"><?php foreach ($categories as $c): ?><option><?= e($c) ?></option><?php endforeach; ?></select>
            </div>
            <div class="col-md-2"><label class="form-label">الكمية</label><input type="number" step="0.01" name="quantity" class="form-control" value="1"></div>
            <div class="col-md-2"><label class="form-label">سعر الوحدة</label><input type="number" step="0.01" name="unit_price" class="form-control" value="0"></div>
            <div class="col-md-3">
                <label class="form-label">المبلغ الإجمالي (يُحسب تلقائياً أو يُدخل يدوياً)</label>
                <input type="number" step="0.01" name="total_amount" class="form-control" placeholder="اتركيه فارغاً ليُحسب تلقائياً">
            </div>
            <div class="col-md-3"><label class="form-label">التاريخ</label><input type="date" name="expense_date" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
            <div class="col-md-9"><label class="form-label">ملاحظات</label><input type="text" name="notes" class="form-control"></div>
            <div class="col-12"><button class="btn btn-gold">إضافة</button></div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <form method="get" class="row g-2 align-items-end">
            <div class="col-md-3"><label class="form-label small">من تاريخ</label><input type="date" name="from" class="form-control form-control-sm" value="<?= e($filterFrom) ?>"></div>
            <div class="col-md-3"><label class="form-label small">إلى تاريخ</label><input type="date" name="to" class="form-control form-control-sm" value="<?= e($filterTo) ?>"></div>
            <div class="col-md-3"><button class="btn btn-sm btn-outline-secondary">تصفية</button> <a href="expenses.php" class="btn btn-sm btn-link">إلغاء</a></div>
            <div class="col-md-3 text-md-end"><strong>الإجمالي المعروض: <?= number_format($totalShown, 0) ?></strong></div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>التاريخ</th><th>المادة</th><th>التصنيف</th><th>الكمية</th><th>سعر الوحدة</th><th>الإجمالي</th><th></th></tr></thead>
            <tbody>
            <?php if (!$expenses): ?><tr><td colspan="7" class="text-center text-muted py-4">لا توجد مصاريف</td></tr><?php endif; ?>
            <?php foreach ($expenses as $ex): ?>
                <tr>
                    <td><?= e($ex['expense_date']) ?></td>
                    <td><?= e($ex['item_name']) ?></td>
                    <td><span class="badge badge-type"><?= e($ex['category']) ?></span></td>
                    <td><?= number_format($ex['quantity'], 2) ?></td>
                    <td><?= number_format($ex['unit_price'], 0) ?></td>
                    <td><?= number_format($ex['total_amount'], 0) ?></td>
                    <td class="text-nowrap">
                        <button type="button" class="btn btn-sm btn-outline-secondary edit-btn"
                            data-id="<?= $ex['id'] ?>" data-item_name="<?= e($ex['item_name']) ?>" data-category="<?= e($ex['category']) ?>"
                            data-quantity="<?= $ex['quantity'] ?>" data-unit_price="<?= $ex['unit_price'] ?>" data-total_amount="<?= $ex['total_amount'] ?>"
                            data-expense_date="<?= e($ex['expense_date']) ?>" data-notes="<?= e($ex['notes']) ?>">تعديل</button>
                        <form method="post" class="d-inline" onsubmit="return confirm('تأكيد الحذف؟')">
                            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $ex['id'] ?>">
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
      <div class="modal-header"><h5 class="modal-title">تعديل المصروف</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="mb-2"><label class="form-label">اسم المادة</label><input type="text" name="item_name" id="edit_item_name" class="form-control"></div>
        <div class="mb-2"><label class="form-label">التصنيف</label>
            <select name="category" id="edit_category" class="form-select"><?php foreach ($categories as $c): ?><option><?= e($c) ?></option><?php endforeach; ?></select>
        </div>
        <div class="mb-2"><label class="form-label">الكمية</label><input type="number" step="0.01" name="quantity" id="edit_quantity" class="form-control"></div>
        <div class="mb-2"><label class="form-label">سعر الوحدة</label><input type="number" step="0.01" name="unit_price" id="edit_unit_price" class="form-control"></div>
        <div class="mb-2"><label class="form-label">المبلغ الإجمالي</label><input type="number" step="0.01" name="total_amount" id="edit_total_amount" class="form-control"></div>
        <div class="mb-2"><label class="form-label">التاريخ</label><input type="date" name="expense_date" id="edit_expense_date" class="form-control"></div>
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
        document.getElementById('edit_item_name').value = btn.dataset.item_name;
        document.getElementById('edit_category').value = btn.dataset.category;
        document.getElementById('edit_quantity').value = btn.dataset.quantity;
        document.getElementById('edit_unit_price').value = btn.dataset.unit_price;
        document.getElementById('edit_total_amount').value = btn.dataset.total_amount;
        document.getElementById('edit_expense_date').value = btn.dataset.expense_date;
        document.getElementById('edit_notes').value = btn.dataset.notes;
        new bootstrap.Modal(document.getElementById('editModal')).show();
    });
});
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
