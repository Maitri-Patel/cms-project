<?php
session_start();

include 'connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php"); 
    exit;
}

function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function disemvowel($text) {
    return preg_replace('/[aeiouAEIOU]/', '', $text);
}

if (isset($_GET['comment_id']) && isset($_GET['action'])) {
    $commentId = (int)$_GET['comment_id'];
    $action = $_GET['action'];

    switch ($action) {
        case 'delete':
           
            $stmtDelete = $db->prepare("DELETE FROM comments WHERE comment_id = ?");
            $stmtDelete->execute([$commentId]);
            break;

        case 'hide':
            $stmtHide = $db->prepare("UPDATE comments SET is_visible = 0 WHERE comment_id = ?");
            $stmtHide->execute([$commentId]);
            break;

        case 'disemvowel':
            // Fetch the comment from the database
            $stmtFetch = $db->prepare("SELECT * FROM comments WHERE comment_id = ?");
            $stmtFetch->execute([$commentId]);
            $comment = $stmtFetch->fetch(PDO::FETCH_ASSOC);

            // Check if the comment exists
            if ($comment) {
                $disemvoweledText = disemvowel($comment['text']);
                $stmtDisemvowel = $db->prepare("UPDATE comments SET text = ? WHERE comment_id = ?");
                $stmtDisemvowel->execute([$disemvoweledText, $commentId]);
            }
            break;

        default:
            break;
    }

    header("Location: comment_management.php");
    exit;
}


$stmt = $db->query("SELECT * FROM comments WHERE is_visible = 1 ORDER BY created_at DESC");
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Comment Management</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>

    <main>
        <h2>Comment Management</h2>

        <?php foreach ($comments as $comment) : ?>
            <div>
                <p>User: <?= htmlspecialchars($comment['user_name']) ?></p>
                <p>Comment: <?= htmlspecialchars($comment['text']) ?></p>
                <p>Submitted at: <?= htmlspecialchars($comment['created_at']) ?></p>

                <a href="comment_management.php?comment_id=<?= $comment['comment_id'] ?>&action=delete">Delete</a>
                <a href="comment_management.php?comment_id=<?= $comment['comment_id'] ?>&action=hide">Hide</a>
                <a href="comment_management.php?comment_id=<?= $comment['comment_id'] ?>&action=disemvowel">Disemvowel</a>
            </div>
            <hr>
        <?php endforeach; ?>
    </main>

    <footer>
        
    </footer>
</body>

</html>
