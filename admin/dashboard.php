<?php
require_once __DIR__ . '/includes/header.php';
$pdo = db();

$thisMonth = date('Y-m');
$monthStart = date('Y-m-01');
$monthEnd = date('Y-m-t');

$totalCustomers = (int)$pdo->query('SELECT COUNT(*) c FROM customers')->fetch()['c'];

$stmt = $pdo->prepare('SELECT COALESCE(SUM(price),0) s FROM operations WHERE operation_date BETWEEN ? AND ?');
$stmt->execute([$monthStart, $monthEnd]);
$monthRevenue = (float)$stmt->fetch()['s'];

$stmt = $pdo->prepare('SELECT COALESCE(SUM(cost),0) s FROM operations WHERE operation_date BETWEEN ? AND ?');
$stmt->execute([$monthStart, $monthEnd]);
$monthOpCost = (float)$stmt->fetch()['s'];

$stmt = $pdo->prepare('SELECT COALESCE(SUM(total_amount),0) s FROM expenses WHERE expense_date BETWEEN ? AND ?');
$stmt->execute([$monthStart, $monthEnd]);
$monthExpenses = (float)$stmt->fetch()['s'];

$stmt = $pdo->prepare('SELECT COALESCE(SUM(amount),0) s FROM salary_payments WHERE pay_month = ?');
$stmt->execute([$thisMonth]);
$monthSalaries = (float)$stmt->fetch()['s'];

$netProfit = $monthRevenue - $monthOpCost - $monthExpenses - $monthSalaries;

$recentOps = $pdo->query('SELECT o.*, c.name AS customer_name FROM operations o JOIN customers c ON c.id = o.customer_id ORDER BY o.operation_date DESC, o.id DESC LIMIT 8')->fetchAll();
?>

<h3 class="mb-4">نظرة عامة — <?= e(date('F Y')) ?></h3>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card"><div class="num"><?= $totalCustomers ?></div><div class="label">إجمالي الزبائن</div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card"><div class="num"><?= number_format($monthRevenue, 0) ?></div><div class="label">إيرادات هذا الشهر</div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card"><div class="num"><?= number_format($monthExpenses + $monthOpCost, 0) ?></div><div class="label">مصاريف ومواد هذا الشهر</div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card"><div class="num" style="color: <?= $netProfit >= 0 ? '#2e7d32' : '#c62828' ?>"><?= number_format($netProfit, 0) ?></div><div class="label">صافي الربح التقديري</div></div>
    </div>
</div>

<div class="card">
    <div class="card-header">آخر عمليات الزبائن</div>
    <div class="card-body p-0">
        <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead><tr><th>التاريخ</th><th>الزبونة</th><th>نوع العملية</th><th>السعر</th></tr></thead>
            <tbody>
            <?php if (!$recentOps): ?>
                <tr><td colspan="4" class="text-center text-muted py-4">لا توجد عمليات مسجّلة بعد</td></tr>
            <?php endif; ?>
            <?php foreach ($recentOps as $op): ?>
                <tr>
                    <td><?= e($op['operation_date']) ?></td>
                    <td><?= e($op['customer_name']) ?></td>
                    <td><span class="badge badge-type"><?= e($op['operation_type']) ?></span></td>
                    <td><?= number_format($op['price'], 0) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
