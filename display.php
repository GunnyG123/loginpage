
<?php
 include('auth_helper.php');
 redirectIfLoggedIn();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="style.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="form">
    <p style="color:red">login sucessfull please: <a href="login.php">Login</a></p>
    </div>
</body>
</html>