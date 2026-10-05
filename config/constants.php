<?php
if (session_status() === PHP_SESSION_NONE) session_start();
function base_url(string $path=''): string { return '/sifemklen/' . ltrim($path,'/'); }
function redirect(string $path): never { header('Location: '.base_url($path)); exit; }
function e($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function flash(string $key, ?string $message=null){ if ($message!==null) { $_SESSION['_flash'][$key]=$message; return null; } $m=$_SESSION['_flash'][$key]??null; unset($_SESSION['_flash'][$key]); return $m; }
