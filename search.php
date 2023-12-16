<?php

session_start();
include 'connect.php';

// Fetch categories
$stmtCategories = $db->query("SELECT * FROM categories");
$categories = $stmtCategories->fetchAll(PDO::FETCH_ASSOC);

$selectedCategory = isset($_GET['category']) ? $_GET['category'] : 'all';
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';

if ($selectedCategory !== 'all') {
    // If a specific category is selected, filter by that category
    $stmt = $db->prepare("SELECT * FROM recipes WHERE (title LIKE :keyword OR description LIKE :keyword) AND category_id = :category");
    $stmt->execute(['keyword' => "%$keyword%", 'category' => $selectedCategory]);
} else {
    // If "all categories" is selected, search across all categories
    $stmt = $db->prepare("SELECT * FROM recipes WHERE title LIKE :keyword OR description LIKE :keyword");
    $stmt->execute(['keyword' => "%$keyword%"]);
}

$searchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Results</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Search Results</h1>
    </header>
    <main>
        <?php if (isset($searchResults) && !empty($searchResults)) : ?>
            <ul>
                <?php foreach ($searchResults as $result) : ?>
                    <li>
                        <h3><?= htmlspecialchars($result['title']) ?></h3>
                        <p><?= htmlspecialchars($result['description']) ?></p>
                        <a href="recipe.php?recipe_id=<?= $result['recipe_id'] ?>">View Recipe</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else : ?>
            <p>No results found.</p>
        <?php endif; ?>
    </main>
    <footer>
        <p>&copy; <?= date("Y"); ?> Flavorful Shares By Maitri Patel</p>
    </footer>
</body>

</html>
