<?php
require_once __DIR__.'/../../includes/auth.php';
require_login('admin');
$rows=$pdo->query("SELECT p.*,b.booking_reference,u.full_name FROM payments p JOIN bookings b ON b.id=p.booking_id JOIN customers c ON c.id=b.customer_id JOIN users u ON u.id=c.user_id ORDER BY p.created_at DESC")->fetchAll();
$pageTitle='Payments';
require __DIR__.'/../../includes/header.php';
?><div class="app-shell"><?php
require __DIR__.'/../../includes/admin_sidebar.php';
?><main class="app-main"><div class="topbar"><h2>Payments</h2></div><div class="panel"><div class="table-responsive"><table class="table"><thead><tr><th>Reference</th><th>Booking</th><th>Customer</th><th>Amount</th><th>Method</th><th>Status</th><th>Action</th></tr></thead><tbody><?php
foreach($rows as $r):
?><tr><td><?=e($r['payment_reference'])?></td><td><?=e($r['booking_reference'])?></td><td><?=e($r['full_name'])?></td><td><?=money($r['amount'])?></td><td><?=e($r['payment_method'])?></td><td><?=status_badge($r['payment_status'])?></td><td><a class="btn btn-sm btn-outline-teal" href="<?=base_url('admin/payments/view.php?id='.$r['id'])?>">Update</a></td></tr><?php
endforeach;
?></tbody></table></div></div></main></div><?php
require __DIR__.'/../../includes/footer.php';
?>