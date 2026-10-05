<?php
require_once __DIR__.'/../../includes/auth.php';
$u=require_login('admin');
$id=(int)($_GET['id']??0);
$s=$pdo->prepare("SELECT b.*,u.full_name,u.email,u.phone,s.service_name FROM bookings b JOIN customers c ON c.id=b.customer_id JOIN users u ON u.id=c.user_id JOIN services s ON s.id=b.service_id WHERE b.id=?");
$s->execute([$id]);
$b=$s->fetch();
if(!$b)exit('Booking not found.');
$s=$pdo->prepare('SELECT a.*,st.full_name,st.phone,st.position FROM staff_assignments a JOIN staff st ON st.id=a.staff_id WHERE a.booking_id=? ORDER BY a.id DESC');
$s->execute([$id]);
$assignments=$s->fetchAll();
$staff=$pdo->query("SELECT * FROM staff WHERE status='Active' ORDER BY full_name")->fetchAll();
$s=$pdo->prepare('SELECT * FROM payments WHERE booking_id=? ORDER BY id DESC');
$s->execute([$id]);
$payments=$s->fetchAll();
$pageTitle='Booking '.$b['booking_reference'];
require __DIR__.'/../../includes/header.php';
?><div class="app-shell"><?php
require __DIR__.'/../../includes/admin_sidebar.php';
?><main class="app-main"><div class="topbar"><div><span class="eyebrow">BOOKING</span><h2><?=e($b['booking_reference'])?></h2></div><?=status_badge($b['status'])?></div><div class="row g-4"><div class="col-lg-8"><div class="panel"><h5>Booking details</h5><div class="detail-grid"><div><span>Customer</span><b><?=e($b['full_name'])?></b></div><div><span>Phone</span><b><?=e($b['phone'])?></b></div><div><span>Service</span><b><?=e($b['service_name'])?></b></div><div><span>Quantity</span><b><?=e($b['quantity'])?></b></div><div><span>Date</span><b><?=e($b['service_date'])?></b></div><div><span>Time</span><b><?=e($b['service_time'])?></b></div><div><span>Address</span><b><?=e($b['service_address'])?></b></div><div><span>Amount</span><b><?=money($b['estimated_amount'])?></b></div></div><hr><p><b>Notes:</b> <?=e($b['notes']?:'None')?></p></div><div class="panel mt-4"><h5>Update status</h5><form method="post" action="<?=base_url('admin/bookings/update_status.php')?>" class="row g-2"><input type="hidden" name="csrf" value="<?=csrf_token()?>"><input type="hidden" name="booking_id" value="<?=$id?>"><div class="col-md-8"><select name="status" class="form-select"><?php
foreach(['Pending','Confirmed','Assigned','In Progress','Completed','Cancelled'] as $st):
?><option <?=$b['status']===$st?'selected':''?>><?=e($st)?></option><?php
endforeach;
?></select></div><div class="col-md-4"><button class="btn btn-teal w-100">Update Status</button></div></form></div></div><div class="col-lg-4"><div class="panel"><h5>Assign staff</h5><form method="post" action="<?=base_url('admin/bookings/assign_staff.php')?>"><input type="hidden" name="csrf" value="<?=csrf_token()?>"><input type="hidden" name="booking_id" value="<?=$id?>"><select required name="staff_id" class="form-select mb-3"><option value="">Choose active staff</option><?php
foreach($staff as $st):
?><option value="<?=$st['id']?>"><?=e($st['full_name'])?> — <?=e($st['position'])?></option><?php
endforeach;
?></select><button class="btn btn-teal w-100">Assign</button></form><hr><?php
foreach($assignments as $a):
?><div class="small py-2"><b><?=e($a['full_name'])?></b><br><?=e($a['position'])?> · <?=status_badge($a['status'])?></div><?php
endforeach;
?></div><div class="panel mt-4"><h5>Payments</h5><?php
foreach($payments as $p):
?><div class="d-flex justify-content-between py-2 border-bottom"><span><?=e($p['payment_method'])?></span><b><?=money($p['amount'])?></b></div><div class="small mb-2"><?=status_badge($p['payment_status'])?></div><?php
endforeach;
?></div></div></div></main></div><?php
require __DIR__.'/../../includes/footer.php';
?>