<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Enter OTP</title>
</head>
<body>
    <form method="POST" action="process-reset.php" class="form">
        <h3>Enter OTP and New Password</h3>
        <input type="email" name="email" placeholder="Enter your email" required>
        <input type="text" name="otp" placeholder="Enter OTP" required>
        <input type="password" name="new_password" placeholder="New Password" required>
        <button type="submit" name="verify_otp" class='custombtn'>Reset Password</button>
    </form>
</body>
</html>
