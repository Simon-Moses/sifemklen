<?php
$pageTitle='Services | SIFEMKLEN';
require 'includes/header.php';
require 'includes/navbar.php';
$q=post('q');
if($q)  {
$s = $pdo->prepare("
    SELECT 
        s.*,
        c.name AS category_name
    FROM services s
    JOIN service_categories c ON c.id = s.category_id
    WHERE s.status = 'Active'
      AND (
          s.service_name LIKE ?
          OR c.name LIKE ?
      )
    ORDER BY s.service_name
");
    $s->execute(["%$q%","%$q%"]);
    $services=$s->fetchAll();
} else $services = $pdo->query("
    SELECT 
        s.*,
        c.name AS category_name
    FROM services s
    JOIN service_categories c ON c.id = s.category_id
    WHERE s.status = 'Active'
    ORDER BY s.service_name
")->fetchAll();
?><main class="container py-5"><div class="section-head"><span>OUR SERVICES</span><h1>Find the right cleaning service</h1></div><form method="post" class="row g-2 mb-4"><div class="col-md-10"><input class="form-control" name="q" value="<?=e($q)?>" placeholder="Search services..."></div><div class="col-md-2"><button class="btn btn-teal w-100">Search</button></div></form><div class="row g-4"><?php
foreach($services as $s):
?><div class="col-md-6 col-lg-4"><div class="service-card h-100"><div class="service-icon"><i class="bi bi-bucket"></i></div><span class="small text-muted"><?=e($s['category_name'])?></span><h5><?=e($s['service_name'])?></h5><p><?=e($s['description'])?></p><div class="small text-muted mb-3">Duration: <?=e($s['duration'])?></div><div class="d-flex justify-content-between align-items-center"><b><?=money($s['price'])?></b><a class="btn btn-sm btn-teal" href="<?=base_url('booking.php?service_id='.$s['id'])?>">Book Now</a></div></div></div><?php
endforeach;
if(!$services):
?><div class="col-12"><div class="empty-state">No services found.</div></div><?php
endif;
?></div></main><?php
require 'includes/footer.php';
?>