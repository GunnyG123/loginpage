<?php
include('database.php');
require 'vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
include('auth_helper.php');
redirectIfLoggedIn();

$secret_key = 'mystrongsecretkey';
$email = '';
$loginError = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $loginError = "Email and password are required!";
    } else {
        try {
            $stmt = $db->prepare("SELECT id, email, firstname, lastname, phone, password FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                
                if (password_verify($password, $user['password'])) {
                   
                    $issuedAt = time();
                    $expirationTime = $issuedAt + 3600; 
                    
                    $payload = [
                        'iat' => $issuedAt,
                        'exp' => $expirationTime,
                        'data' => [
                            'id' => $user['id'],
                            'email' => $user['email'],
                            'firstname' => $user['firstname']
                        ]
                    ];
                    
                    $jwt = JWT::encode($payload, $secret_key, 'HS256');
                    
                    setcookie('auth_token', $jwt, [
                        'expires' => $expirationTime,
                        'path' => '/',
                        'secure' => true,
                        'httponly' => true,
                        'samesite' => 'Strict'
                    ]);
                    
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $loginError = "Invalid email or password!";
                }
            } else {
                $loginError = "Invalid email or password!";
            }
        } catch (Exception $e) {
            $loginError = "System error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <style>.error-message {color:red; font-size:0.8em;}</style>
</head>
<body>
    <div class="container">
        <form method="post" class="form">
            <h2>Login</h2>
            <p>Don't have an account? <a href="index.php">Sign Up</a></p>
            <div class="lineshape"></div>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Email" value="<?= htmlspecialchars($email) ?>"><br>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Password"><br>

            <div class="error-message"><?= $loginError ?></div>

            <button type="submit" name="login" class="custombtn">Login</button>
            <div class="div"><a href="resetpass.php">Forgot your password?</a></div>
        </form>
    </div>
</body>
</html>