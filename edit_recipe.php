<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    echo "You need to log in to access this page.";
    exit;
}

if (isset($_REQUEST['recipe_id'])) {
    $recipe_id = $_REQUEST['recipe_id'];
} else {
    echo "Recipe ID is missing.";
    exit;
}

$stmt = $db->prepare("SELECT * FROM recipes WHERE recipe_id = ?");
$stmt->execute([$recipe_id]);
$recipe = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$recipe) {
    echo "Recipe not found.";
    exit;
}

if ($_SESSION['role'] !== 'admin' && $_SESSION['user_id'] !== $recipe['user_id']) {
    echo "You do not have permission to edit or delete this recipe.";
    exit;
}

$stmtCategories = $db->query("SELECT * FROM categories");
$categories = $stmtCategories->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update_recipe'])) {
        $newTitle = trim(filter_input(INPUT_POST, 'new_title', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
        $newDescription = trim(filter_input(INPUT_POST, 'new_description', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
        $newCalories = filter_input(INPUT_POST, 'new_calories', FILTER_VALIDATE_INT);
        $newCookingTime = filter_input(INPUT_POST, 'new_cooking_time', FILTER_VALIDATE_INT);
        $newDifficultyLevel = trim(filter_input(INPUT_POST, 'new_difficulty_level', FILTER_SANITIZE_FULL_SPECIAL_CHARS));

        $newcategoryId = filter_input(INPUT_POST, 'new_category_id', FILTER_VALIDATE_INT);

        
        if (empty($newTitle) || empty($newDescription)) {
            $_SESSION['error_message'] = "Title and description are required.";
            header("Location: edit_recipe.php?recipe_id=$recipe_id");
            exit;
        }

        if ($newCalories === false) {
            $_SESSION['error_message'] = "Invalid value for calories.";
            header("Location: edit_recipe.php?recipe_id=$recipe_id");
            exit;
        }

        if ($newCookingTime === false) {
            $_SESSION['error_message'] = "Invalid value for cooking time.";
            header("Location: edit_recipe.php?recipe_id=$recipe_id");
            exit;
        }

        $newImagePath = $recipe['image_path'];

        if ($_FILES['new_image']['error'] == UPLOAD_ERR_OK) {
            $fileType = strtolower(pathinfo($_FILES['new_image']['name'], PATHINFO_EXTENSION));
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($fileType, $allowedTypes)) {
                $newImagePath = 'uploads/' . uniqid() . '_' . $_FILES['new_image']['name'];

               
                if (!move_uploaded_file($_FILES['new_image']['tmp_name'], $newImagePath)) {
                    $_SESSION['error_message'] = "Error uploading the new image.";
                    header("Location: edit_recipe.php?recipe_id=$recipe_id");
                    exit;
                }
            } else {
                echo "Invalid image file or unsupported image type.";
                exit;
            }
        }

        $stmt = $db->prepare("UPDATE recipes SET title = ?, description = ?, calories = ?, cooking_time = ?, difficulty_level = ?, category_id = ?, image_path = ? WHERE recipe_id = ?");
        $stmt->execute([$newTitle, $newDescription, $newCalories, $newCookingTime, $newDifficultyLevel, $newcategoryId, $newImagePath, $recipe_id]);
        
        if (!empty($recipe['image_path'])) {
            if (isset($_POST['delete_image']) && $_POST['delete_image'] == 'on') {
                unlink($recipe['image_path']);
                $stmt = $db->prepare("UPDATE recipes SET image_path = NULL WHERE recipe_id = ?");
                $stmt->execute([$recipe_id]);
            }
        }

        $_SESSION['success_message'] = "Recipe updated successfully!";
        header("Location: index.php");
        exit;
        
    } elseif (isset($_POST['delete_recipe'])) {
        $stmt = $db->prepare("DELETE FROM recipes WHERE recipe_id = ?");
        $stmt->execute([$recipe_id]);

        // Delete the image file if exists
        if (!empty($recipe['image_path'])) {
            unlink($recipe['image_path']);
        }

        $_SESSION['success_message'] = "Recipe deleted successfully!";
        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Recipe - Flavorful Shares</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <header>
        <h1>Edit Recipe on Flavorful Shares</h1>
    </header>
    <main>
        <?php if ($recipe) : ?>
            <form action="edit_recipe.php?recipe_id=<?= $recipe_id ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="recipe_id" value="<?= $recipe_id ?>">

                <label for="new_title">Title:</label>
                <input type="text" name="new_title" value="<?= htmlspecialchars($recipe['title']) ?>" required><br>

                <label for="new_description">Description:</label>
                <textarea name="new_description" required><?= htmlspecialchars($recipe['description']) ?></textarea><br>

                <label for="new_image">New Image:</label>
                <input type="file" name="new_image">

                <label for="new_calories">Calories:</label>
                <input type="number" name="new_calories" value="<?= htmlspecialchars($recipe['calories']) ?>"><br>

                <label for="new_cooking_time">Cooking Time (minutes):</label>
                <input type="number" name="new_cooking_time" value="<?= htmlspecialchars($recipe['cooking_time']) ?>"><br>

                <label for="new_difficulty_level">Difficulty Level:</label>
                <input type="text" name="new_difficulty_level" value="<?= htmlspecialchars($recipe['difficulty_level']) ?>"><br>

                <label for="new_category_id">Select Category:</label>
                <select name="new_category_id">
                    <?php foreach ($categories as $category) : ?>
                        <option value="<?= $category['category_id'] ?>" <?= ($category['category_id'] == $recipe['category_id']) ? 'selected' : '' ?>>
                            <?= $category['category_name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <?php if (!empty($recipe['image_path'])) : ?>
                    <label for="delete_image">Delete Image:</label>
                    <input type="checkbox" name="delete_image">
                <?php endif; ?>

                <button type="submit" name="update_recipe">Update Recipe</button>
                <button type="submit" name="delete_recipe">Delete Recipe</button>
            </form>
        <?php else : ?>
            <p>Recipe not found.</p>
        <?php endif; ?>
    </main>
</body>

</html>
