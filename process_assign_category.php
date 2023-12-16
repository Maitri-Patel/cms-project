
<?php
session_start();
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    if (isset($_POST['recipe_id']) && isset($_POST['category_id'])) {
        $recipe_id = $_POST['recipe_id'];
        $category_id = $_POST['category_id'];

        $stmtUpdate = $db->prepare("UPDATE recipes SET category_id = ? WHERE recipe_id = ?");
        $stmtUpdate->execute([$category_id, $recipe_id]);

        header("Location: index.php");
        exit();
    }
}

exit();
?>
