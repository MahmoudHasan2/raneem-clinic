<?php
require_once __DIR__ . '/includes/header.php';
$pdo = db();

$month = $_GET['month'] ?? date('Y-m');
$monthStart = $month . '-01';
$monthEnd = date('Y-m-t', strtotime($monthStart));

// الإيرادات حسب نوع العملية
$stmt = $pdo->prepare('SELECT operation_type, COUNT(*) cnt, COALESCE(SUM(price),0) total, COALESCE(SUM(cost),0) cost
    FROM operations WHERE operation_date BETWEEN ? AND ? GROUP BY operation_type ORDER BY total DESC');
$stmt->execute([$monthStart, $monthEnd]);
$byType = $stmt->fetchAll();

$totalRevenue = array_sum(array_column($byType, 'total'));
$totalOpCost = array_sum(array_column($byType, 'cost'));

// المصاريف حسب التصنيف
$stmt = $pdo->prepare('SELECT category, COALESCE(SUM(total_amount),0) total FROM expenses WHERE expense_date BETWEEN ? AND ? GROUP BY category ORDER BY total DESC');
$stmt->execute([$monthStart, $monthEnd]);
$byExpenseCat = $stmt->fetchAll();
$totalExpenses = array_sum(array_column($byExpenseCat, 'total'));

// الرواتب
$stmt = $pdo->prepare('SELECT e.name, SUM(sp.amount) total FROM salary_payments sp JOIN employees e ON e.id = sp.employee_id WHERE sp.pay_month = ? GROUP BY e.name ORDER BY total DESC');
$stmt->execute([$month]);
$salaries = $stmt->fetchAll();
$totalSalaries = array_sum(array_column($salaries, 'total'));

$netProfit = $totalRevenue - $totalOpCost - $totalExpenses - $totalSalaries;

// أفضل الزبائن هذا الشهر
$stmt = $pdo->prepare('SELECT c.name, COUNT(*) visits, SUM(o.price) total FROM operations o JOIN customers c ON c.id=o.customer_id WHERE o.operation_date BETWEEN ? AND ? GROUP BY c.id ORDER BY total DESC LIMIT 5');
$stmt->execute([$monthStart, $monthEnd]);
$topCustomers = $stmt->fetchAll();
?>
<h3 class="mb-4">تقرير الأرباح</h3>

<form method="get" class="mb-4 d-flex gap-2 align-items-end">
    <div>
        <label class="form-label small">اختاري الشهر</label>
        <input type="month" name="month" class="form-control" value="<?= e($month) ?>" onchange="this.form.submit()">
    </div>
</form>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3"><div class="stat-card"><div class="num"><?= number_format($totalRevenue,0) ?></div><div class="label">إجمالي الإيرادات</div></div></div>
    <div class="col-6 col-md-3"><div class="stat-card"><div class="num"><?= number_format($totalOpCost + $totalExpenses,0) ?></div><div class="label">مصاريف ومواد</div></div></div>
    <div class="col-6 col-md-3"><div class="stat-card"><div class="num"><?= number_format($totalSalaries,0) ?></div><div class="label">رواتب مدفوعة</div></div></div>
    <div class="col-6 col-md-3"><div class="stat-card"><div class="num" style="color: <?= $netProfit>=0?'#2e7d32':'#c62828' ?>"><?= number_format($netProfit,0) ?></div><div class="label">صافي الربح</div></div></div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">الإيرادات حسب نوع الخدمة</div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead><tr><th>النوع</th><th>عدد العمليات</th><th>الإيراد</th></tr></thead>
                    <tbody>
                    <?php if (!$byType): ?><tr><td colspan="3" class="text-center text-muted py-3">لا توجد بيانات لهذا الشهر</td></tr><?php endif; ?>
                    <?php foreach ($byType as $row): ?>
                        <tr><td><?= e($row['operation_type']) ?></td><td><?= (int)$row['cnt'] ?></td><td><?= number_format($row['total'],0) ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">المصاريف حسب التصنيف</div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead><tr><th>التصنيف</th><th>الإجمالي</th></tr></thead>
                    <tbody>
                    <?php if (!$byExpenseCat): ?><tr><td colspan="2" class="text-center text-muted py-3">لا توجد مصاريف لهذا الشهر</td></tr><?php endif; ?>
                    <?php foreach ($byExpenseCat as $row): ?>
                        <tr><td><?= e($row['category']) ?></td><td><?= number_format($row['total'],0) ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">الرواتب المدفوعة هذا الشهر</div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead><tr><th>الموظفة</th><th>المبلغ</th></tr></thead>
                    <tbody>
                    <?php if (!$salaries): ?><tr><td colspan="2" class="text-center text-muted py-3">لا توجد رواتب مسجّلة</td></tr><?php endif; ?>
                    <?php foreach ($salaries as $row): ?>
                        <tr><td><?= e($row['name']) ?></td><td><?= number_format($row['total'],0) ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">أفضل الزبائن هذا الشهر</div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead><tr><th>الزبونة</th><th>الزيارات</th><th>الإجمالي</th></tr></thead>
                    <tbody>
                    <?php if (!$topCustomers): ?><tr><td colspan="3" class="text-center text-muted py-3">لا توجد بيانات</td></tr><?php endif; ?>
                    <?php foreach ($topCustomers as $row): ?>
                        <tr><td><?= e($row['name']) ?></td><td><?= (int)$row['visits'] ?></td><td><?= number_format($row['total'],0) ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
