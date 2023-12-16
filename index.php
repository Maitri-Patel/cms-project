<?php
session_start();

include 'connect.php';

if (isset($_SESSION['success_message'])) {
    echo "<p class='success'>" . $_SESSION['success_message'] . "</p>";
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
    echo "<p class='error'>" . $_SESSION['error_message'] . "</p>";
    unset($_SESSION['error_message']);
}

$isAdmin = isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin';

$stmtCategories = $db->query("SELECT * FROM categories");
$categories = $stmtCategories->fetchAll(PDO::FETCH_ASSOC);

$sortOptions = ['recipe_id', 'title', 'calories', 'cooking_time', 'difficulty_level'];
$sort = isset($_GET['sort']) && in_array($_GET['sort'], $sortOptions) ? $_GET['sort'] : 'recipe_id';
$order = isset($_GET['order']) && strtoupper($_GET['order']) === 'DESC' ? 'DESC' : 'ASC';

// Fetch recipes with sorting options
$stmt = $db->query("SELECT * FROM recipes ORDER BY $sort $order");
$recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Flavorful Shares</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div id="container">
        <header>
            <h1>Welcome to Flavorful Shares</h1>
            <nav>
                <form action="search.php" method="GET">
                    <label for="keyword">Search:</label>
                    <input type="text" name="keyword" id="keyword" placeholder="Enter keyword" value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>">

                    <label for="category">Select Category:</label>
                    <select name="category">
                        <option value="all" <?= ($selectedCategory === 'all') ? 'selected' : '' ?>>All Categories</option>
                        <?php foreach ($categories as $category) : ?>
                            <option value="<?= $category['category_id'] ?>" <?= ($selectedCategory == $category['category_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category['category_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <button type="submit">Search</button>
                </form>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <?php if ($isAdmin) : ?>
                        <li><a href="admin_dashboard.php">Admin Management</a></li>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['user_id'])) : ?>
                        <li><a href="logout.php">Logout</a></li>
                        <li><a href="add_recipe.php">Add your recipe here</a></li>
                        <li><a href="add_category.php">Add Categories here</a></li>
                        <li><a href="pages_by_category.php">Browse by Category</a></li>
                    <?php else : ?>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="register.php">Register</a></li>
                        <li><a href="pages_by_category.php">Browse by Category</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </header>

        <main>
            <h2>Discover and Share Your Favorite Recipes!</h2>
            <p>Welcome to Flavorful Shares, a community where cooking enthusiasts can share their unique recipes with the world. Join us today and add flavor to your life!</p>

            <!-- Display sorting options -->
            <?php if (isset($_SESSION['user_id'])) : ?>
                <p>Sort by:
                    
                    <a href="?sort=title&order=<?= $order ?>">Title</a> |
                    <a href="?sort=calories&order=<?= $order ?>">Calories</a> |
                    <a href="?sort=cooking_time&order=<?= $order ?>">Cooking Time</a> |
                    <a href="?sort=difficulty_level&order=<?= $order ?>">Difficulty Level</a>
                </p>
            <?php endif; ?>

            <?php if ($recipes) : ?>
                <h3>Recipes List</h3>
                <ul>
                    <?php foreach ($recipes as $recipe) : ?>
                        <ul class="recipe-card">
                            <h4><a href="recipe.php?recipe_id=<?= $recipe['recipe_id'] ?>"><?= htmlspecialchars($recipe['title']) ?></a></h4>
                            <p><?= htmlspecialchars($recipe['description']) ?></p>
                            <?php if (!empty($recipe['image_path'])) : ?>
                                <img src="<?= htmlspecialchars($recipe['image_path']) ?>" alt="Recipe Image">
                            <?php endif; ?>
                    
                            <p><strong>Calories:</strong> <?= isset($recipe['calories']) ? $recipe['calories'] . ' kcal' : 'N/A' ?></p>
                            <p><strong>Cooking Time:</strong> <?= isset($recipe['cooking_time']) ? $recipe['cooking_time'] . ' mins' : 'N/A' ?></p>
                            <p><strong>Difficulty Level:</strong> <?= isset($recipe['difficulty_level']) ? $recipe['difficulty_level'] : 'N/A' ?></p>
                            <a href="edit_recipe.php?recipe_id=<?= $recipe['recipe_id'] ?>">Edit Recipe</a><br>
                            <a href="comments.php?recipe_id=<?= $recipe['recipe_id'] ?>">Comments on Recipe</a>

                            <h5>Comments Section</h5>
                            <?php
                            $stmtComments = $db->prepare("SELECT * FROM comments WHERE recipe_id = ? ORDER BY created_at DESC");
                            $stmtComments->execute([$recipe['recipe_id']]);
                            $comments = $stmtComments->fetchAll(PDO::FETCH_ASSOC);

                            foreach ($comments as $comment) {
                                echo "<p>{$comment['user_name']} said: {$comment['text']}</p>";
                            }
                            ?>
                        </ul>
                    <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <p>No recipes available.</p>
            <?php endif; ?>
        </main>

        <footer>
            <div class="footer-content">
                <div class="footer-section about">
                    <h2>About Us</h2>
                    <p>Welcome to Flavorful Shares, your go-to community for sharing and discovering delicious recipes. Join our cooking enthusiasts and spice up your culinary journey!</p>
                </div>

                <div class="footer-section contact">
                    <h2>Contact Us</h2>
                    <p>Email: info@flavorfulshares.com</p>
                    <p>Phone: +1 (431) 669-6304</p>
                </div>

                <div class="footer-section social">
                    <h2>Connect with Us</h2>
                    <a href="#" class="social-icon"><img src="facebook.png" alt="Facebook"></a>
                    <a href="#" class="social-icon"><img src="twitter.png" alt="Twitter"></a>
                    <a href="#" class="social-icon"><img src="insta logo.png" alt="Instagram"></a>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; <?php echo date("Y"); ?> Flavorful Shares By Maitri Patel</p>
            </div>
        </footer>
    </div>
</body>

</html>
