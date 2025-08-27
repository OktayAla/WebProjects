<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'customertracking');
define('DB_USER', 'root');
define('DB_PASS', '');

// Application configuration
define('APP_NAME', 'CustomerTrackingPanel');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set default timezone
date_default_timezone_set('Europe/Istanbul');

// Set default charset
ini_set('default_charset', 'UTF-8');
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
?>
