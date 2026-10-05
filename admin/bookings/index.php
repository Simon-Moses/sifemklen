<?php
require_once __DIR__.'/../../includes/auth.php';
require_login('admin');
$q=post('q');
$status=post('status');
$sql="SELECT b.*,u.full_name,s.service_name FROM bookings b JOIN customers c ON c.id=b.customer_id JOIN users u ON u.id=c.user_id JOIN services s ON s.id=b.service_id WHERE 1";
$params=[];
if($q)  {
    $sql.=' AND (b.booking_reference LIKE ? OR u.full_name LIKE ? OR s.service_name LIKE ?)';
    $params=["%$q%","%$q%","%$q%"];
}
$allowed=['Pending','Confirmed','Assigned','In Progress','Completed','Cancelled'];
if(in_array($status,$allowed,true))  {
    $sql.=' AND b.status=?';
    $params[]=$status;
}
$sql.=' ORDER BY b.created_at DESC';
$s=$pdo->prepare($sql);
$s->execute($params);
$rows=$s->fetchAll();
$pageTitle='Bookings';
require __DIR__.'/../../includes/header.php';
?><div class="app-shell"><?php
require __DIR__.'/../../includes/admin_sidebar.php';
?><main class="app-main"><div class="topbar"><h2>Bookings</h2></div><div class="panel"><form method="post" class="row g-2 mb-3"><div class="col-md-7"><input class="form-control" name="q" value="<?=e($q)?>" placeholder="Search reference, customer or service"></div><div class="col-md-3"><select class="form-select" name="status"><option value="">All statuses</option><?php
foreach($allowed as $x):
?><option <?=$status===$x?'selected':''?>><?=e($x)?></option><?php
endforeach;
?></select></div><div class="col-md-2"><button class="btn btn-outline-teal w-100">Filter</button></div></form><div class="table-responsive"><table class="table"><thead><tr><th>Reference</th><th>Customer</th><th>Service</th><th>Date</th><th>Amount</th><th>Status</th><th>Actions</th></tr></thead><tbody><?php
foreach($rows as $r):
?><tr><td><?=e($r['booking_reference'])?></td><td><?=e($r['full_name'])?></td><td><?=e($r['service_name'])?></td><td><?=e($r['service_date'])?></td><td><?=money($r['estimated_amount'])?></td><td><?=status_badge($r['status'])?></td><td><a class="btn btn-sm btn-outline-teal" href="<?=base_url('admin/bookings/view.php?id='.$r['id'])?>">View</a></td></tr><?php
endforeach;
if(!$rows):
?><tr><td colspan="7" class="text-center text-muted">No bookings found.</td></tr><?php
endif;
?></tbody></table></div></div></main></div><?php
require __DIR__.'/../../includes/footer.php';
?>