<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>

    <header>

    </header>

    <main>
        <h1>Admin Dashboard</h1>


        <ul>
            <li><a href="user_management.php">User Management</a></li>
            <li><a href="category_management.php">Category Management</a></li>
            <li><a href="comment_management.php">Comment Management</a></li>
        </ul>

    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Flavorful Shares By Maitri Patel</p>
    </footer>

</body>

</html>