<?php
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../includes/functions.php';
if($_SERVER['REQUEST_METHOD']!=='POST') redirect('register.php');
verify_csrf();
$name=post('full_name');
$email=filter_var(post('email'),FILTER_VALIDATE_EMAIL);
$phone=post('phone');
$address=post('address');
$password=$_POST['password']??'';
$confirm=$_POST['confirm_password']??'';
if(!$name||!$email||!$phone||!$address||strlen($password)<8||$password!==$confirm)  {
    flash('error','Complete all fields. Password must be at least 8 characters and match confirmation.');
    redirect('register.php');
}
$s=$pdo->prepare('SELECT id FROM users WHERE email=?');
$s->execute([$email]);
if($s->fetch())  {
    flash('error','An account with that email already exists.');
    redirect('register.php');
}
try  {
    $pdo->beginTransaction();
    $s=$pdo->prepare("INSERT INTO users(full_name,email,phone,password,role,status) VALUES(?,?,?,?, 'customer','Active')");
    $s->execute([$name,$email,$phone,password_hash($password,PASSWORD_DEFAULT)]);
    $uid=$pdo->lastInsertId();
    $s=$pdo->prepare("INSERT INTO customers(user_id,address,status) VALUES(?,?, 'Active')");
    $s->execute([$uid,$address]);
    $pdo->commit();
    $_SESSION['user_id']=$uid;
    $_SESSION['role']='customer';
    log_activity('Customer registration','New customer account created.');
    redirect('customer/dashboard.php');
} catch(Throwable $e)  {
    if($pdo->inTransaction())$pdo->rollBack();
    flash('error','Registration could not be completed.');
    redirect('register.php');
}
?>