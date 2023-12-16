<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Flavorful Shares</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Register for Flavorful Shares</h1>
    </header>
    <main>
        <form action="register_submit.php" method="post">
            Username: <input type="text" name="username" required><br>
            Email: <input type="email" name="email" required><br>
            Role: <input type="text" name="role" placeholder="member or admin"><br>
            Password: <input type="password" name="password" required><br>
            Confirm Password: <input type="password" name="confirm_password" required><br>
            <input type="submit" value="Register">
        </form>
    </main>
    <footer>
       
    </footer>
</body>
</html>
