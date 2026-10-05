<?php
require_once __DIR__.'/../../includes/auth.php';
require_login('admin');
$rows=$pdo->query('SELECT c.*,COUNT(s.id) service_count FROM service_categories c LEFT JOIN services s ON s.category_id=c.id GROUP BY c.id ORDER BY c.id DESC')->fetchAll();
$pageTitle='Categories';
require __DIR__.'/../../includes/header.php';
?><div class="app-shell"><?php
require __DIR__.'/../../includes/admin_sidebar.php';
?><main class="app-main"><div class="topbar"><h2>Categories</h2><a class="btn btn-teal" href="<?=base_url('admin/categories/create.php')?>">Add Category</a></div><div class="panel"><div class="table-responsive"><table class="table"><thead><tr><th>Name</th><th>Description</th><th>Services</th><th>Status</th><th>Actions</th></tr></thead><tbody><?php
foreach($rows as $r):
?><tr><td><?=e($r['category_name'])?></td><td><?=e($r['description'])?></td><td><?=e($r['service_count'])?></td><td><?=status_badge($r['status'])?></td><td><a class="btn btn-sm btn-outline-teal" href="<?=base_url('admin/categories/edit.php?id='.$r['id'])?>">Edit</a> <a onclick="return confirm('Delete this category?')" class="btn btn-sm btn-outline-danger" href="<?=base_url('admin/categories/delete.php?id='.$r['id'])?>">Delete</a></td></tr><?php
endforeach;
?></tbody></table></div></div></main></div><?php
require __DIR__.'/../../includes/footer.php';
?>