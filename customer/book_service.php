<?php
require_once __DIR__.'/../includes/auth.php';
$u=require_login('customer');
$cid=get_customer_id_for_user($u['id']);
if($_SERVER['REQUEST_METHOD']==='POST')  {
    verify_csrf();
    $sid=(int)($_POST['service_id']??0);
    $qty=(int)($_POST['quantity']??0);
    $date=post('service_date');
    $time=post('service_time');
    $addr=post('service_address');
    $notes=post('notes');
    $s=$pdo->prepare("SELECT * FROM services WHERE id=? AND status='Active'");
    $s->execute([$sid]);
    $svc=$s->fetch();
    if(!$svc||$qty<1||$qty>100||!preg_match('/^\d{4}-\d{2}-\d{2}$/',$date)||$date<date('Y-m-d')||!preg_match('/^\d{2}:\d{2}$/',$time)||!$addr)  {
        flash('error','Please provide valid booking details.');
    } else  {
        $amount=$svc['price']*$qty;
        try  {
            $pdo->beginTransaction();
            $ref=booking_ref();
            $s=$pdo->prepare("INSERT INTO bookings(booking_reference,customer_id,service_id,quantity,service_date,service_time,service_address,notes,estimated_amount,status) VALUES(?,?,?,?,?,?,?,?,?,'Pending')");
            $s->execute([$ref,$cid,$sid,$qty,$date,$time,$addr,$notes,$amount]);
            $bid=$pdo->lastInsertId();
            $s=$pdo->prepare("INSERT INTO payments(payment_reference,booking_id,amount,payment_method,payment_status) VALUES(?,? ,?,'Cash','Pending')");
            $s->execute(['PAY-'.strtoupper(bin2hex(random_bytes(4))),$bid,$amount]);
            $pdo->commit();
            log_activity('Booking creation','Created booking '.$ref);
            flash('success','Booking created successfully.');
            redirect('customer/booking_details.php?id='.$bid);
        } catch(Throwable $e)  {
            if($pdo->inTransaction())$pdo->rollBack();
            flash('error','Unable to create booking.');
        }
    }
}
$services=$pdo->query("SELECT s.*,c.category_name FROM services s JOIN service_categories c ON c.id=s.category_id WHERE s.status='Active' ORDER BY s.service_name")->fetchAll();
$selected=(int)($_GET['service_id']??$_POST['service_id']??0);
$err=flash('error');
$pageTitle='Book Service';
require __DIR__.'/../includes/header.php';
?><div class="app-shell"><?php
require __DIR__.'/../includes/customer_sidebar.php';
?><main class="app-main"><div class="topbar"><div><span class="eyebrow">NEW BOOKING</span><h2>Book a service</h2></div></div><?php
if($err):
?><div class="alert alert-danger"><?=e($err)?></div><?php
endif;
?><div class="panel"><form method="post" id="bookingForm"><input type="hidden" name="csrf" value="<?=csrf_token()?>"><div class="row g-3"><div class="col-md-8"><label>Service</label><select required name="service_id" id="service_id" class="form-select"><option value="">Choose service</option><?php
foreach($services as $s):
?><option data-price="<?=$s['price']?>" value="<?=$s['id']?>" <?=$selected===$s['id']?'selected':''?>><?=e($s['service_name'])?> — <?=money($s['price'])?></option><?php
endforeach;
?></select></div><div class="col-md-4"><label>Quantity</label><input id="quantity" required min="1" max="100" type="number" name="quantity" value="1" class="form-control"></div><div class="col-md-6"><label>Service date</label><input required min="<?=date('Y-m-d')?>" type="date" name="service_date" class="form-control"></div><div class="col-md-6"><label>Service time</label><input required type="time" name="service_time" class="form-control"></div><div class="col-12"><label>Service address</label><textarea required name="service_address" class="form-control"><?=e($pdo->query('SELECT address FROM customers WHERE id='.$cid)->fetchColumn())?></textarea></div><div class="col-12"><label>Notes</label><textarea name="notes" class="form-control" placeholder="Access instructions or special requests"></textarea></div><div class="col-12"><div class="amount-box">Estimated amount <strong id="estimatedAmount">—</strong></div></div><div class="col-12"><button class="btn btn-teal">Create Booking</button></div></div></form></div></main></div><script src="<?=base_url('assets/js/booking.js')?>"></script><?php
require __DIR__.'/../includes/footer.php';
?>