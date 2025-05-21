<?php
include('database.php');
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$feedback = '';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['send_otp'])) {
    $email = trim($_POST['email']);

    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $otp = rand(100000, 999999);
        $expires = time() + 300; 

        
        $stmt = $db->prepare("DELETE FROM pwdReset WHERE pwdResetEmail = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();

        
        $stmt = $db->prepare("INSERT INTO pwdReset (pwdResetEmail, pwdResetToken, pwdResetExpires) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $otp, $expires);
        $stmt->execute();

        
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'sandbox.smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Username = 'b16187b18dd724';
            $mail->Password = 'b5e43943db4a92';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 2525;

            $mail->setFrom('noreply@example.com', 'App');
            $mail->addAddress($email);
            $mail->Subject = 'Your OTP for Password Reset';
            $mail->Body = "Your OTP is: $otp (valid for 5 minutes)";

            $mail->send();
            $feedback = '<div class="sucessfull">OTP sent to your email.</div>';
        } catch (Exception $e) {
           $feedback = '<div class="sucessfull">Mailer Error: ' . $mail->ErrorInfo . '</div>';
        }
    } else {
        $feedback = '<div class="unsucessfull">Email not found!</div>';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Request OTP</title>
</head>
<body>
    <form method="POST" class="form">
        <h3>Reset Password - Request OTP</h3>
        <input type="email" name="email" placeholder="Enter your email" required>
        <button type="submit" name="send_otp" class ='custombtn'>Send OTP</button>
        <p><?= $feedback ?></p>
        <a href="resetpassword.php">Already have OTP?</a>
    </form>
    
</body>
</html>
