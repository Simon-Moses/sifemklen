<?php
require_once __DIR__.'/../../includes/auth.php';
require_login('admin');
$id=(int)($_GET['id']??$_POST['id']??0);
$editing=$id>0;
$r=null;
if($editing)  {
    $s=$pdo->prepare('SELECT * FROM staff WHERE id=?');
    $s->execute([$id]);
    $r=$s->fetch();
    if(!$r)exit('Staff not found.');
}
if($_SERVER['REQUEST_METHOD']==='POST')  {
    verify_csrf();
    $vals=[post('full_name'),post('phone'),post('email'),post('position'),post('address'),post('status','Active')];
    if(!$vals[0]||!$vals[1]||!filter_var($vals[2],FILTER_VALIDATE_EMAIL))  {
        flash('error','Enter valid staff details.');
    } else  {
        if($editing)  {
            $s=$pdo->prepare('UPDATE staff SET full_name=?,phone=?,email=?,position=?,address=?,status=?,updated_at=NOW() WHERE id=?');
            $s->execute([...$vals,$id]);
            log_activity('Staff update','Updated staff '.$id);
        } else  {
            $s=$pdo->prepare('INSERT INTO staff(full_name,phone,email,position,address,status) VALUES(?,?,?,?,?,?)');
            $s->execute($vals);
            log_activity('Staff creation','Created staff '.$vals[0]);
        }
        redirect('admin/staff/index.php');
    }
}
$pageTitle=$editing?'Edit Staff':'Add Staff';
require __DIR__.'/../../includes/header.php';
?><div class="app-shell"><?php
require __DIR__.'/../../includes/admin_sidebar.php';
?><main class="app-main"><div class="topbar"><h2><?=$editing?'Edit':'Add'?> staff</h2></div><div class="panel"><?php
if($e=flash('error')):
?><div class="alert alert-danger"><?=e($e)?></div><?php
endif;
?><form method="post"><input type="hidden" name="csrf" value="<?=csrf_token()?>"><input type="hidden" name="id" value="<?=$id?>"><div class="row g-3"><div class="col-md-6"><label>Full name</label><input required name="full_name" class="form-control" value="<?=e($r['full_name']??'')?>"></div><div class="col-md-6"><label>Phone</label><input required name="phone" class="form-control" value="<?=e($r['phone']??'')?>"></div><div class="col-md-6"><label>Email</label><input required type="email" name="email" class="form-control" value="<?=e($r['email']??'')?>"></div><div class="col-md-6"><label>Position</label><select name="position" class="form-select"><?php
foreach(['Manager','Cleaner','Laundry Specialist','Supervisor','Driver','Customer Support'] as $p):
?><option <?=($r['position']??'Cleaner')===$p?'selected':''?>><?=e($p)?></option><?php
endforeach;
?></select></div><div class="col-12"><label>Address</label><textarea name="address" class="form-control"><?=e($r['address']??'')?></textarea></div><div class="col-md-4"><label>Status</label><select name="status" class="form-select"><option <?=($r['status']??'Active')==='Active'?'selected':''?>>Active</option><option <?=($r['status']??'')==='Inactive'?'selected':''?>>Inactive</option></select></div><div class="col-12"><button class="btn btn-teal">Save Staff</button></div></div></form></div></main></div><?php
require __DIR__.'/../../includes/footer.php';
?>