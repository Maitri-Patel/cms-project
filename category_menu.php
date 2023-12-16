<!-- category_menu.php -->
<?php
session_start();
include 'connect.php';

// Fetch all categories
$stmtCategories = $db->query("SELECT * FROM categories");
$categories = $stmtCategories->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Category Menu</title>
    <link rel="stylesheet" href="styles.css">
    
</head>
<body>

<main>
    <h1>Category Menu</h1>
    <ul>
        <?php foreach ($categories as $category): ?>
            <li><a href="pages_by_category.php?category_id=<?= $category['category_id'] ?>"><?= $category['category_name'] ?></a></li>
        <?php endforeach; ?>
    </ul>
    <a href="index.php">Back to Home</a>
</main>

<footer>
   
</footer>

</body>
</html>
