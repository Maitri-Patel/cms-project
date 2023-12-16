<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_user'])) {
    $newUsername = $_POST['new_username'];
    $newEmail = $_POST['new_email'];
    $newPassword = $_POST['new_password'];
    $newRole = $_POST['new_role'];

    if (empty($newUsername) || empty($newEmail) || empty($newPassword)) {
        echo "All fields are required.";
        exit;
    }

    if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format.";
        exit;
    }

    $stmt = $db->prepare("SELECT * FROM users WHERE user_name = ?");
    $stmt->execute([$newUsername]);
    $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingUser) {
        $_SESSION['error_message'] = "Username already exists. Please choose a different username.";
    } else {
        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

        $stmt = $db->prepare("INSERT INTO users (user_name, email, password_hash, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$newUsername, $newEmail, $newPasswordHash, $newRole]);

        $_SESSION['success_message'] = "User added successfully!";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_user'])) {
    $userId = $_POST['user_id'];
    $updatedUsername = $_POST['updated_username'];
    $updatedEmail = $_POST['updated_email'];

    $stmt = $db->prepare("UPDATE users SET user_name = ?, email = ? WHERE user_id = ?");
    $stmt->execute([$updatedUsername, $updatedEmail, $userId]);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_user'])) {
    $userId = $_POST['user_id'];

    $stmt = $db->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);
}

$stmt = $db->query("SELECT * FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User Management</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <h2>User Management</h2>

    <h3> Add User Form </h3>
    <form method="post" action="user_management.php">
        <label for="new_username">Username:</label>
        <input type="text" name="new_username" required>

        <label for="new_email">Email:</label>
        <input type="email" name="new_email" required>

        <label for="new_password">Password:</label>
        <input type="password" name="new_password" required>

        <label for="new_role">Role:</label>
        <input type="text" name="new_role" required>

        <button type="submit" name="add_user">Add User</button>
    </form>

    <?php if ($users) : ?>
        <h3>Users List</h3>
        <table>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user) : ?>
                    <tr>
                        <td><?= $user['user_id'] ?></td>
                        <td><?= $user['user_name'] ?></td>
                        <td><?= $user['email'] ?></td>
                        <td>
                            <!-- Update Form -->
                            <form method="post" action="user_management.php">
                                <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                                <input type="text" name="updated_username" placeholder="New Username" required>
                                <input type="email" name="updated_email" placeholder="New Email" required>
                                <button type="submit" name="update_user">Update</button>
                            </form>

                            <!-- Delete Form -->
                            <form method="post" action="user_management.php">
                                <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                                <button type="submit" name="delete_user">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p>No registered users.</p>
    <?php endif; ?>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> Flavorful Shares By Maitri Patel</p>
    </footer>
</body>

</html>