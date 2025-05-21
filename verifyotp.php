<?php
session_start();
include('database.php');

$feedback = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['verifyotp'])) {
    $email = $_SESSION['reset_email'] ?? '';
    $otp = trim($_POST['otp']);

    if (empty($email) || empty($otp)) {
        $feedback = "OTP is required.";
    } else {
        $stmt = $db->prepare("SELECT pwdResetToken, pwdResetExpires FROM pwdReset WHERE pwdResetEmail = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $storedOtp = $row['pwdResetToken'];
            $expires = $row['pwdResetExpires'];

            if ($storedOtp == $otp && time() <= $expires) {
                // OTP valid
                header("Location: create-new-password.php");
                exit();
            } else {
                $feedback = "<span style='color: red;'>Invalid or expired OTP.</span>";
            }
        } else {
            $feedback = "<span style='color: red;'>OTP not found for this session.</span>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Verify OTP</title>
</head>
<body>
    <form method="POST" class="form">
        <h2>Verify OTP</h2>
        <input type="text" name="otp" placeholder="Enter OTP" required>
        <button type="submit" name="verifyotp" class='custombtn'>Verify OTP</button>
        <p><?= $feedback ?></p>
        <a href="resetpass.php">Forgot your password?</a>
    </form>
</body>
</html>
