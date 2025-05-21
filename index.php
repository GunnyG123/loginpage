<?php 
include('database.php');
require 'vendor/autoload.php';
include('auth_helper.php');

redirectIfLoggedIn();

$errorMessages = [
    'email' => '',
    'firstname' => '',
    'lastname' => '',
    'phone' => '',
    'password' => '',
    'confirmpassword' => '',
    'general' => ''
];

$formData = [
    'firstname' => '',
    'lastname' => '',
    'email' => '',
    'phone' => ''
];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $formData = array_map('trim', $_POST);
    $password = $_POST['password'];
    $confirmpassword = $_POST['confirmpassword'];

    if (empty($formData['email'])) $errorMessages['email'] = "Email required!";
    if (empty($formData['firstname'])) $errorMessages['firstname'] = "First name required!";
    if (empty($formData['lastname'])) $errorMessages['lastname'] = "Last name required!";
    if (empty($formData['phone'])) $errorMessages['phone'] = "Phone required!";
    if (empty($password)) $errorMessages['password'] = "Password required!";
    if (empty($confirmpassword)) $errorMessages['confirmpassword'] = "Please confirm your password!";

    if (empty(array_filter($errorMessages))) {
        if ($password !== $confirmpassword) {
            $errorMessages['general'] = "Passwords do not match!";
        } elseif (strlen($password) < 8) {
            $errorMessages['general'] = 'Password must be at least 8 characters';
        } else {
            try {
                $checkEmail = $db->prepare("SELECT id FROM users WHERE email = ?");
                $checkEmail->bind_param("s", $formData['email']);
                $checkEmail->execute();
                if ($checkEmail->get_result()->num_rows > 0) {
                    $errorMessages['email'] = "Email already registered!";
                } else {
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $db->prepare("INSERT INTO users (email, firstname, lastname, phone, password) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("sssss", $formData['email'], $formData['firstname'], $formData['lastname'], $formData['phone'], $hashedPassword);
                    
                    if ($stmt->execute()) {
                        header("Location: display.php");
                        exit();
                    } else {
                        $errorMessages['general'] = "Database error: " . $stmt->error;
                    }
                }
            } catch (Exception $e) {
                $errorMessages['general'] = "System error: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <form method="post" class="form">
            <h2>Sign Up</h2>
            <p>Already have an account? <a href="login.php">Login</a></p>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Email" value="<?= htmlspecialchars($formData['email']) ?>"><br>
            <div class="error-message"><?= $errorMessages['email'] ?></div>

            <label for="firstname">First Name</label>
            <input type="text" id="firstname" name="firstname" placeholder="First Name" value="<?= htmlspecialchars($formData['firstname']) ?>"><br>
            <div class="error-message"><?= $errorMessages['firstname'] ?></div>

            <label for="lastname">Last Name</label>
            <input type="text" id="lastname" name="lastname" placeholder="Last Name" value="<?= htmlspecialchars($formData['lastname']) ?>"><br>
            <div class="error-message"><?= $errorMessages['lastname'] ?></div>

            <label for="phone">Phone</label>
            <input type="text" id="phone" name="phone" placeholder="Phone" value="<?= htmlspecialchars($formData['phone']) ?>"><br>
            <div class="error-message"><?= $errorMessages['phone'] ?></div>

            <label for="password">Password</label>
            <div class="div1">
                <input type="password" id="password" name="password" placeholder="Password">
                <span class="toggle-password">
                    <img src="images/eyeclose.svg" class="eye" alt="Show Password">
                </span>
            </div>
            <div class="error-message"><?= $errorMessages['password'] ?></div>

            <label for="confirmpassword">Confirm Password</label>
            <div class="div1">
                <input type="password" id="confirmpassword" name="confirmpassword" placeholder="Confirm Password">
                <span class="toggle-password">
                    <img src="images/eyeclose.svg" class="eye" alt="Show Password">
                </span>
            </div>
            <div class="error-message"><?= $errorMessages['confirmpassword'] ?></div>

            <div class="error-message"><?= $errorMessages['general'] ?></div>
            <button type="submit" name="submit" class="custombtn">Register</button>
            <a href="resetpass.php">Forgot your password?</a>
        </form>
    </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleButtons = document.querySelectorAll('.toggle-password');
        
        toggleButtons.forEach(button => {
            button.addEventListener('click', function() {
                const inputField = this.closest('.div1').querySelector('input');
                const eyeImage = this.querySelector('.eye');
                
                if (inputField.type === 'password') {
                    inputField.type = 'text';
                    eyeImage.src = 'images/eyeopen.svg';
                    eyeImage.alt = 'Hide Password';
                } else {
                    inputField.type = 'password';
                    eyeImage.src = 'images/eyeclose.svg';
                    eyeImage.alt = 'Show Password';
                }
            });
        });
    });
    </script>
</body>
</html>