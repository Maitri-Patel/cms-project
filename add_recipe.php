<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    echo "You need to log in to access this page.";
    exit;
}

// Fetch all categories
$stmtCategories = $db->query("SELECT * FROM categories");
$categories = $stmtCategories->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim(filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $description = trim(filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $calories = filter_input(INPUT_POST, 'calories', FILTER_VALIDATE_INT);
    $cookingTime = filter_input(INPUT_POST, 'cooking_time', FILTER_VALIDATE_INT);
    $difficultyLevel = trim(filter_input(INPUT_POST, 'difficulty_level', FILTER_SANITIZE_FULL_SPECIAL_CHARS));

    
    if (empty($title) || empty($description)) {
        echo "Title and description are required.";
        exit;
    }


    if ($calories === false) {
        echo "Invalid value for calories.";
        exit;
    }

    if ($cookingTime === false) {
        echo "Invalid value for cooking time.";
        exit;
    }

    $imageFileName = "";
    $imageFilePath = "";

    if ($_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $fileType = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileType, $allowedTypes)) {
            $imageFileName = uniqid() . '_' . $_FILES['image']['name'];
            $imageFilePath = 'uploads/' . $imageFileName;

            move_uploaded_file($_FILES['image']['tmp_name'], $imageFilePath);
        } else {
            echo "Invalid image file or unsupported image type.";
            exit;
        }
    }

    $categoryId = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);

    if ($categoryId === false) {
        echo "Invalid value for category.";
        exit;
    }

    $stmt = $db->prepare("INSERT INTO recipes (user_id, title, description, image_path, category_id, calories, cooking_time, difficulty_level) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $title, $description, $imageFilePath, $categoryId, $calories, $cookingTime, $difficultyLevel]);

    $_SESSION['success_message'] = "Recipe added successfully!";
    header("Location: index.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Recipe - Flavorful Shares</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <header>
        <h1>Add Recipe to Flavorful Shares</h1>
    </header>
    <main>
        <form action="add_recipe.php" method="post" enctype="multipart/form-data">
            <label for="title">Title:</label>
            <input type="text" name="title" required><br>

            <label for="description">Description:</label>
            <textarea name="description" required></textarea><br>

            <label for="calories">Calories:</label>
            <input type="number" name="calories"><br>

            <label for="cooking_time">Cooking Time (minutes):</label>
            <input type="number" name="cooking_time"><br>

            <label for="difficulty_level">Difficulty Level:</label>
            <input type="text" name="difficulty_level"><br>

            <label for="image">Image (optional):</label>
            <input type="file" name="image"><br>

            <label for="category_id">Select Category:</label>
            <select name="category_id">
                <?php foreach ($categories as $category) : ?>
                    <option value="<?= $category['category_id'] ?>">
                        <?= $category['category_name'] ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <input type="submit" value="Add Recipe">
        </form>
    </main>
    <footer>
    
        <p>&copy; <?php echo date("Y"); ?> Flavorful Shares By Maitri Patel</p>
    
    </footer>
</body>

</html>
