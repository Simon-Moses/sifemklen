<?php
require_once __DIR__.'/../includes/auth.php';
$u=require_login('customer');
$cid=get_customer_id_for_user($u['id']);
$s=$pdo->prepare('SELECT c.address FROM customers c WHERE c.id=?');
$s->execute([$cid]);
$c=$s->fetch();
$err='';
$ok='';
if($_SERVER['REQUEST_METHOD']==='POST')  {
    verify_csrf();
    $name=post('full_name');
    $email=filter_var(post('email'),FILTER_VALIDATE_EMAIL);
    $phone=post('phone');
    $address=post('address');
    $new=$_POST['new_password']??'';
    $confirm=$_POST['confirm_password']??'';
    if(!$name||!$email||!$phone||!$address)  {
        $err='Complete all fields.';
    } else  {
        $s=$pdo->prepare('SELECT id FROM users WHERE email=? AND id<>?');
        $s->execute([$email,$u['id']]);
        if($s->fetch())$err='Email is already in use.';
        else  {
            try  {
                $pdo->beginTransaction();
                $s=$pdo->prepare('UPDATE users SET full_name=?,email=?,phone=?,updated_at=NOW() WHERE id=?');
                $s->execute([$name,$email,$phone,$u['id']]);
                $s=$pdo->prepare('UPDATE customers SET address=?,updated_at=NOW() WHERE id=?');
                $s->execute([$address,$cid]);
                if($new!=='')  {
                    if(strlen($new)<8||$new!==$confirm)throw new Exception('Password must be at least 8 characters and match.');
                    $s=$pdo->prepare('UPDATE users SET password=? WHERE id=?');
                    $s->execute([password_hash($new,PASSWORD_DEFAULT),$u['id']]);
                }
                $pdo->commit();
                $ok='Profile updated successfully.';
                log_activity('Customer update','Updated profile.');
                $u=current_user();
            } catch(Throwable $e)  {
                if($pdo->inTransaction())$pdo->rollBack();
                $err=$e->getMessage()==='Password must be at least 8 characters and match.'?$e->getMessage():'Unable to update profile.';
            }
        }
    }
}
$pageTitle='Profile';
require __DIR__.'/../includes/header.php';
?><div class="app-shell"><?php
require __DIR__.'/../includes/customer_sidebar.php';
?><main class="app-main"><div class="topbar"><div><span class="eyebrow">PROFILE</span><h2>My profile</h2></div></div><?php
if($err):
?><div class="alert alert-danger"><?=e($err)?></div><?php
endif;
?><?php
if($ok):
?><div class="alert alert-success"><?=e($ok)?></div><?php
endif;
?><div class="panel"><form method="post"><input type="hidden" name="csrf" value="<?=csrf_token()?>"><div class="row g-3"><div class="col-md-6"><label>Full name</label><input class="form-control" name="full_name" value="<?=e($u['full_name'])?>" required></div><div class="col-md-6"><label>Email</label><input type="email" class="form-control" name="email" value="<?=e($u['email'])?>" required></div><div class="col-md-6"><label>Phone</label><input class="form-control" name="phone" value="<?=e($u['phone'])?>" required></div><div class="col-md-6"><label>Address</label><input class="form-control" name="address" value="<?=e($c['address'])?>" required></div><div class="col-md-6"><label>New password (optional)</label><input type="password" class="form-control" name="new_password"></div><div class="col-md-6"><label>Confirm password</label><input type="password" class="form-control" name="confirm_password"></div><div class="col-12"><button class="btn btn-teal">Save Changes</button></div></div></form></div></main></div><?php
require __DIR__.'/../includes/footer.php';
?>