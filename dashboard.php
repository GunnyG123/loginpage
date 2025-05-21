<?php
require 'vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;


error_reporting(E_ALL);
ini_set('display_errors', 1);

$secret_key = 'mystrongsecretkey'; 


if (!isset($_COOKIE['auth_token'])) {
    header("Location: login.php");
    exit();
}

try {
    
    $decoded = JWT::decode($_COOKIE['auth_token'], new Key($secret_key, 'HS256'));
    
    
    if (time() > $decoded->exp) {
        throw new Exception("Token expired");
    }
    
    
    $userData = (array) $decoded->data;
    $firstname = htmlspecialchars($userData['firstname']);
    
} catch (Exception $e) {
    
    setcookie('auth_token', '', [
        'expires' => time() - 3600,
        'path' => '/',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <form action="logout.php" method="post" class="form">
            <h2>Hello, <?= $firstname ?></h2>
            <p>Welcome to the Dashboard</p>
            <button type="submit" name="logout" class="custombtn">Logout</button>
        </form>
    </div>
</body>
</html>