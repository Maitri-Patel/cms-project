<?php
session_start();
include 'connect.php';

$stmtRecipes = $db->query("SELECT * FROM recipes");
$recipes = $stmtRecipes->fetchAll(PDO::FETCH_ASSOC);

$stmtCategories = $db->query("SELECT * FROM categories");
$categories = $stmtCategories->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Assign Categories to Recipes</title>
    <link rel="stylesheet" href="styles.css">

</head>

<body>

    <main>
        <h1>Assign Categories to Recipes</h1>


        <form action="process_assign_category.php" method="post">
            <label for="recipe_id">Select Recipe:</label>
            <select name="recipe_id">
                <?php foreach ($recipes as $recipe) : ?>
                    <option value="<?= $recipe['recipe_id'] ?>"><?= $recipe['title'] ?></option>
                <?php endforeach; ?>
            </select>

            <label for="category_id">Select Category:</label>
            <select name="category_id">
                <?php foreach ($categories as $category) : ?>
                    <option value="<?= $category['category_id'] ?>"><?= $category['category_name'] ?></option>
                <?php endforeach; ?>
            </select>

            <input type="submit" value="Assign Category">
        </form>

    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Flavorful Shares By Maitri Patel</p>
    </footer>

</body>

</html>