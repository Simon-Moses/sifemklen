<?php
require_once __DIR__.'/../../includes/auth.php';
require_login('admin');
$from=post('from',date('Y-m-01'));
$to=post('to',date('Y-m-d'));
$s=$pdo->prepare('SELECT COUNT(*) FROM bookings WHERE service_date BETWEEN ? AND ?');
$s->execute([$from,$to]);
$total=$s->fetchColumn();
$s=$pdo->prepare("SELECT COUNT(*) FROM bookings WHERE status='Completed' AND service_date BETWEEN ? AND ?");
$s->execute([$from,$to]);
$completed=$s->fetchColumn();
$s=$pdo->prepare("SELECT COUNT(*) FROM bookings WHERE status='Cancelled' AND service_date BETWEEN ? AND ?");
$s->execute([$from,$to]);
$cancelled=$s->fetchColumn();
$s=$pdo->prepare("SELECT COALESCE(SUM(amount),0) FROM payments WHERE payment_status='Paid' AND payment_date BETWEEN ? AND ?");
$s->execute([$from,$to.' 23:59:59']);
$revenue=$s->fetchColumn();
$popular=$pdo->prepare("SELECT s.service_name,COUNT(b.id) n,COALESCE(SUM(b.estimated_amount),0) revenue FROM services s JOIN bookings b ON b.service_id=s.id WHERE b.service_date BETWEEN ? AND ? GROUP BY s.id ORDER BY n DESC");
$popular->execute([$from,$to]);
$popular=$popular->fetchAll();
$newc=$pdo->prepare('SELECT COUNT(*) FROM customers WHERE created_at BETWEEN ? AND ?');
$newc->execute([$from,$to.' 23:59:59']);
$newc=$newc->fetchColumn();
$pageTitle='Reports';
require __DIR__.'/../../includes/header.php';
?><div class="app-shell"><?php
require __DIR__.'/../../includes/admin_sidebar.php';
?><main class="app-main"><div class="topbar"><div><span class="eyebrow">REPORTING</span><h2>Business reports</h2></div></div><div class="panel mb-4"><form method="post" class="row g-2"><div class="col-md-5"><label>From</label><input type="date" name="from" class="form-control" value="<?=e($from)?>"></div><div class="col-md-5"><label>To</label><input type="date" name="to" class="form-control" value="<?=e($to)?>"></div><div class="col-md-2 d-flex align-items-end"><button class="btn btn-teal w-100">Run Report</button></div></form></div><div class="row g-3"><div class="col-6 col-lg-3"><div class="kpi"><span>Total bookings</span><strong><?=$total?></strong></div></div><div class="col-6 col-lg-3"><div class="kpi"><span>Completed</span><strong><?=$completed?></strong></div></div><div class="col-6 col-lg-3"><div class="kpi"><span>Cancelled</span><strong><?=$cancelled?></strong></div></div><div class="col-6 col-lg-3"><div class="kpi"><span>Revenue</span><strong><?=money($revenue)?></strong></div></div></div><div class="row g-4 mt-1"><div class="col-lg-8"><div class="panel"><h5>Service performance</h5><div class="table-responsive"><table class="table"><thead><tr><th>Service</th><th>Bookings</th><th>Revenue</th></tr></thead><tbody><?php
foreach($popular as $p):
?><tr><td><?=e($p['service_name'])?></td><td><?=e($p['n'])?></td><td><?=money($p['revenue'])?></td></tr><?php
endforeach;
?></tbody></table></div></div></div><div class="col-lg-4"><div class="panel"><h5>Customer report</h5><div class="display-5 fw-bold"><?=$newc?></div><p class="text-muted">New customers in selected period.</p></div></div></div></main></div><?php
require __DIR__.'/../../includes/footer.php';
?>