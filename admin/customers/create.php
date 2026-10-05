<?php
require_once __DIR__.'/../../includes/auth.php';
$u=require_login('admin');
if($_SERVER['REQUEST_METHOD']==='POST')  {
    verify_csrf();
    $name=post('full_name');
    $email=filter_var(post('email'),FILTER_VALIDATE_EMAIL);
    $phone=post('phone');
    $address=post('address');
    $password=$_POST['password']??'Admin@12345';
    if(!$name||!$email||!$phone||!$address)  {
        flash('error','All fields are required.');
    } else  {
        $s=$pdo->prepare('SELECT id FROM users WHERE email=?');
        $s->execute([$email]);
        if($s->fetch())flash('error','Email already exists.');
        else  {
            try  {
                $pdo->beginTransaction();
                $s=$pdo->prepare("INSERT INTO users(full_name,email,phone,password,role,status) VALUES(?,?,?,?, 'customer','Active')");
                $s->execute([$name,$email,$phone,password_hash($password,PASSWORD_DEFAULT)]);
                $uid=$pdo->lastInsertId();
                $s=$pdo->prepare("INSERT INTO customers(user_id,address,status) VALUES(?,?, 'Active')");
                $s->execute([$uid,$address]);
                $pdo->commit();
                log_activity('Customer creation','Admin created customer '.$name);
                flash('success','Customer created. Temporary password: '.$password);
                redirect('admin/customers/index.php');
            } catch(Throwable $e)  {
                if($pdo->inTransaction())$pdo->rollBack();
                flash('error','Unable to create customer.');
            }
        }
    }
}
$err=flash('error');
$pageTitle='Add Customer';
require __DIR__.'/../../includes/header.php';
?><div class="app-shell"><?php
require __DIR__.'/../../includes/admin_sidebar.php';
?><main class="app-main"><div class="topbar"><h2>Add customer</h2></div><?php
if($err):
?><div class="alert alert-danger"><?=e($err)?></div><?php
endif;
?><div class="panel"><form method="post"><input type="hidden" name="csrf" value="<?=csrf_token()?>"><div class="row g-3"><div class="col-md-6"><label>Full name</label><input required name="full_name" class="form-control"></div><div class="col-md-6"><label>Email</label><input required type="email" name="email" class="form-control"></div><div class="col-md-6"><label>Phone</label><input required name="phone" class="form-control"></div><div class="col-md-6"><label>Temporary password</label><input minlength="8" name="password" class="form-control" value="Admin@12345"></div><div class="col-12"><label>Address</label><textarea required name="address" class="form-control"></textarea></div><div class="col-12"><button class="btn btn-teal">Create Customer</button></div></div></form></div></main></div><?php
require __DIR__.'/../../includes/footer.php';
?>