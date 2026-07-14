<?php
session_start();
require_once("../includes/db.php");

$message="";

if($_SERVER["REQUEST_METHOD"]=="POST"){

$email=$_POST['email'];
$password=$_POST['password'];

$stmt=$pdo->prepare("SELECT * FROM users WHERE email=?");
$stmt->execute([$email]);

$user=$stmt->fetch();

if($user){

if(password_verify($password,$user['password'])){

$_SESSION['user_id']=$user['id'];
$_SESSION['fullname']=$user['full_name'];

header("Location: dashboard.php");
exit();

}else{

$message="Incorrect password.";

}

}else{

$message="User not found.";

}

}
?>

<!DOCTYPE html>

<html>

<head>

<title>Login | MixMaster AI</title>

<link rel="stylesheet" href="../assets/styles.css">

</head>

<body>

<div class="login-box">

<h2>Login</h2>

<?php

if(isset($_GET['success'])){

echo "<p style='color:green;'>Registration successful.</p>";

}

if($message!=""){

echo "<p style='color:red;'>$message</p>";

}

?>

<form method="POST">

<input type="email" name="email" placeholder="Email Address" required>

<input type="password" name="password" placeholder="Password" required>

<button type="submit">Login</button>

</form>

<p>

<a href="register.php">

Create an account

</a>

</p>

</div>

</body>

</html>