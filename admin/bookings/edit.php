<?php
require_once __DIR__.'/../../includes/auth.php';
require_login('admin');
$id=(int)($_GET['id']??0);
$s=$pdo->prepare('SELECT * FROM bookings WHERE id=?');
$s->execute([$id]);
$b=$s->fetch();
if(!$b)exit('Booking not found.');
if($_SERVER['REQUEST_METHOD']==='POST')  {
    verify_csrf();
    $date=post('service_date');
    $time=post('service_time');
    $addr=post('service_address');
    $notes=post('notes');
    if($date<date('Y-m-d'))flash('error','Date cannot be in the past.');
    else  {
        $s=$pdo->prepare('UPDATE bookings SET service_date=?,service_time=?,service_address=?,notes=?,updated_at=NOW() WHERE id=?');
        $s->execute([$date,$time,$addr,$notes,$id]);
        log_activity('Booking update','Updated booking '.$b['booking_reference']);
        redirect('admin/bookings/view.php?id='.$id);
    }
}
$pageTitle='Edit Booking';
require __DIR__.'/../../includes/header.php';
?><div class="app-shell"><?php
require __DIR__.'/../../includes/admin_sidebar.php';
?><main class="app-main"><div class="topbar"><h2>Edit booking</h2></div><div class="panel"><form method="post"><input type="hidden" name="csrf" value="<?=csrf_token()?>"><div class="row g-3"><div class="col-md-6"><label>Date</label><input required min="<?=date('Y-m-d')?>" type="date" name="service_date" class="form-control" value="<?=e($b['service_date'])?>"></div><div class="col-md-6"><label>Time</label><input required type="time" name="service_time" class="form-control" value="<?=e($b['service_time'])?>"></div><div class="col-12"><label>Address</label><textarea required name="service_address" class="form-control"><?=e($b['service_address'])?></textarea></div><div class="col-12"><label>Notes</label><textarea name="notes" class="form-control"><?=e($b['notes'])?></textarea></div><div class="col-12"><button class="btn btn-teal">Save</button></div></div></form></div></main></div><?php
require __DIR__.'/../../includes/footer.php';
?>