<?php
session_start();
include 'connect.php';


if (!isset($_GET['category_id'])) {
    header("Location: category_menu.php");
    exit();
}

$category_id = $_GET['category_id'];


$stmtCategory = $db->prepare("SELECT * FROM categories WHERE category_id = ?");
$stmtCategory->execute([$category_id]);
$category = $stmtCategory->fetch(PDO::FETCH_ASSOC);


if (!$category) {

    header("Location: category_menu.php");
    exit();
}

// Fetch all recipes for the selected category
$stmtRecipes = $db->prepare("SELECT * FROM recipes WHERE category_id = ?");
$stmtRecipes->execute([$category_id]);
$recipes = $stmtRecipes->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Recipes by Category</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>

    <main>
        <h1>Recipes in <?= $category['category_name'] ?></h1>

        <!-- Display all recipes for the selected category -->
        <?php if ($recipes) : ?>
            <ul>
                <?php foreach ($recipes as $recipe) : ?>
                    <li>
                        <h2><?= $recipe['title'] ?></h2>
                        <p><?= $recipe['description'] ?></p>
                        <img src="<?= htmlspecialchars($recipe['image_path']) ?>" alt="Recipe Image">

                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else : ?>
            <p>No recipes available for this category.</p>
        <?php endif; ?>

        <a href="category_menu.php">Back to Category Menu</a>
    </main>
    
    <footer>
        <p>&copy; <?php echo date("Y"); ?> Flavorful Shares By Maitri Patel</p>
    </footer>


</body>

</html>