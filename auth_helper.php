<?php
require 'vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$secret_key = 'mystrongsecretkey'; 

function isLoggedIn() {
    global $secret_key;
    if (!isset($_COOKIE['auth_token'])) return false;
    try {
        $decoded = JWT::decode($_COOKIE['auth_token'], new Key($secret_key, 'HS256'));
        return time() <= $decoded->exp;
    } catch (Exception $e) {
        return false;
    }
}

function preventCaching() {
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
}

function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        header("Location: dashboard.php");
        exit();
    }
    preventCaching(); // Add this line
}

function getCurrentUser() {
    global $secret_key;
    if (!isLoggedIn()) return null;
    try {
        $decoded = JWT::decode($_COOKIE['auth_token'], new Key($secret_key, 'HS256'));
        return (array) $decoded->data;
    } catch (Exception $e) {
        return null;
    }
}
?>