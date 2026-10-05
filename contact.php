<?php
$pageTitle='Contact | SIFEMKLEN';
require 'includes/header.php';
require 'includes/navbar.php';
?><main class="container py-5"><div class="section-head"><span>CONTACT</span><h1>Let's keep your space fresh.</h1></div><div class="row g-4"><div class="col-md-6"><div class="soft-card"><h5>Contact information</h5><p><i class="bi bi-telephone"></i> <?=e(setting('phone'))?></p><p><i class="bi bi-envelope"></i> <?=e(setting('email'))?></p><p><i class="bi bi-geo-alt"></i> <?=e(setting('address'))?></p><p><i class="bi bi-clock"></i> <?=e(setting('business_hours'))?></p></div></div><div class="col-md-6"><div class="soft-card"><h5>Book online</h5><p>For the fastest service, create an account and submit your cleaning request online.</p><a class="btn btn-teal" href="<?=base_url('register.php')?>">Create Account</a></div></div></div></main><?php
require 'includes/footer.php';
?>