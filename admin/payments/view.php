<?php
require_once __DIR__.'/../../includes/auth.php';
require_login('admin');
$id=(int)($_GET['id']??0);
$s=$pdo->prepare("SELECT p.*,b.booking_reference,u.full_name FROM payments p JOIN bookings b ON b.id=p.booking_id JOIN customers c ON c.id=b.customer_id JOIN users u ON u.id=c.user_id WHERE p.id=?");
$s->execute([$id]);
$p=$s->fetch();
if(!$p)exit('Payment not found.');
if($_SERVER['REQUEST_METHOD']==='POST')  {
    verify_csrf();
    $status=post('payment_status');
    $method=post('payment_method');
    if(!in_array($status,['Pending','Paid','Failed','Refunded'],true)||!in_array($method,['Cash','Bank Transfer','Card'],true))  {
        flash('error','Invalid payment details.');
    } else  {
        $s=$pdo->prepare('UPDATE payments SET payment_method=?,payment_status=?,payment_date=CASE WHEN ?="Paid" THEN NOW() ELSE payment_date END,updated_at=NOW() WHERE id=?');
        $s->execute([$method,$status,$status,$id]);
        log_activity('Payment update','Payment '.$p['payment_reference'].' changed to '.$status);
        flash('success','Payment updated.');
        redirect('admin/payments/index.php');
    }
}
$pageTitle='Payment';
require __DIR__.'/../../includes/header.php';
?><div class="app-shell"><?php
require __DIR__.'/../../includes/admin_sidebar.php';
?><main class="app-main"><div class="topbar"><h2><?=e($p['payment_reference'])?></h2></div><div class="panel"><form method="post"><input type="hidden" name="csrf" value="<?=csrf_token()?>"><div class="detail-grid mb-4"><div><span>Booking</span><b><?=e($p['booking_reference'])?></b></div><div><span>Customer</span><b><?=e($p['full_name'])?></b></div><div><span>Amount</span><b><?=money($p['amount'])?></b></div></div><div class="row g-3"><div class="col-md-6"><label>Method</label><select name="payment_method" class="form-select"><option <?=$p['payment_method']==='Cash'?'selected':''?>>Cash</option><option <?=$p['payment_method']==='Bank Transfer'?'selected':''?>>Bank Transfer</option><option <?=$p['payment_method']==='Card'?'selected':''?>>Card</option></select></div><div class="col-md-6"><label>Status</label><select name="payment_status" class="form-select"><option <?=$p['payment_status']==='Pending'?'selected':''?>>Pending</option><option <?=$p['payment_status']==='Paid'?'selected':''?>>Paid</option><option <?=$p['payment_status']==='Failed'?'selected':''?>>Failed</option><option <?=$p['payment_status']==='Refunded'?'selected':''?>>Refunded</option></select></div><div class="col-12"><button class="btn btn-teal">Update Payment</button></div></div></form></div></main></div><?php
require __DIR__.'/../../includes/footer.php';
?>