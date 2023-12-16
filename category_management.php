
<?php
session_start();
include 'connect.php'; 

$stmt = $db->query("SELECT * FROM categories");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Category Management</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header>
</header>

<main>
    <h1>Category Management</h1>
    <br>

    <table>
        <tr>
            <th>Category Name</th>
            <th>Description</th>
            <th>Edit</th>
        </tr>
        <?php foreach ($categories as $category): ?>
            <tr>
                <td><?= $category['category_name'] ?></td>
                <td class="table"><?= $category['description'] ?></td>
                <td class="space">
                    <a href="update_category.php?category_id=<?= $category['category_id'] ?>">Edit</a>
                    
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

   
    <a href="admin_dashboard.php">Back to Admin Dashboard</a>
<br>
    <a href="add_category.php">Add New Category</a>
</main>

<footer>
   
</footer>

</body>
</html>
