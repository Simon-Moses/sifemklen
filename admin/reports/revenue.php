<?php
require_once __DIR__ . '/../../includes/auth.php';
require_login('admin');
$from = $_GET['from'] ?? date('Y-m-01');
$to = $_GET['to'] ?? date('Y-m-d');
$stmt = $pdo->prepare( "SELECT p.payment_reference, b.booking_reference,
            u.full_name AS customer_name, p.amount,
            p.payment_method, p.payment_status,
            p.payment_date, p.created_at
     FROM payments p
     INNER JOIN bookings b ON b.id = p.booking_id
     INNER JOIN customers c ON c.id = b.customer_id
     INNER JOIN users u ON u.id = c.user_id
     WHERE DATE(COALESCE(p.payment_date, p.created_at)) BETWEEN ? AND ?
     ORDER BY COALESCE(p.payment_date, p.created_at) DESC" );
$stmt->execute([$from, $to]);
$rows = $stmt->fetchAll();
$summaryStmt = $pdo->prepare( "SELECT
        COALESCE(SUM(CASE WHEN payment_status = 'Paid' THEN amount ELSE 0 END), 0) AS paid_revenue,
        COALESCE(SUM(CASE WHEN payment_status = 'Pending' THEN amount ELSE 0 END), 0) AS pending_amount,
        COALESCE(SUM(CASE WHEN payment_status = 'Refunded' THEN amount ELSE 0 END), 0) AS refunded_amount
     FROM payments
     WHERE DATE(COALESCE(payment_date, created_at)) BETWEEN ? AND ?" );
$summaryStmt->execute([$from, $to]);
$summary = $summaryStmt->fetch();
$pageTitle = 'Revenue Report';
require __DIR__ . '/../../includes/header.php';
?>
<div class="app-shell">
    <?php
require __DIR__ . '/../../includes/admin_sidebar.php';
?>

    <main class="app-main">
        <div class="topbar">
            <div>
                <span class="eyebrow">REPORTING</span>
                <h2>Revenue Report</h2>
            </div>
            <a href="<?=base_url('admin/reports/index.php')?>" class="btn btn-outline-teal">Back to Reports</a>
        </div>

        <div class="panel mb-4">
            <form method="get" class="row g-3">
                <div class="col-md-5">
                    <label class="form-label">From</label>
                    <input type="date" name="from" class="form-control" value="<?=e($from)?>" required>
                </div>
                <div class="col-md-5">
                    <label class="form-label">To</label>
                    <input type="date" name="to" class="form-control" value="<?=e($to)?>" required>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-teal w-100">Generate</button>
                </div>
            </form>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="kpi">
                    <span>Paid Revenue</span>
                    <strong><?=money($summary['paid_revenue'])?></strong>
                </div>
            </div>
            <div class="col-md-4">
                <div class="kpi">
                    <span>Pending Payments</span>
                    <strong><?=money($summary['pending_amount'])?></strong>
                </div>
            </div>
            <div class="col-md-4">
                <div class="kpi">
                    <span>Refunded</span>
                    <strong><?=money($summary['refunded_amount'])?></strong>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Payment</th>
                            <th>Booking</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
foreach ($rows as $row):
?>
                            <tr>
                                <td><?=e($row['payment_reference'])?></td>
                                <td><?=e($row['booking_reference'])?></td>
                                <td><?=e($row['customer_name'])?></td>
                                <td><?=money($row['amount'])?></td>
                                <td><?=e($row['payment_method'])?></td>
                                <td><?=status_badge($row['payment_status'])?></td>
                                <td><?=e($row['payment_date'] ?? $row['created_at'])?></td>
                            </tr>
                        <?php
endforeach;
?>

                        <?php
if (!$rows):
?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No payments found.</td>
                            </tr>
                        <?php
endif;
?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<?php
require __DIR__ . '/../../includes/footer.php';
?>
