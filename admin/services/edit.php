<?php
require_once __DIR__.'/../../includes/auth.php';
require_login('admin');
$cats=$pdo->query("SELECT * FROM service_categories WHERE status='Active' ORDER BY category_name")->fetchAll();
$id=(int)($_GET['id']??$_POST['id']??0);
$editing=$id>0;
$r=null;
if($editing)  {
    $s=$pdo->prepare('SELECT * FROM services WHERE id=?');
    $s->execute([$id]);
    $r=$s->fetch();
    if(!$r)exit('Service not found.');
}
if($_SERVER['REQUEST_METHOD']==='POST')  {
    verify_csrf();
    $name=post('service_name');
    $cat=(int)post('category_id');
    $desc=post('description');
    $price=(float)post('price');
    $duration=post('duration');
    $status=post('status','Active');
    if(!$name||$cat<1||$price<0||!$duration)  {
        flash('error','Complete the required fields.');
    } else  {
        if($editing)  {
            $s=$pdo->prepare('UPDATE services SET category_id=?,service_name=?,description=?,price=?,duration=?,status=?,updated_at=NOW() WHERE id=?');
            $s->execute([$cat,$name,$desc,$price,$duration,$status,$id]);
            log_activity('Service update','Updated service '.$id);
        } else  {
            $s=$pdo->prepare('INSERT INTO services(category_id,service_name,description,price,duration,status) VALUES(?,?,?,?,?,?)');
            $s->execute([$cat,$name,$desc,$price,$duration,$status]);
            log_activity('Service creation','Created service '.$name);
        }
        redirect('admin/services/index.php');
    }
}
$pageTitle=$editing?'Edit Service':'Add Service';
require __DIR__.'/../../includes/header.php';
?><div class="app-shell"><?php
require __DIR__.'/../../includes/admin_sidebar.php';
?><main class="app-main"><div class="topbar"><h2><?=$editing?'Edit':'Add'?> service</h2></div><div class="panel"><?php
if($e=flash('error')):
?><div class="alert alert-danger"><?=e($e)?></div><?php
endif;
?><form method="post"><input type="hidden" name="csrf" value="<?=csrf_token()?>"><input type="hidden" name="id" value="<?=$id?>"><div class="row g-3"><div class="col-md-6"><label>Service name</label><input required name="service_name" class="form-control" value="<?=e($r['service_name']??'')?>"></div><div class="col-md-6"><label>Category</label><select required name="category_id" class="form-select"><option value="">Choose</option><?php
foreach($cats as $c):
?><option value="<?=$c['id']?>" <?=((int)($r['category_id']??0)===$c['id'])?'selected':''?>><?=e($c['category_name'])?></option><?php
endforeach;
?></select></div><div class="col-md-6"><label>Price</label><input required min="0" step="0.01" type="number" name="price" class="form-control" value="<?=e($r['price']??'')?>"></div><div class="col-md-6"><label>Duration</label><input required name="duration" class="form-control" placeholder="2 hours" value="<?=e($r['duration']??'')?>"></div><div class="col-12"><label>Description</label><textarea name="description" class="form-control"><?=e($r['description']??'')?></textarea></div><div class="col-md-4"><label>Status</label><select name="status" class="form-select"><option <?=$r['status']??'Active'==='Active'?'selected':''?>>Active</option><option <?=$r['status']??''==='Inactive'?'selected':''?>>Inactive</option></select></div><div class="col-12"><button class="btn btn-teal">Save Service</button></div></div></form></div></main></div><?php
require __DIR__.'/../../includes/footer.php';
?>