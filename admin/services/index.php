<?php
require_once __DIR__.'/../../includes/auth.php';
require_login('admin');
$rows=$pdo->query('SELECT s.*,c.category_name FROM services s JOIN service_categories c ON c.id=s.category_id ORDER BY s.id DESC')->fetchAll();
$pageTitle='Services';
require __DIR__.'/../../includes/header.php';
?><div class="app-shell"><?php
require __DIR__.'/../../includes/admin_sidebar.php';
?><main class="app-main"><div class="topbar"><h2>Services</h2><a class="btn btn-teal" href="<?=base_url('admin/services/create.php')?>">Add Service</a></div><div class="panel"><div class="table-responsive"><table class="table"><thead><tr><th>Service</th><th>Category</th><th>Price</th><th>Duration</th><th>Status</th><th>Actions</th></tr></thead><tbody><?php
foreach($rows as $r):
?><tr><td><b><?=e($r['service_name'])?></b><br><small><?=e($r['description'])?></small></td><td><?=e($r['category_name'])?></td><td><?=money($r['price'])?></td><td><?=e($r['duration'])?></td><td><?=status_badge($r['status'])?></td><td><a class="btn btn-sm btn-outline-teal" href="<?=base_url('admin/services/edit.php?id='.$r['id'])?>">Edit</a> <a onclick="return confirm('Delete service?')" class="btn btn-sm btn-outline-danger" href="<?=base_url('admin/services/delete.php?id='.$r['id'])?>">Delete</a></td></tr><?php
endforeach;
?></tbody></table></div></div></main></div><?php
require __DIR__.'/../../includes/footer.php';
?>