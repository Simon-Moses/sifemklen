<?php
$pageTitle='Register';
require 'includes/header.php';
require 'includes/navbar.php';
$err=flash('error');
?><main class="auth-page"><div class="auth-card"><h2>Create your account</h2><p class="text-muted">Book and track SIFEMKLEN services online.</p><?php
if($err):
?><div class="alert alert-danger"><?=e($err)?></div><?php
endif;
?><form method="post" action="<?=base_url('auth/register_process.php')?>" class="needs-validation" novalidate><input type="hidden" name="csrf" value="<?=csrf_token()?>"><div class="mb-3"><label>Full name</label><input required class="form-control" name="full_name"></div><div class="mb-3"><label>Email</label><input required type="email" class="form-control" name="email"></div><div class="mb-3"><label>Phone</label><input required class="form-control" name="phone"></div><div class="mb-3"><label>Address</label><textarea required class="form-control" name="address"></textarea></div><div class="row"><div class="col-md-6 mb-3"><label>Password</label><input required minlength="8" type="password" class="form-control" name="password"></div><div class="col-md-6 mb-3"><label>Confirm password</label><input required minlength="8" type="password" class="form-control" name="confirm_password"></div></div><button class="btn btn-teal w-100">Create Account</button></form><p class="mt-3 text-center">Already registered? <a href="<?=base_url('login.php')?>">Login</a></p></div></main><?php
require 'includes/footer.php';
?>