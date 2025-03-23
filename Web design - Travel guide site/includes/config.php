<?php
// Removed session_start() to prevent duplicate session initialization

define('SITE_TITLE', 'Türkiye Gezi Rehberi');

function count_content_items($contentType) {
    $path = DATA_PATH . "pages/$contentType/*.php";
    $files = glob($path);
    return $files ? count($files) : 0;
}
define('SITE_URL', 'http://localhost/turkiyegezirehberi');
define('DATA_PATH', __DIR__ . '/../data/');
?>
