<?php
require_once __DIR__ . '/../../includes/auth.php';
require_login('admin');
$from = $_GET['from'] ?? date('Y-m-01');
$to = $_GET['to'] ?? date('Y-m-d');
$stmt = $pdo->prepare( "SELECT c.id, u.full_name, u.email, u.phone,
            c.address, c.status, c.created_at,
            COUNT(b.id) AS booking_count,
            COALESCE(SUM(CASE WHEN b.status = 'Completed' THEN 1 ELSE 0 END), 0) AS completed_bookings
     FROM customers c
     INNER JOIN users u ON u.id = c.user_id
     LEFT JOIN bookings b ON b.customer_id = c.id
     WHERE DATE(c.created_at) BETWEEN ? AND ?
     GROUP BY c.id, u.full_name, u.email, u.phone, c.address, c.status, c.created_at
     ORDER BY c.created_at DESC" );
$stmt->execute([$from, $to]);
$rows = $stmt->fetchAll();
$pageTitle = 'Customer Report';
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
                <h2>Customer Report</h2>
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

        <div class="panel">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Bookings</th>
                            <th>Completed</th>
                            <th>Status</th>
                            <th>Registered</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
foreach ($rows as $row):
?>
                            <tr>
                                <td><?=e($row['full_name'])?></td>
                                <td><?=e($row['email'])?></td>
                                <td><?=e($row['phone'])?></td>
                                <td><?=e($row['booking_count'])?></td>
                                <td><?=e($row['completed_bookings'])?></td>
                                <td><?=status_badge($row['status'])?></td>
                                <td><?=e($row['created_at'])?></td>
                            </tr>
                        <?php
endforeach;
?>

                        <?php
if (!$rows):
?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No customers found.</td>
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
