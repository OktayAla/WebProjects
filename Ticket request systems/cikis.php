<?php
session_start();
// Tüm oturum değişkenlerini temizle
$_SESSION = array();
session_destroy();
// Çıkış sonrası yönlendirme
header("Location: index.php");
exit;
