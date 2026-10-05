<?php
require_once __DIR__.'/../../includes/auth.php';
require_login('admin');
if($_SERVER['REQUEST_METHOD']==='POST')  {
    verify_csrf();
    $n=post('category_name');
    $d=post('description');
    if($n)  {
        $s=$pdo->prepare('INSERT INTO service_categories(category_name,description,status) VALUES(?,?,?)');
        $s->execute([$n,$d,post('status','Active')]);
        log_activity('Category creation','Created category '.$n);
        redirect('admin/categories/index.php');
    }
}
$pageTitle='Add Category';
require __DIR__.'/../../includes/header.php';
?><div class="app-shell"><?php
require __DIR__.'/../../includes/admin_sidebar.php';
?><main class="app-main"><div class="topbar"><h2>Add category</h2></div><div class="panel"><form method="post"><input type="hidden" name="csrf" value="<?=csrf_token()?>"><div class="mb-3"><label>Name</label><input required name="category_name" class="form-control"></div><div class="mb-3"><label>Description</label><textarea name="description" class="form-control"></textarea></div><button class="btn btn-teal">Create</button></form></div></main></div><?php
require __DIR__.'/../../includes/footer.php';
?>