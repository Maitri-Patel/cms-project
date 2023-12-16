
<?php
session_start();
include 'connect.php'; 


if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   
    if (isset($_POST['category_id']) && isset($_POST['category_name']) && isset($_POST['description'])) {
        $category_id = $_POST['category_id'];
        $category_name = $_POST['category_name'];
        $description = $_POST['description'];

        // Update the category in the database
        $stmtUpdate = $db->prepare("UPDATE categories SET category_name = ?, description = ? WHERE category_id = ?");
        $stmtUpdate->execute([$category_name, $description, $category_id]);

        // Redirect or handle success as needed
        header("Location: category_management.php");
        exit();
    }
}

// Handle other cases or redirect if needed
header("Location: index.php");
exit();
?>
