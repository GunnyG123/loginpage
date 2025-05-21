<?php
// logout.php
setcookie('auth_token', '', [
    'expires' => time() - 3600,
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
]);
header("Location: login.php");
exit();
?>