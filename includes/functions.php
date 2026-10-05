<?php
require_once __DIR__.'/../config/constants.php';
function csrf_token(){ if(empty($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(32)); return $_SESSION['csrf']; }
function verify_csrf(){ if(!hash_equals($_SESSION['csrf']??'', $_POST['csrf']??'')){ http_response_code(419); exit('Invalid security token. Please go back and try again.'); } }
function current_user(){ global $pdo; static $u=null; if($u!==null) return $u; if(empty($_SESSION['user_id'])) return null; $s=$pdo->prepare('SELECT * FROM users WHERE id=?'); $s->execute([$_SESSION['user_id']]); return $u=$s->fetch()?:null; }
function require_login($role=null){ $u=current_user(); if(!$u) redirect('login.php'); if($u['status']!=='Active') { session_destroy(); redirect('login.php'); } if($role && $u['role']!==$role) { http_response_code(403); exit('Unauthorized access.'); } return $u; }
function log_activity($activity,$description){ global $pdo; $uid=$_SESSION['user_id']??null; $ip=$_SERVER['REMOTE_ADDR']??'unknown'; $s=$pdo->prepare('INSERT INTO activity_logs(user_id,activity,description,ip_address) VALUES(?,?,?,?)'); $s->execute([$uid,$activity,$description,$ip]); }
function setting($key,$default=''){ global $pdo; static $cache=[]; if(array_key_exists($key,$cache)) return $cache[$key]; $s=$pdo->prepare('SELECT setting_value FROM settings WHERE setting_key=?'); $s->execute([$key]); return $cache[$key]=$s->fetchColumn()?:$default; }
function money($amount){ return e(setting('currency','$')).number_format((float)$amount,2); }
function booking_ref(){ do {$ref='SFK-'.date('ymd').'-'.strtoupper(bin2hex(random_bytes(3))); global $pdo; $s=$pdo->prepare('SELECT id FROM bookings WHERE booking_reference=?'); $s->execute([$ref]); } while($s->fetch()); return $ref; }
function status_badge($status){ $map=['Pending'=>'warning','Confirmed'=>'info','Assigned'=>'primary','In Progress'=>'primary','Completed'=>'success','Cancelled'=>'danger','Paid'=>'success','Failed'=>'danger','Refunded'=>'secondary','Active'=>'success','Inactive'=>'secondary']; $c=$map[$status]??'secondary'; return '<span class="badge text-bg-'.$c.'">'.e($status).'</span>'; }
function get_customer_id_for_user($userId){ global $pdo; $s=$pdo->prepare('SELECT id FROM customers WHERE user_id=?'); $s->execute([$userId]); return (int)$s->fetchColumn(); }
function post($key,$default=''){ return trim($_POST[$key]??$default); }
