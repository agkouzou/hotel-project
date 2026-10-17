<?php
// v3.1: search and results live on index.php. Old links are forwarded with their query string.
$qs = $_SERVER['QUERY_STRING'] ?? '';
header('Location: /index.php' . ($qs !== '' ? '?' . $qs : ''), true, 301);
exit;
