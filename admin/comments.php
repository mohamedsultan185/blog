<?php
session_start();
if (!isset($_SESSION['password'])) {
    header('location: auth/login.php');
    exit();
}
include 'database/conn-data.php';

// Check if the edit form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id']) && isset($_POST['content'])) {
    $id = $_POST['id'];
    $content = $_POST['content'];

    // Update the comment in the database
    $update_sql = "UPDATE comment SET content = '$content' WHERE id = $id";
    if ($conn->query($update_sql) === TRUE) {
        header('Location: comments.php');
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

$sql = "SELECT c.id, c.content, c.user_id, c.article_id, c.parent_id, c.created_at, c.updated_at, u.username, a.title, p.content AS parent_content
        FROM comment c
        JOIN user u ON c.user_id = u.id
        JOIN article a ON c.article_id = a.id
        LEFT JOIN comment p ON c.parent_id = p.id";



$result = $conn->query($sql);
?>

<?php include('includes/header.php'); ?>
<br><br><br><br>
<div class="container mt-4">
    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Content</th>
                <th scope="col">Username</th>
                <th scope="col">Article Title</th>
                <th scope="col">Parent Comment</th> <!-- Added: Show "Parent Comment" instead of "parent_id" -->
                <th scope="col">Created At</th>
                <th scope="col">Updated At</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0) {
                // Output data of each row using foreach loop
                foreach ($result as $row) { ?>
                    <tr>
                        <td><?php echo $row["id"]; ?></td>
                        <td><?php echo $row["content"]; ?></td>
                        <td><?php echo $row["username"]; ?></td>
                        <td><?php echo $row["title"]; ?></td>
                        <td><?php echo $row["parent_content"]; ?></td> <!-- Show parent comment content -->
                        <td><?php echo $row["created_at"]; ?></td>
                        <td><?php echo $row["updated_at"]; ?></td>
                        <td>
                            <!-- Add the Edit button with a link to edit the row -->
                            <a href="edit_comment.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">Edit</a>

                            <!-- Add the Delete button with a link to delete the row -->
                            <a href="?action=delete&id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                        </td>
                    </tr>
                <?php }
            } else { ?>
                <tr>
                    <td colspan="8">No data found in the table.</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <a href="auth/logout.php" class="btn btn-primary btn-sm">Logout</a>
</div>

<?php include('includes/footer.php'); ?>
<?php include('includes/scripts.php'); ?>
