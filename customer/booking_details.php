<?php
require_once __DIR__.'/../includes/auth.php';
$u=require_login('customer');
$cid=get_customer_id_for_user($u['id']);
$id=(int)($_GET['id']??0);
$s=$pdo->prepare("SELECT b.*,s.service_name,s.duration,u.full_name,u.phone FROM bookings b JOIN services s ON s.id=b.service_id JOIN customers c ON c.id=b.customer_id JOIN users u ON u.id=c.user_id WHERE b.id=? AND b.customer_id=?");
$s->execute([$id,$cid]);
$b=$s->fetch();
if(!$b)  {
    http_response_code(404);
    exit('Booking not found.');
}
$s=$pdo->prepare("SELECT p.* FROM payments p WHERE p.booking_id=? ORDER BY p.id DESC LIMIT 1");
$s->execute([$id]);
$pay=$s->fetch();
$s=$pdo->prepare("SELECT st.full_name,st.phone,st.position FROM staff_assignments a JOIN staff st ON st.id=a.staff_id WHERE a.booking_id=? ORDER BY a.id DESC LIMIT 1");
$s->execute([$id]);
$staff=$s->fetch();
$statuses=['Pending','Confirmed','Assigned','In Progress','Completed'];
$current=array_search($b['status'],$statuses,true);
$pageTitle='Booking Details';
require __DIR__.'/../includes/header.php';
?><div class="app-shell"><?php
require __DIR__.'/../includes/customer_sidebar.php';
?><main class="app-main"><div class="topbar"><div><span class="eyebrow">BOOKING TRACKING</span><h2><?=e($b['booking_reference'])?></h2></div><?=status_badge($b['status'])?></div><div class="panel mb-4"><div class="timeline"><?php
foreach($statuses as $i=>$st):
?><div class="timeline-item <?=$i<=$current?'done':''?> <?=$i===$current?'current':''?>"><span><?=$i+1?></span><strong><?=e($st)?></strong></div><?php
endforeach;
?></div></div><div class="row g-4"><div class="col-lg-8"><div class="panel"><h5>Booking information</h5><div class="detail-grid"><div><span>Service</span><b><?=e($b['service_name'])?></b></div><div><span>Quantity</span><b><?=e($b['quantity'])?></b></div><div><span>Date</span><b><?=e($b['service_date'])?></b></div><div><span>Time</span><b><?=e($b['service_time'])?></b></div><div><span>Address</span><b><?=e($b['service_address'])?></b></div><div><span>Amount</span><b><?=money($b['estimated_amount'])?></b></div><div><span>Assigned staff</span><b><?=e($staff['full_name']??'Awaiting assignment')?></b></div><div><span>Payment</span><b><?=status_badge($pay['payment_status']??'Pending')?></b></div></div><hr><p class="mb-0"><strong>Notes:</strong> <?=e($b['notes']?:'No notes')?></p></div></div><div class="col-lg-4"><div class="panel"><h5>Service contact</h5><p><?=e($b['full_name'])?></p><p><?=e($b['phone'])?></p><p class="text-muted small">Staff will use the booking address and notes to prepare for the service.</p></div></div></div></main></div><?php
require __DIR__.'/../includes/footer.php';
?>