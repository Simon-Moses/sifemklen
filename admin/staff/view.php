<?php
require_once __DIR__.'/../../includes/auth.php';
require_login('admin');
$id=(int)($_GET['id']??0);
$s=$pdo->prepare('SELECT * FROM staff WHERE id=?');
$s->execute([$id]);
$r=$s->fetch();
if(!$r)exit('Staff not found.');
$s=$pdo->prepare('SELECT a.*,b.booking_reference,b.service_date,s.service_name FROM staff_assignments a JOIN bookings b ON b.id=a.booking_id JOIN services s ON s.id=b.service_id WHERE a.staff_id=? ORDER BY a.assigned_at DESC');
$s->execute([$id]);
$rows=$s->fetchAll();
$pageTitle='Staff Profile';
require __DIR__.'/../../includes/header.php';
?><div class="app-shell"><?php
require __DIR__.'/../../includes/admin_sidebar.php';
?><main class="app-main"><div class="topbar"><h2><?=e($r['full_name'])?></h2><?=status_badge($r['status'])?></div><div class="panel mb-4"><div class="detail-grid"><div><span>Phone</span><b><?=e($r['phone'])?></b></div><div><span>Email</span><b><?=e($r['email'])?></b></div><div><span>Position</span><b><?=e($r['position'])?></b></div><div><span>Address</span><b><?=e($r['address'])?></b></div></div></div><div class="panel"><h5>Assignment history</h5><div class="table-responsive"><table class="table"><thead><tr><th>Booking</th><th>Service</th><th>Date</th><th>Status</th></tr></thead><tbody><?php
foreach($rows as $x):
?><tr><td><?=e($x['booking_reference'])?></td><td><?=e($x['service_name'])?></td><td><?=e($x['service_date'])?></td><td><?=status_badge($x['status'])?></td></tr><?php
endforeach;
?></tbody></table></div></div></main></div><?php
require __DIR__.'/../../includes/footer.php';
?>