<?php
session_start();
include 'connect.php';

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    echo "You are already logged in. Please logout before attempting to log in again.";
    header("Location: index.php"); 
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Flavorful Shares</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <header>
        <h1>Login to Flavorful Shares</h1>
    </header>

    <main>
        <?php if (!isset($_SESSION['user_id'])): ?>
            <form action="authenticate.php" method="post">
                Username: <input type="text" name="user_name" required><br>
                Password: <input type="password" name="password" required><br>
                <input type="submit" value="Login">
            </form>
        <?php else: ?>
            <p>You are already logged in. Please logout before attempting to log in again.</p>
            <a href="index.php">Go to Home Page</a>
        <?php endif; ?>
        <p>New to this website Create Account Here:<a href="register.php">Register</a></p>
    </main>
    <footer>

    </footer>
</body>
</html>
