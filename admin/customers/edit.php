<?php
require_once __DIR__.'/../../includes/auth.php';
$u=require_login('admin');
$id=(int)($_GET['id']??$_POST['id']??0);
$s=$pdo->prepare('SELECT c.*,u.full_name,u.email,u.phone FROM customers c JOIN users u ON u.id=c.user_id WHERE c.id=?');
$s->execute([$id]);
$r=$s->fetch();
if(!$r)exit('Customer not found.');
if($_SERVER['REQUEST_METHOD']==='POST')  {
    verify_csrf();
    $name=post('full_name');
    $email=filter_var(post('email'),FILTER_VALIDATE_EMAIL);
    $phone=post('phone');
    $address=post('address');
    $status=post('status');
    $s=$pdo->prepare('SELECT id FROM users WHERE email=? AND id<>?');
    $s->execute([$email,$r['user_id']]);
    if($s->fetch())flash('error','Email already exists.');
    else  {
        $pdo->beginTransaction();
        $s=$pdo->prepare('UPDATE users SET full_name=?,email=?,phone=?,updated_at=NOW() WHERE id=?');
        $s->execute([$name,$email,$phone,$r['user_id']]);
        $s=$pdo->prepare('UPDATE customers SET address=?,status=?,updated_at=NOW() WHERE id=?');
        $s->execute([$address,$status,$id]);
        $pdo->commit();
        log_activity('Customer update','Admin updated customer '.$id);
        flash('success','Customer updated.');
        redirect('admin/customers/index.php');
    }
}
$pageTitle='Edit Customer';
require __DIR__.'/../../includes/header.php';
?><div class="app-shell"><?php
require __DIR__.'/../../includes/admin_sidebar.php';
?><main class="app-main"><div class="topbar"><h2>Edit customer</h2></div><div class="panel"><form method="post"><input type="hidden" name="csrf" value="<?=csrf_token()?>"><input type="hidden" name="id" value="<?=$id?>"><div class="row g-3"><div class="col-md-6"><label>Full name</label><input required name="full_name" class="form-control" value="<?=e($r['full_name'])?>"></div><div class="col-md-6"><label>Email</label><input required type="email" name="email" class="form-control" value="<?=e($r['email'])?>"></div><div class="col-md-6"><label>Phone</label><input required name="phone" class="form-control" value="<?=e($r['phone'])?>"></div><div class="col-md-6"><label>Status</label><select name="status" class="form-select"><option <?=$r['status']==='Active'?'selected':''?>>Active</option><option <?=$r['status']==='Inactive'?'selected':''?>>Inactive</option></select></div><div class="col-12"><label>Address</label><textarea required name="address" class="form-control"><?=e($r['address'])?></textarea></div><div class="col-12"><button class="btn btn-teal">Save Changes</button></div></div></form></div></main></div><?php
require __DIR__.'/../../includes/footer.php';
?>