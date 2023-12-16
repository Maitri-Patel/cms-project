<?php
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['password'] === $_POST['confirm_password']) {
    // Validate and sanitize inputs
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING);
    $password = $_POST['password']; 
    $confirmPassword = $_POST['confirm_password'];

    if (empty($username) || empty($email) || empty($role) || empty($password) || empty($confirmPassword)) {
        echo "All fields are required.";
        exit;
    }

    if ($email === false) {
        echo "Invalid email address.";
        exit;
    }

    if (strlen($password) < 8) {
        echo "Password must be at least 8 characters long.";
        exit;
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Check if the username already exists
        $stmt = $db->prepare("SELECT * FROM users WHERE user_name = ?");
        $stmt->execute([$username]);
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingUser) {
            echo "Username already exists. Please choose a different username.";
        } else {
            // Insert the new user into the database
            $stmt = $db->prepare("INSERT INTO users (user_name, email, password_hash, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$username, $email, $passwordHash, $role]);
            echo "User registered successfully!";
            header("Location: login.php");
        }
    } catch (PDOException $e) {
        echo "Registration error: " . $e->getMessage();
    }
} else {
    echo "Passwords do not match.";
}
?>
