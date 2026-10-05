<?php
require_once __DIR__.'/../../includes/auth.php';
require_login('admin');
$id=(int)($_GET['id']??0);
$s=$pdo->prepare('SELECT * FROM service_categories WHERE id=?');
$s->execute([$id]);
$r=$s->fetch();
if(!$r)exit('Category not found.');
if($_SERVER['REQUEST_METHOD']==='POST')  {
    verify_csrf();
    $s=$pdo->prepare('UPDATE service_categories SET category_name=?,description=?,status=?,updated_at=NOW() WHERE id=?');
    $s->execute([post('category_name'),post('description'),post('status'),$id]);
    log_activity('Category update','Updated category '.$id);
    redirect('admin/categories/index.php');
}
$pageTitle='Edit Category';
require __DIR__.'/../../includes/header.php';
?><div class="app-shell"><?php
require __DIR__.'/../../includes/admin_sidebar.php';
?><main class="app-main"><div class="topbar"><h2>Edit category</h2></div><div class="panel"><form method="post"><input type="hidden" name="csrf" value="<?=csrf_token()?>"><div class="mb-3"><label>Name</label><input required name="category_name" class="form-control" value="<?=e($r['category_name'])?>"></div><div class="mb-3"><label>Description</label><textarea name="description" class="form-control"><?=e($r['description'])?></textarea></div><div class="mb-3"><label>Status</label><select name="status" class="form-select"><option <?=$r['status']==='Active'?'selected':''?>>Active</option><option <?=$r['status']==='Inactive'?'selected':''?>>Inactive</option></select></div><button class="btn btn-teal">Save</button></form></div></main></div><?php
require __DIR__.'/../../includes/footer.php';
?>