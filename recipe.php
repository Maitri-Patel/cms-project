<?php
session_start();

include 'connect.php';


if (isset($_GET['recipe_id'])) {
    $recipeId = $_GET['recipe_id'];

    $stmt = $db->prepare("SELECT * FROM recipes WHERE recipe_id = ?");
    $stmt->execute([$recipeId]);
    $recipeDetails = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmtComments = $db->prepare("SELECT * FROM comments WHERE recipe_id = ? ORDER BY created_at DESC");
    $stmtComments->execute([$recipeId]);
    $comments = $stmtComments->fetchAll(PDO::FETCH_ASSOC);
} else {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($recipeDetails['title']) ?> - Recipe Details</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <header>
        <h1>Flavorful Shares</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h2><?= htmlspecialchars($recipeDetails['title']) ?> - Recipe Details</h2>

        <p><?= htmlspecialchars($recipeDetails['description']) ?></p>

        <?php if (!empty($recipeDetails['image_path'])) : ?>
            <img src="<?= htmlspecialchars($recipeDetails['image_path']) ?>" alt="Recipe Image">
        <?php endif; ?>
        <a href="edit_recipe.php?recipe_id=<?= $recipe['recipe_id'] ?>">Edit Recipe</a>
        <a href="comments.php?recipe_id=<?= $recipe['recipe_id']?>">Comments on Recipe</a>

        <h3>Comments Section</h3>
        <?php foreach ($comments as $comment) : ?>
            <p><?= htmlspecialchars($comment['user_name']) ?> said: <?= htmlspecialchars($comment['text']) ?></p>
        <?php endforeach; ?>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Flavorful Shares By Maitri Patel </p>
    </footer>
</body>

</html>
