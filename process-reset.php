<?php
include('database.php');

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['verify_otp'])) {
    $email = trim($_POST['email']);
    $otp = trim($_POST['otp']);
    $new_password = trim($_POST['new_password']);

    if (empty($email) || empty($otp) || empty($new_password)) {
        echo "All fields are required.";
        exit();
    }

    $stmt = $db->prepare("SELECT pwdResetToken, pwdResetExpires FROM pwdReset WHERE pwdResetEmail = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($storedOtp, $expires);
    $stmt->fetch();
    $stmt->close();

    if ($storedOtp === $otp && time() <= $expires) {
        $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);

        $stmt = $db->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $hashedPassword, $email);
        $stmt->execute(); 
        header("Location: sucessfull.php");
    } else {
        echo "Invalid or expired OTP.";
    }
}
?>
