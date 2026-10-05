<?php
$pageTitle='SIFEMKLEN | Professional Cleaning Services';
require 'includes/header.php';
require 'includes/navbar.php';
$services = $pdo->query("
    SELECT 
        s.*,
        c.name AS category_name
    FROM services s
    JOIN service_categories c ON c.id = s.category_id
    WHERE s.status = 'Active'
    ORDER BY s.id DESC
    LIMIT 6
")->fetchAll();
$stats=['customers'=>$pdo->query("SELECT COUNT(*) FROM customers WHERE status='Active'")->fetchColumn(),'services'=>$pdo->query("SELECT COUNT(*) FROM services WHERE status='Active'")->fetchColumn(),'completed'=>$pdo->query("SELECT COUNT(*) FROM bookings WHERE status='Completed'")->fetchColumn(),'staff'=>$pdo->query("SELECT COUNT(*) FROM staff WHERE status='Active'")->fetchColumn()];
?>
<section class="hero"><div class="container py-5"><div class="row align-items-center g-5"><div class="col-lg-7"><span class="eyebrow">CLEANING SERVICES</span><h1>Professional Cleaning Services You Can Trust</h1><p class="lead">Reliable, affordable and professional cleaning services for homes, offices and businesses.</p><div class="d-flex gap-2"><a class="btn btn-teal btn-lg" href="<?=base_url('booking.php')?>">Book a Service</a><a class="btn btn-outline-dark btn-lg" href="<?=base_url('services.php')?>">View Services</a></div></div><div class="col-lg-5"><div class="hero-card"><i class="bi bi-stars"></i><h4>Clean Spaces, Better Living.</h4><p>Schedule your cleaning, follow progress and keep every booking organized.</p><div class="mini-check"><i class="bi bi-check-circle-fill"></i> Professional team</div><div class="mini-check"><i class="bi bi-check-circle-fill"></i> Flexible scheduling</div><div class="mini-check"><i class="bi bi-check-circle-fill"></i> Transparent pricing</div></div></div></div></div></section>
<section class="container py-5"><div class="row g-3"><?php
foreach($stats as $k=>$v):
?><div class="col-6 col-lg-3"><div class="stat-card"><strong><?=e($v)?></strong><span><?=e(ucwords(str_replace('_',' ',$k)))?></span></div></div><?php
endforeach;
?></div></section>
<section class="container py-5"><div class="section-head"><span>OUR SERVICES</span><h2>Cleaning solutions for every space</h2></div><div class="row g-4"><?php
foreach($services as $s):
?><div class="col-md-6 col-lg-4"><div class="service-card h-100"><div class="service-icon"><i class="bi bi-droplet"></i></div><span class="small text-muted"><?=e($s['category_name'])?></span><h5><?=e($s['service_name'])?></h5><p><?=e($s['description'])?></p><div class="d-flex justify-content-between align-items-center"><b><?=money($s['price'])?></b><a href="<?=base_url('booking.php?service_id='.$s['id'])?>" class="btn btn-sm btn-outline-teal">Book</a></div></div></div><?php
endforeach;
?></div></section>
<section class="container py-5"><div class="row g-4"><div class="col-lg-6"><div class="section-head"><span>HOW IT WORKS</span><h2>Simple from booking to completion</h2></div><div class="steps"><div><b>01</b><p><strong>Choose a Service</strong><br>Pick the cleaning solution you need.</p></div><div><b>02</b><p><strong>Book Your Service</strong><br>Select date, time and address.</p></div><div><b>03</b><p><strong>Staff Assignment</strong><br>Our team prepares for the job.</p></div><div><b>04</b><p><strong>Cleaning & Completion</strong><br>Track progress until your job is complete.</p></div></div></div><div class="col-lg-6"><div class="cta-box"><span>READY FOR A CLEANER SPACE?</span><h2>Let SIFEMKLEN handle the work.</h2><a class="btn btn-light" href="<?=base_url('booking.php')?>">Book a Service</a></div></div></div></section><?php
require 'includes/footer.php';
?>
