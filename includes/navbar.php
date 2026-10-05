<nav class="navbar navbar-expand-lg bg-white sticky-top border-bottom"><div class="container"><a class="navbar-brand fw-bold text-teal" href="<?=base_url()?>"><i class="bi bi-stars"></i> SIFEMKLEN</a><button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav"><span class="navbar-toggler-icon"></span></button><div id="nav" class="collapse navbar-collapse"><ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2"><li><a class="nav-link" href="<?=base_url()?>">Home</a></li><li><a class="nav-link" href="<?=base_url('about.php')?>">About</a></li><li><a class="nav-link" href="<?=base_url('services.php')?>">Services</a></li><li><a class="nav-link" href="<?=base_url('booking.php')?>">Book</a></li><li><a class="nav-link" href="<?=base_url('contact.php')?>">Contact</a></li><?php
if(current_user()):
?><li><a class="btn btn-teal" href="<?=base_url(current_user()['role']==='admin'?'admin/dashboard.php':'customer/dashboard.php')?>">Dashboard</a></li><li><a class="nav-link" href="<?=base_url('logout.php')?>">Logout</a></li><?php
else:
?><li><a class="nav-link" href="<?=base_url('login.php')?>">Login</a></li><li><a class="btn btn-teal" href="<?=base_url('register.php')?>">Register</a></li><?php
endif;
?></ul></div></div></nav>
