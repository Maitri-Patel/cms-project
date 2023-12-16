<?php
session_start();
$_SESSION = array();
session_destroy();
setcookie('user_logged_in', false, time() - 3600, '/');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>LogOut - Flavorful Shares</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <p>You have succesfully logged out</><br>
    <a href="index.php" >Go to Home Page </a><br>
    <a href="login.php"> To login again </a>
</body>