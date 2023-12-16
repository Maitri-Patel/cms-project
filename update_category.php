<?php
session_start();
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['category_id'], $_POST['category_name'], $_POST['description'])) {
        // Sanitize input data
        $category_id = filter_input(INPUT_POST, 'category_id', FILTER_SANITIZE_NUMBER_INT);
        $category_name = filter_input(INPUT_POST, 'category_name', FILTER_SANITIZE_STRING);
        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);

        $category_id = filter_var($category_id, FILTER_VALIDATE_INT);

        if ($category_id === false) {
            $_SESSION['error_message'] = "Invalid category ID.";
            header("Location: update_category.php?category_id=$category_id");
            exit();
        }

        $stmt = $db->prepare("UPDATE categories SET category_name = ?, description = ? WHERE category_id = ?");
        $result = $stmt->execute([$category_name, $description, $category_id]);

        if ($result) {
            $_SESSION['success_message'] = "Category updated successfully.";
        } else {
            $_SESSION['error_message'] = "Error updating category.";
        }

        // Redirect to a page after updating (adjust the URL as needed)
        header("Location: category_management.php");
        exit();
    } else {
        $_SESSION['error_message'] = "All fields are required.";
    }
}


if (isset($_GET['category_id'])) {
    $category_id = filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT);

    // Fetch the category details
    $stmtCategory = $db->prepare("SELECT * FROM categories WHERE category_id = ?");
    $stmtCategory->execute([$category_id]);
    $category = $stmtCategory->fetch(PDO::FETCH_ASSOC);

    if (!$category) {
        $_SESSION['error_message'] = "Category not found.";
        header("Location: update_category.php?category_id=$category_id");
        exit();
    }
} else {
    $_SESSION['error_message'] = "Category ID is missing.";
    header("Location: update_category.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Update Category</title>
    <link rel="stylesheet" href="styles.css">

</head>

<body>

<main>
    <h1>Update Category</h1>

    <?php if (isset($_SESSION['error_message'])) : ?>
        <p style="color: red;"><?= $_SESSION['error_message'] ?></p>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success_message'])) : ?>
        <p style="color: green;"><?= $_SESSION['success_message'] ?></p>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <form action="update_category.php" method="post">
        <label for="category_name">Category Name:</label>
        <input type="text" name="category_name" value="<?= htmlspecialchars($category['category_name']) ?>" required>

        <label for="description">Description:</label>
        <textarea name="description"><?= htmlspecialchars($category['description']) ?></textarea>

        <input type="hidden" name="category_id" value="<?= $category_id ?>">
       
        <input type="submit" value="Update Category">
    </form>

    
</main>

<footer>
   

</body>

</html>
