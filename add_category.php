<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    echo "You need to log in to access this page.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ensure required fields are present
    if (isset($_POST['category_name']) && isset($_POST['description'])) {
        $category_name = htmlspecialchars($_POST['category_name']);
        $description = htmlspecialchars($_POST['description']);
        
        if (empty($category_name) || empty($description)) {
            echo "Category Name and Description are required.";
            exit;
        }

        $stmtInsert = $db->prepare("INSERT INTO categories (category_name, description) VALUES (?, ?)");
        $stmtInsert->execute([$category_name, $description]);

        header("Location: index.php");
        exit();
    } else {
        echo "Category Name and Description are required.";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Category</title>
    <link rel="stylesheet" href="styles.css">
</head>

</head>

<body>
    <main>
        <h1>Add Category </h1>

        <form action="add_category.php" method="post">
            <label for="category_name">Category Name:</label>
            <input type="text" name="category_name" required>

            <label for="description">Description:</label>
            <textarea name="description"></textarea>

            <input type="submit" value="Add Category">
        </form>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Flavorful Shares By Maitri Patel </p>
    </footer>

</body>

</html>