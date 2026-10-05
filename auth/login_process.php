<?php
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../includes/functions.php';
if($_SERVER['REQUEST_METHOD']!=='POST') redirect('login.php');
verify_csrf();
$email=filter_var(post('email'),FILTER_VALIDATE_EMAIL);
$password=$_POST['password']??'';
if(!$email||$password==='')  {
    flash('error','Enter a valid email and password.');
    redirect('login.php');
}
$s=$pdo->prepare('SELECT * FROM users WHERE email=? LIMIT 1');
$s->execute([$email]);
$u=$s->fetch();
if(!$u||!password_verify($password,$u['password'])||$u['status']!=='Active')  {
    flash('error','Invalid login details or inactive account.');
    redirect('login.php');
}
session_regenerate_id(true);
$_SESSION['user_id']=$u['id'];
$_SESSION['role']=$u['role'];
log_activity($u['role']==='admin'?'Admin login':'Customer login','Successful login.');
redirect($u['role']==='admin'?'admin/dashboard.php':'customer/dashboard.php');
?>