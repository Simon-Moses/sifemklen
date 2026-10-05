<?php
require_once __DIR__.'/../includes/auth.php';
$u=require_login('customer');
$cid=get_customer_id_for_user($u['id']);
$s=$pdo->prepare("SELECT b.*,s.service_name FROM bookings b JOIN services s ON s.id=b.service_id WHERE b.customer_id=? ORDER BY b.created_at DESC");
$s->execute([$cid]);
$rows=$s->fetchAll();
$pageTitle='My Bookings';
require __DIR__.'/../includes/header.php';
?><div class="app-shell"><?php
require __DIR__.'/../includes/customer_sidebar.php';
?><main class="app-main"><div class="topbar"><div><span class="eyebrow">BOOKINGS</span><h2>My bookings</h2></div></div><div class="panel"><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Reference</th><th>Service</th><th>Date</th><th>Time</th><th>Amount</th><th>Status</th><th></th></tr></thead><tbody><?php
foreach($rows as $r):
?><tr><td><?=e($r['booking_reference'])?></td><td><?=e($r['service_name'])?></td><td><?=e($r['service_date'])?></td><td><?=e($r['service_time'])?></td><td><?=money($r['estimated_amount'])?></td><td><?=status_badge($r['status'])?></td><td><a class="btn btn-sm btn-outline-teal" href="<?=base_url('customer/booking_details.php?id='.$r['id'])?>">Track</a></td></tr><?php
endforeach;
if(!$rows):
?><tr><td colspan="7" class="text-center text-muted">No bookings found.</td></tr><?php
endif;
?></tbody></table></div></div></main></div><?php
require __DIR__.'/../includes/footer.php';
?>