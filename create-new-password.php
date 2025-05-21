<?php
session_start();
include('database.php');

$selector = $_GET['selector'] ?? '';
$token = $_GET['token'] ?? '';
$showForm = true;
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resetpassword'])) {
    $selector = $_POST['selector'];
    $token = $_POST['token'];
    $password = $_POST['password'];
    $confirm = $_POST['confirmpassword'];

    if (empty($password) || strlen($password) < 8 || $password !== $confirm) {
        $error = "Password must be 8+ characters and match confirmation";
    } else {
        $currentDate = date("U");
        $stmt = $db->prepare("SELECT * FROM pwdReset WHERE pwdResetSelector = ? AND pwdResetExpires >= ?");
        $stmt->bind_param("ss", $selector, $currentDate);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $tokenBin = hex2bin($token);
            if (password_verify($tokenBin, $row["pwdResetToken"])) {
                $email = $row["pwdResetEmail"];
                $hashedPwd = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("UPDATE users SET password=? WHERE email=?");
                $stmt->bind_param("ss", $hashedPwd, $email);
                $stmt->execute();

                $stmt = $db->prepare("DELETE FROM pwdReset WHERE pwdResetEmail=?");
                $stmt->bind_param("s", $email);
                $stmt->execute();

                $success = "Password has been reset!";
                $showForm = false;
                if($success) {
                    header("Location: login.php?reset=success");
                    exit();
                }
            } else {
                $error = "Invalid token";
            }
        } else {
            $error = "Reset link expired or invalid";
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
<body>
    

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if ($showForm): ?>
        <form method="post" class='form'>
        <h2>Reset Your Password</h2>
            <input type="hidden" name="selector" value="<?= htmlspecialchars($selector) ?>">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            <label>New Password</label>
            <input type="password" name="password" required minlength="8">
            <label>Confirm Password</label>
            <input type="password" name="confirmpassword" required minlength="8">
            <button type="submit" name="resetpassword" class='custombtn'>Reset Password</button>
            <div class="reset-link">
                <a href="login.php">Back to Login</a>
        </form>
    <?php endif; ?>
</body>
</html>
