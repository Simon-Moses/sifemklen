<?php
require_once __DIR__.'/../../includes/auth.php';
require_login('admin');
$rows=$pdo->query("SELECT st.*,(SELECT COUNT(*) FROM staff_assignments a WHERE a.staff_id=st.id AND a.status IN ('Assigned','Working')) workload FROM staff st ORDER BY st.id DESC")->fetchAll();
$pageTitle='Staff';
require __DIR__.'/../../includes/header.php';
?><div class="app-shell"><?php
require __DIR__.'/../../includes/admin_sidebar.php';
?><main class="app-main"><div class="topbar"><h2>Staff</h2><a class="btn btn-teal" href="<?=base_url('admin/staff/create.php')?>">Add Staff</a></div><div class="panel"><div class="table-responsive"><table class="table"><thead><tr><th>Name</th><th>Phone</th><th>Position</th><th>Workload</th><th>Status</th><th>Actions</th></tr></thead><tbody><?php
foreach($rows as $r):
?><tr><td><?=e($r['full_name'])?></td><td><?=e($r['phone'])?></td><td><?=e($r['position'])?></td><td><?=e($r['workload'])?></td><td><?=status_badge($r['status'])?></td><td><a class="btn btn-sm btn-outline-teal" href="<?=base_url('admin/staff/edit.php?id='.$r['id'])?>">Edit</a> <a onclick="return confirm('Deactivate staff member?')" class="btn btn-sm btn-outline-danger" href="<?=base_url('admin/staff/delete.php?id='.$r['id'])?>">Deactivate</a></td></tr><?php
endforeach;
?></tbody></table></div></div></main></div><?php
require __DIR__.'/../../includes/footer.php';
?>