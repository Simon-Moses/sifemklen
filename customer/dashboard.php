<?php
require_once __DIR__.'/../includes/auth.php';
$u=require_login('customer');
$cid=get_customer_id_for_user($u['id']);
$counts=[];
foreach(['Total'=>'','Pending'=>" AND status='Pending'",'Completed'=>" AND status='Completed'",'Cancelled'=>" AND status='Cancelled'"] as $k=>$cond)  {
    $s=$pdo->prepare("SELECT COUNT(*) FROM bookings WHERE customer_id=?$cond");
    $s->execute([$cid]);
    $counts[$k]=$s->fetchColumn();
}
$s=$pdo->prepare("SELECT b.*,s.service_name FROM bookings b JOIN services s ON s.id=b.service_id WHERE b.customer_id=? ORDER BY b.created_at DESC LIMIT 5");
$s->execute([$cid]);
$recent=$s->fetchAll();
$pageTitle='Customer Dashboard';
require __DIR__.'/../includes/header.php';
?><div class="app-shell"><?php
require __DIR__.'/../includes/customer_sidebar.php';
?><main class="app-main"><div class="topbar"><div><span class="eyebrow">CUSTOMER PORTAL</span><h2>Welcome, <?=e($u['full_name'])?></h2></div><a class="btn btn-teal" href="<?=base_url('customer/book_service.php')?>">Book Service</a></div><div class="row g-3"><?php
foreach($counts as $k=>$v):
?><div class="col-6 col-lg-3"><div class="kpi"><span><?=e($k)?></span><strong><?=e($v)?></strong></div></div><?php
endforeach;
?></div><div class="panel mt-4"><div class="panel-head"><h5>Recent bookings</h5><a href="<?=base_url('customer/my_bookings.php')?>">View all</a></div><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Reference</th><th>Service</th><th>Date</th><th>Amount</th><th>Status</th></tr></thead><tbody><?php
foreach($recent as $r):
?><tr><td><a href="<?=base_url('customer/booking_details.php?id='.$r['id'])?>"><?=e($r['booking_reference'])?></a></td><td><?=e($r['service_name'])?></td><td><?=e($r['service_date'])?></td><td><?=money($r['estimated_amount'])?></td><td><?=status_badge($r['status'])?></td></tr><?php
endforeach;
if(!$recent):
?><tr><td colspan="5" class="text-center text-muted">No bookings yet.</td></tr><?php
endif;
?></tbody></table></div></div></main></div><?php
require __DIR__.'/../includes/footer.php';
?>