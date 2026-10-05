<?php
require_once __DIR__ . '/../../includes/auth.php';
require_login('admin');
$from = $_GET['from'] ?? date('Y-m-01');
$to = $_GET['to'] ?? date('Y-m-d');
$status = $_GET['status'] ?? '';
$allowedStatuses = ['Pending', 'Confirmed', 'Assigned', 'In Progress', 'Completed', 'Cancelled'];
$sql = "SELECT b.booking_reference, u.full_name AS customer_name,
               s.service_name, b.service_date, b.service_time,
               b.quantity, b.estimated_amount, b.status
        FROM bookings b
        INNER JOIN customers c ON c.id = b.customer_id
        INNER JOIN users u ON u.id = c.user_id
        INNER JOIN services s ON s.id = b.service_id
        WHERE b.service_date BETWEEN ? AND ?";
$params = [$from, $to];
if (in_array($status, $allowedStatuses, true))  {
    $sql .= " AND b.status = ?";
    $params[] = $status;
}
$sql .= " ORDER BY b.service_date DESC, b.service_time DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();
$pageTitle = 'Booking Report';
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
                <h2>Booking Report</h2>
            </div>
            <a href="<?=base_url('admin/reports/index.php')?>" class="btn btn-outline-teal">
                Back to Reports
            </a>
        </div>

        <div class="panel mb-4">
            <form method="get" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">From</label>
                    <input type="date" name="from" class="form-control" value="<?=e($from)?>" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">To</label>
                    <input type="date" name="to" class="form-control" value="<?=e($to)?>" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All statuses</option>
                        <?php
foreach ($allowedStatuses as $item):
?>
                            <option value="<?=e($item)?>" <?=$status === $item ? 'selected' : ''?>>
                                <?=e($item)?>
                            </option>
                        <?php
endforeach;
?>
                    </select>
                </div>

                <div class="col-12">
                    <button class="btn btn-teal">Generate Report</button>
                </div>
            </form>
        </div>

        <div class="panel">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Customer</th>
                            <th>Service</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Qty</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
foreach ($rows as $row):
?>
                            <tr>
                                <td><?=e($row['booking_reference'])?></td>
                                <td><?=e($row['customer_name'])?></td>
                                <td><?=e($row['service_name'])?></td>
                                <td><?=e($row['service_date'])?></td>
                                <td><?=e($row['service_time'])?></td>
                                <td><?=e($row['quantity'])?></td>
                                <td><?=money($row['estimated_amount'])?></td>
                                <td><?=status_badge($row['status'])?></td>
                            </tr>
                        <?php
endforeach;
?>

                        <?php
if (!$rows):
?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    No bookings found for the selected period.
                                </td>
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
