<?php
session_start();
include 'database/conn-data.php';

// Check if the comment form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user'])) {
    $comment = $_POST['comment']; // Get the comment from the form
    $userId = $_SESSION['user']['user_id'];
    $articleId = $_POST['article_id'];
    $parentId = isset($_POST['parent_id']) ? $_POST['parent_id'] : null; // Check if 'parent_id' key exists, assign null if not present
    $commentQuery = "INSERT INTO comment (user_id, article_id, content, parent_id) VALUES ('$userId', '$articleId', '$comment', '$parentId')";
    mysqli_query($conn, $commentQuery);

    // Check if the delete button is clicked for a comment
    if (isset($_POST['delete_comment'])) {
        $deleteCommentId = $_POST['delete_comment_id'];

        // Delete the comment
        $deleteCommentQuery = "DELETE FROM comment WHERE id = '$deleteCommentId'";
        mysqli_query($conn, $deleteCommentQuery);

        // Redirect back to the current page after deleting the comment
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    // Check if the delete button is clicked for an article
    if (isset($_POST['delete_article'])) {
        $deleteArticleId = $_POST['delete_article_id'];

        // Delete the article
        $deleteArticleQuery = "DELETE FROM article WHERE id = '$deleteArticleId'";
        mysqli_query($conn, $deleteArticleQuery);

        // Redirect to the home page after deleting the article
        header('Location: index.php');
        exit;
    }
}

// Fetch all articles
$articleSql = "SELECT id, title, content, file_path, user_id, created_at, updated_at FROM article";
$articleResult = mysqli_query($conn, $articleSql);

$articles = array();
while ($row = mysqli_fetch_assoc($articleResult)) {
    $articleId = $row['id'];
    $commentSql = "SELECT `id`, `content`, `user_id`, `article_id`, `created_at`, `updated_at` FROM `comment`
    WHERE `comment`.`article_id` = '$articleId'";

    $commentResult = mysqli_query($conn, $commentSql);

    $comments = array();
    while ($comment = mysqli_fetch_assoc($commentResult)) {
        $commentId = $comment['id'];
        $childCommentSql = "SELECT `id`, `content`, `user_id`, `article_id`, `created_at`, `updated_at` FROM `comment`
        WHERE `comment`.`parent_id` = '$commentId'";

        $childCommentResult = mysqli_query($conn, $childCommentSql);

        $childComments = array();
        while ($childComment = mysqli_fetch_assoc($childCommentResult)) {
            $childComments[] = $childComment;
        }

        $comment['child_comments'] = $childComments;
        $comments[] = $comment;
    }

    $row['comments'] = $comments;
    $articles[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<br>
<?php if (isset($_SESSION['user'])): ?>
    <a href="Artical/addArtical.php" class="btn btn-primary">Add Article</a><br>
<?php endif; ?>

<?php foreach ($articles as $article): ?>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h1 class="card-title"><?php echo $article['title'] ?></h1>
            </div>
            <div class="card-body">
                <?php if (!empty($article['file_path'])): ?>
                    <img src="image/<?php echo $article['file_path']; ?>" alt="Article Image" style="max-width: 10%;">
                <?php endif; ?>
                <p class="card-text"><?php echo $article['content'] ?></p>
            </div>
            <div class="card-footer">
                <p>Created at: <?php echo $article['created_at'] ?></p>
                <p>Updated at: <?php echo $article['updated_at'] ?></p>
            </div>

            <?php if (isset($_SESSION['user']) && $_SESSION['user']['user_id'] === $article['user_id']): ?>
                <div class="card-footer">
                    <form method="POST" action="">
                        <input type="hidden" name="delete_article_id" value="<?php echo $article['id']; ?>">
                        <button type="submit" name="delete_article" class="btn btn-danger">Delete Article</button>
                    </form>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['user'])): ?>
                <!-- Comment form -->
                <div class="card-footer">
                    <form method="POST" action="">
                        <input type="hidden" name="article_id" value="<?php echo $article['id']; ?>">
                        <textarea name="comment" placeholder="Add a comment" class="form-control"></textarea>
                        <button type="submit" class="btn btn-primary mt-2">Submit</button>
                    </form>
                </div>
            <?php endif; ?>

            <!-- Displaying comments -->
            <div class="card-footer">
                <h3>Comments:</h3>
                <ul class="list-group">
                    <?php foreach ($article['comments'] as $comment): ?>
                        <li class="list-group-item">
                            <h6 class="list-group-item-heading">User ID: <?php echo $comment['user_id']; ?></h6>
                            <p class="list-group-item-text"><?php echo $comment['content']; ?></p>

                            <?php if (isset($_SESSION['user']) && $_SESSION['user']['user_id'] === $comment['user_id']): ?>
                                <form method="POST" action="">
                                    <input type="hidden" name="delete_comment_id" value="<?php echo $comment['id']; ?>">
                                    <button type="submit" name="delete_comment" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            <?php endif; ?>

                            <?php if (!empty($comment['child_comments'])): ?>
                                <ul class="list-group mt-2">
                                    <?php foreach ($comment['child_comments'] as $childComment): ?>
                                        <li class="list-group-item">
                                            <h6 class="list-group-item-heading">User ID: <?php echo $childComment['user_id']; ?></h6>
                                            <p class="list-group-item-text"><?php echo $childComment['content']; ?></p>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                            <?php if (!empty($comment['child_comments'])): ?>
                                <ul class="list-group mt-2">
                                    <?php foreach ($comment['child_comments'] as $childComment): ?>
                                        <li class="list-group-item">
                                            <h6 class="list-group-item-heading">User ID: <?php echo $childComment['user_id']; ?></h6>
                                            <p class="list-group-item-text"><?php echo $childComment['content']; ?></p>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>

                            <?php if (isset($_SESSION['user'])): ?>
                                <!-- Reply form -->
                                <form method="POST" action="">
                                    <input type="hidden" name="article_id" value="<?php echo $article['id']; ?>">
                                    <input type="hidden" name="parent_id" value="<?php echo $comment['id']; ?>">
                                    <textarea name="comment" placeholder="Reply to this comment" class="form-control"></textarea>
                                    <button type="submit" class="btn btn-primary mt-2 btn-sm">Submit Reply</button>
                                </form>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
    <br> <!-- Add a line break after each article -->
<?php endforeach; ?>

<br><br>

<?php if (isset($_SESSION['user'])): ?>
    <a href="logout.php" class="btn btn-primary">Logout</a>
<?php else: ?>
    <a href="register.php" class="btn btn-primary">Register</a><br><br><br>
    <a href="auth/login.php" class="btn btn-primary">Login</a>
<?php endif; ?>

</body>
</html>


