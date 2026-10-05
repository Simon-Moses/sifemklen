<?php
require_once __DIR__.'/../../includes/auth.php';
require_login('admin');
$id=(int)($_GET['id']??0);
$s=$pdo->prepare('SELECT c.*,u.full_name,u.email,u.phone FROM customers c JOIN users u ON u.id=c.user_id WHERE c.id=?');
$s->execute([$id]);
$r=$s->fetch();
if(!$r)exit('Customer not found.');
$s=$pdo->prepare('SELECT b.*,s.service_name FROM bookings b JOIN services s ON s.id=b.service_id WHERE b.customer_id=? ORDER BY b.created_at DESC');
$s->execute([$id]);
$books=$s->fetchAll();
$pageTitle='Customer';
require __DIR__.'/../../includes/header.php';
?><div class="app-shell"><?php
require __DIR__.'/../../includes/admin_sidebar.php';
?><main class="app-main"><div class="topbar"><h2><?=e($r['full_name'])?></h2><?=status_badge($r['status'])?></div><div class="panel mb-4"><div class="detail-grid"><div><span>Email</span><b><?=e($r['email'])?></b></div><div><span>Phone</span><b><?=e($r['phone'])?></b></div><div><span>Address</span><b><?=e($r['address'])?></b></div><div><span>Created</span><b><?=e($r['created_at'])?></b></div></div></div><div class="panel"><h5>Booking history</h5><div class="table-responsive"><table class="table"><thead><tr><th>Reference</th><th>Service</th><th>Date</th><th>Amount</th><th>Status</th></tr></thead><tbody><?php
foreach($books as $b):
?><tr><td><?=e($b['booking_reference'])?></td><td><?=e($b['service_name'])?></td><td><?=e($b['service_date'])?></td><td><?=money($b['estimated_amount'])?></td><td><?=status_badge($b['status'])?></td></tr><?php
endforeach;
?></tbody></table></div></div></main></div><?php
require __DIR__.'/../../includes/footer.php';
?>