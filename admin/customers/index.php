<?php
require_once __DIR__.'/../../includes/auth.php';
$u=require_login('admin');
$q=post('q');
$sql="SELECT c.*,u.full_name,u.email,u.phone,(SELECT COUNT(*) FROM bookings b WHERE b.customer_id=c.id) booking_count FROM customers c JOIN users u ON u.id=c.user_id";
$params=[];
if($q)  {
    $sql.=" WHERE u.full_name LIKE ? OR u.email LIKE ? OR u.phone LIKE ?";
    $params=["%$q%","%$q%","%$q%"];
}
$sql.=' ORDER BY c.id DESC';
$s=$pdo->prepare($sql);
$s->execute($params);
$rows=$s->fetchAll();
$pageTitle='Customers';
require __DIR__.'/../../includes/header.php';
?><div class="app-shell"><?php
require __DIR__.'/../../includes/admin_sidebar.php';
?><main class="app-main"><div class="topbar"><div><span class="eyebrow">CUSTOMERS</span><h2>Customer management</h2></div><a class="btn btn-teal" href="<?=base_url('admin/customers/create.php')?>">Add Customer</a></div><div class="panel"><form method="post" class="row g-2 mb-3"><div class="col-md-10"><input class="form-control" name="q" value="<?=e($q)?>" placeholder="Search name, email or phone"></div><div class="col-md-2"><button class="btn btn-outline-teal w-100">Search</button></div></form><div class="table-responsive"><table class="table align-middle"><thead><tr><th>ID</th><th>Customer</th><th>Phone</th><th>Bookings</th><th>Status</th><th>Created</th><th>Actions</th></tr></thead><tbody><?php
foreach($rows as $r):
?><tr><td>#<?=e($r['id'])?></td><td><b><?=e($r['full_name'])?></b><br><small><?=e($r['email'])?></small></td><td><?=e($r['phone'])?></td><td><?=e($r['booking_count'])?></td><td><?=status_badge($r['status'])?></td><td><?=e($r['created_at'])?></td><td><a class="btn btn-sm btn-outline-secondary" href="<?=base_url('admin/customers/view.php?id='.$r['id'])?>">View</a> <a class="btn btn-sm btn-outline-teal" href="<?=base_url('admin/customers/edit.php?id='.$r['id'])?>">Edit</a> <a onclick="return confirm('Deactivate this customer?')" class="btn btn-sm btn-outline-danger" href="<?=base_url('admin/customers/delete.php?id='.$r['id'])?>">Deactivate</a></td></tr><?php
endforeach;
if(!$rows):
?><tr><td colspan="7" class="text-center text-muted">No customers found.</td></tr><?php
endif;
?></tbody></table></div></div></main></div><?php
require __DIR__.'/../../includes/footer.php';
?>