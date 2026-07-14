<?php
session_start();
require_once("../includes/db.php");

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if (empty($fullname) || empty($email) || empty($password) || empty($confirm)) {
        $message = "Please fill in all fields.";
    } elseif ($password != $confirm) {
        $message = "Passwords do not match.";
    } else {

        $check = $pdo->prepare("SELECT id FROM users WHERE email=?");
        $check->execute([$email]);

        if ($check->rowCount() > 0) {
            $message = "Email already exists.";
        } else {

            $hashed = password_hash($password, PASSWORD_DEFAULT);

            $insert = $pdo->prepare("INSERT INTO users(full_name,email,password) VALUES(?,?,?)");
            $insert->execute([$fullname,$email,$hashed]);

            header("Location: login.php?success=1");
            exit();

        }

    }

}
?>

<!DOCTYPE html>
<html>

<head>

<title>Register | MixMaster AI</title>
<link rel="stylesheet" href="../assets/styles.css">

</head>

<body>

<div class="login-box">

<h2>Create Account</h2>

<?php
if($message!=""){
    echo "<p style='color:red;'>$message</p>";
}
?>

<form method="POST">

<input type="text" name="fullname" placeholder="Full Name" required>

<input type="email" name="email" placeholder="Email Address" required>

<input type="password" name="password" placeholder="Password" required>

<input type="password" name="confirm_password" placeholder="Confirm Password" required>

<button type="submit">Register</button>

</form>

<p>

Already have an account?

<a href="login.php">Login</a>

</p>

</div>

</body>

</html>