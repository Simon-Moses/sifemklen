<?php
require_once __DIR__.'/../includes/auth.php';
require_login('admin');
$keys=['company_name','tagline','phone','email','address','currency','business_hours'];
if($_SERVER['REQUEST_METHOD']==='POST')  {
    verify_csrf();
    foreach($keys as $k)  {
        $s=$pdo->prepare('INSERT INTO settings(setting_key,setting_value) VALUES(?,?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value),updated_at=NOW()');
        $s->execute([$k,post($k)]);
    }
    log_activity('Settings update','Updated business settings.');
    flash('success','Settings saved.');
    redirect('admin/settings.php');
}
$vals=[];
foreach($keys as $k)$vals[$k]=setting($k);
$pageTitle='Settings';
require __DIR__.'/../includes/header.php';
?><div class="app-shell"><?php
require __DIR__.'/../includes/admin_sidebar.php';
?><main class="app-main"><div class="topbar"><h2>Settings</h2></div><?php
if($ok=flash('success')):
?><div class="alert alert-success"><?=e($ok)?></div><?php
endif;
?><div class="panel"><form method="post"><input type="hidden" name="csrf" value="<?=csrf_token()?>"><div class="row g-3"><?php
foreach($keys as $k):
?><div class="col-md-6"><label><?=e(ucwords(str_replace('_',' ',$k)))?></label><?php
if($k==='address'||$k==='business_hours'):
?><textarea name="<?=$k?>" class="form-control"><?=e($vals[$k])?></textarea><?php
else:
?><input name="<?=$k?>" class="form-control" value="<?=e($vals[$k])?>"><?php
endif;
?></div><?php
endforeach;
?><div class="col-12"><button class="btn btn-teal">Save Settings</button></div></div></form></div></main></div><?php
require __DIR__.'/../includes/footer.php';
?>