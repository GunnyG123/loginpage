<?php
session_start();
include('database.php');
$feedback = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['reset_password'])) {
    $email = $_SESSION['reset_email'] ?? '';
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (!$email) {
        $feedback = "<div style='color: red; font-size: 12px;'>Session expired. Please restart the reset process.</div>";
    } elseif (empty($new_password) || empty($confirm_password)) {
        $feedback = "<div style='color: red;font-size: 12px;'>Please fill out both password fields.</div>";
    } elseif (strlen($new_password) < 8) {
        $feedback = "<div style='color: red; font-size: 12px;'>Password must be at least 8 characters long.</div>";
    } elseif ($new_password !== $confirm_password) {
        $feedback = "<div style='color: red;font-size: 12px;'>Passwords do not match.</div>";
    } else {
        $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $hashedPassword, $email);

        if ($stmt->execute()) {
            // Optional: delete token
            $stmt = $db->prepare("DELETE FROM pwdReset WHERE pwdResetEmail = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();

            $feedback = "<div style='color: green; font-size: 12px;'>Password successfully updated! You can now <a href='login.php'>login</a>.</div>";
            session_destroy(); // clear session
        } else {
            $feedback = "<div style='color: red; font-size: 12px;'>Failed to reset password. Try again later.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create New Password</title>
    <link rel="stylesheet" href="style.css">
</head>
<style>
    .div5{
        text-align: center;
    }
</style>
<body>
    <form method="POST" class="form">
        <h2>Create New Password</h2>
        <div class="change">
        <input type="password" name="new_password" placeholder="New Password (min 8 characters)" required>
        <input type="password" name="confirm_password" placeholder="Rewrite New Password" required>
        <span><a href="resetpass.php"><?= $feedback ?></a></span>
        <button type="submit" name="reset_password" class='custombtn'>Change Password</button>
        </div>
    </form>
</body>
</html>
