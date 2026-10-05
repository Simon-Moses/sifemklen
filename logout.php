<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
if(current_user()) log_activity(current_user()['role']==='admin'?'Admin logout':'Customer logout','User logged out.');
$_SESSION=[];
session_destroy();
header('Location: '.base_url('login.php'));
exit;
?>