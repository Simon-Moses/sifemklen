<?php
require_once __DIR__.'/../includes/auth.php';
$u=require_login('customer');
$cid=get_customer_id_for_user($u['id']);
$s=$pdo->prepare("SELECT p.*,b.booking_reference FROM payments p JOIN bookings b ON b.id=p.booking_id WHERE b.customer_id=? ORDER BY p.created_at DESC");
$s->execute([$cid]);
$rows=$s->fetchAll();
$pageTitle='Payments';
require __DIR__.'/../includes/header.php';
?><div class="app-shell"><?php
require __DIR__.'/../includes/customer_sidebar.php';
?><main class="app-main"><div class="topbar"><div><span class="eyebrow">PAYMENTS</span><h2>My payments</h2></div></div><div class="panel"><div class="table-responsive"><table class="table"><thead><tr><th>Reference</th><th>Booking</th><th>Amount</th><th>Method</th><th>Status</th><th>Date</th></tr></thead><tbody><?php
foreach($rows as $r):
?><tr><td><?=e($r['payment_reference'])?></td><td><?=e($r['booking_reference'])?></td><td><?=money($r['amount'])?></td><td><?=e($r['payment_method'])?></td><td><?=status_badge($r['payment_status'])?></td><td><?=e($r['payment_date']??$r['created_at'])?></td></tr><?php
endforeach;
if(!$rows):
?><tr><td colspan="6" class="text-center text-muted">No payments found.</td></tr><?php
endif;
?></tbody></table></div></div></main></div><?php
require __DIR__.'/../includes/footer.php';
?>