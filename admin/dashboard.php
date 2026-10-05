<?php
require_once __DIR__.'/../includes/auth.php';
$u=require_login('admin');
$cards=[['Customers',$pdo->query("SELECT COUNT(*) FROM customers WHERE status='Active'")->fetchColumn(),'bi-people'],['Services',$pdo->query("SELECT COUNT(*) FROM services WHERE status='Active'")->fetchColumn(),'bi-bucket'],['Bookings',$pdo->query('SELECT COUNT(*) FROM bookings')->fetchColumn(),'bi-calendar-check'],['Pending',$pdo->query("SELECT COUNT(*) FROM bookings WHERE status='Pending'")->fetchColumn(),'bi-hourglass'],['Completed',$pdo->query("SELECT COUNT(*) FROM bookings WHERE status='Completed'")->fetchColumn(),'bi-check2-circle'],['Revenue',$pdo->query("SELECT COALESCE(SUM(amount),0) FROM payments WHERE payment_status='Paid'")->fetchColumn(),'bi-cash-stack'],['Active Staff',$pdo->query("SELECT COUNT(*) FROM staff WHERE status='Active'")->fetchColumn(),'bi-person-badge']];
$recent=$pdo->query("SELECT b.*,u.full_name,s.service_name FROM bookings b JOIN customers c ON c.id=b.customer_id JOIN users u ON u.id=c.user_id JOIN services s ON s.id=b.service_id ORDER BY b.created_at DESC LIMIT 8")->fetchAll();
$status=$pdo->query('SELECT status,COUNT(*) n FROM bookings GROUP BY status ORDER BY n DESC')->fetchAll();
$popular=$pdo->query("SELECT s.service_name,COUNT(b.id) n FROM services s LEFT JOIN bookings b ON b.service_id=s.id GROUP BY s.id ORDER BY n DESC LIMIT 5")->fetchAll();
$logs=$pdo->query("SELECT a.*,u.full_name FROM activity_logs a LEFT JOIN users u ON u.id=a.user_id ORDER BY a.created_at DESC LIMIT 8")->fetchAll();
$pageTitle='Admin Dashboard';
require __DIR__.'/../includes/header.php';
?><div class="app-shell"><?php
require __DIR__.'/../includes/admin_sidebar.php';
?><main class="app-main"><div class="topbar"><div><span class="eyebrow">ADMINISTRATION</span><h2>Business overview</h2></div><span class="text-muted small"><?=date('l, d M Y')?></span></div><div class="row g-3"><?php
foreach($cards as $c):
?><div class="col-6 col-xl-3"><div class="kpi"><div class="icon"><i class="bi <?=$c[2]?>"></i></div><span><?=e($c[0])?></span><strong><?=is_numeric($c[1])&&$c[0]==='Revenue'?money($c[1]):e($c[1])?></strong></div></div><?php
endforeach;
?></div><div class="row g-4 mt-1"><div class="col-xl-8"><div class="panel"><div class="panel-head"><h5>Recent bookings</h5><a href="<?=base_url('admin/bookings/index.php')?>">Manage</a></div><div class="table-responsive"><table class="table"><thead><tr><th>Reference</th><th>Customer</th><th>Service</th><th>Date</th><th>Amount</th><th>Status</th></tr></thead><tbody><?php
foreach($recent as $r):
?><tr><td><?=e($r['booking_reference'])?></td><td><?=e($r['full_name'])?></td><td><?=e($r['service_name'])?></td><td><?=e($r['service_date'])?></td><td><?=money($r['estimated_amount'])?></td><td><?=status_badge($r['status'])?></td></tr><?php
endforeach;
?></tbody></table></div></div></div><div class="col-xl-4"><div class="panel"><h5>Booking status</h5><?php
foreach($status as $s):
?><div class="d-flex justify-content-between py-2 border-bottom"><span><?=e($s['status'])?></span><b><?=e($s['n'])?></b></div><?php
endforeach;
?></div><div class="panel mt-4"><h5>Popular services</h5><?php
foreach($popular as $p):
?><div class="d-flex justify-content-between py-2 border-bottom"><span><?=e($p['service_name'])?></span><b><?=e($p['n'])?></b></div><?php
endforeach;
?></div></div></div><div class="panel mt-4"><div class="panel-head"><h5>Recent activity</h5><span class="text-muted small">Audit trail</span></div><?php
foreach($logs as $l):
?><div class="activity"><i class="bi bi-dot"></i><div><b><?=e($l['activity'])?></b><p><?=e($l['description'])?></p><small><?=e($l['full_name']??'System')?> · <?=e($l['created_at'])?></small></div></div><?php
endforeach;
?></div></main></div><?php
require __DIR__.'/../includes/footer.php';
?>