<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'connect.php';

function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

if (isset($_REQUEST['recipe_id'])) {
    $recipe_id = (int)$_REQUEST['recipe_id'];
} else {
    echo "Recipe ID is missing.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_name = sanitizeInput($_POST['user_name']);
    $comment_text = sanitizeInput($_POST['comment']);
    $recipe_id = filter_input(INPUT_POST, 'recipe_id', FILTER_VALIDATE_INT);

    // Check if the recipe_id is valid
    if ($recipe_id === false || $recipe_id === null) {
        echo "Invalid recipe ID.";
        exit;
    }

    // Check if the recipe_id exists in the recipes table
    $checkRecipeStmt = $db->prepare("SELECT COUNT(*) FROM recipes WHERE recipe_id = ?");
    $checkRecipeStmt->execute([$recipe_id]);
    $recipeExists = $checkRecipeStmt->fetchColumn();

    if (!$recipeExists) {
        echo "Recipe does not exist.";
        exit;
    }

    // Insert the comment into the database
    $stmt = $db->prepare("INSERT INTO comments (user_name, recipe_id, text, created_at) VALUES (?, ?, ?, current_timestamp())");
    $stmt->execute([$user_name, $recipe_id, $comment_text]);
    header("Location: index.php");
    exit;
}

// Fetch comments
$stmt = $db->prepare("SELECT * FROM comments WHERE recipe_id = ? ORDER BY created_at DESC");
$stmt->execute([$recipe_id]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Flavorful Shares</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
<h1>Comment section</h1>

<form action="comments.php" method="post">
    <label for="user_name">Your Name:</label>
    <input type="text" name="user_name" required><br>

    <label for="comment">Your Comment:</label>
    <textarea name="comment" required></textarea><br>

    <input type="hidden" name="recipe_id" value="<?php echo $recipe_id; ?>">

    <input type="submit" value="Submit Comment">
</form>

<!-- Display comments -->
<?php foreach ($comments as $comment) : ?>
    <div>
        <p><?= htmlspecialchars($comment['user_name']) ?> said:</p>
        <p><?= htmlspecialchars($comment['text']) ?></p>
        <p>Posted at: <?= htmlspecialchars($comment['created_at']) ?></p>
    </div>
<?php endforeach; ?>
<footer></footer>
</body>

</html>