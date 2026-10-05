<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
if(!current_user())  {
    flash('error','Please login or register before booking a service.');
    redirect('login.php');
}
if(current_user()['role']!=='customer') redirect('admin/dashboard.php');
$pageTitle='Book a Service';
require 'includes/header.php';
require 'includes/navbar.php';
$services=$pdo->query("SELECT s.*,c.category_name FROM services s JOIN service_categories c ON c.id=s.category_id WHERE s.status='Active' ORDER BY s.service_name")->fetchAll();
$sid=(int)($_GET['service_id']??0);
?><main class="container py-5"><div class="section-head"><span>BOOKING</span><h1>Schedule your cleaning</h1></div><div class="soft-card"><form method="post" action="<?=base_url('customer/book_service.php')?>"><input type="hidden" name="service_id" value="<?=$sid?>"><p class="text-muted">You will be redirected to your booking details after submission.</p><button class="btn btn-teal">Continue to booking form</button></form></div></main><?php
require 'includes/footer.php';
?>