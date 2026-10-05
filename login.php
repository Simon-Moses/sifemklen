<?php
$pageTitle='Login';
require 'includes/header.php';
require 'includes/navbar.php';
$err=flash('error');
$ok=flash('success');
?><main class="auth-page"><div class="auth-card"><h2>Welcome back</h2><p class="text-muted">Sign in to manage your SIFEMKLEN account.</p><?php
if($err):
?><div class="alert alert-danger"><?=e($err)?></div><?php
endif;
?><?php
if($ok):
?><div class="alert alert-success"><?=e($ok)?></div><?php
endif;
?><form method="post" action="<?=base_url('auth/login_process.php')?>"><input type="hidden" name="csrf" value="<?=csrf_token()?>"><div class="mb-3"><label>Email</label><input required type="email" class="form-control" name="email"></div><div class="mb-3"><label>Password</label><input required type="password" class="form-control" name="password"></div><button class="btn btn-teal w-100">Login</button></form><p class="mt-3 text-center">New customer? <a href="<?=base_url('register.php')?>">Create an account</a></p></div></main><?php
require 'includes/footer.php';
?>