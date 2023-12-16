<?php
session_start();
include 'connect.php';

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    echo "You are already logged in. Please log out before attempting to log in again.";
    header("Location: index.php"); // Redirect to the home page or dashboard
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = filter_input(INPUT_POST, 'user_name', FILTER_SANITIZE_STRING);
    $password = $_POST['password'];

    // Check for empty fields
    if (empty($username) || empty($password)) {
        echo "Username and password are required.";
        exit;
    }

    $stmt = $db->prepare("SELECT * FROM users WHERE user_name = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verify the password
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_name'] = $user['user_name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['success_message'] = "Login successful! Welcome back, " . htmlspecialchars($user['user_name']) . ".";

        setcookie('user_logged_in', true, time() + 3600, '/');

        header("Location: index.php");
        exit;
    } else {
        // Incorrect login
        echo "Invalid username or password. Try again.";
    }
} else {
    header("Location: login.php");
    exit;
}
?>
