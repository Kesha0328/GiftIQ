<?php
session_start();
include __DIR__ . '/../config.php';
echo 'session id: '.session_id()."<br>";
echo 'session user_id: '.($_SESSION['user_id'] ?? 'NULL')."<br>";
echo 'session fullname: '.($_SESSION['fullname'] ?? 'NULL')."<br>";
echo 'php_self: '.$_SERVER['PHP_SELF']."<br>";
echo '<pre>'; print_r($_SESSION); echo '</pre>';
