<?php
session_start();
if (!isset($_SESSION['password'])) {
    header('location: auth/login.php');
    exit();
}
include 'database/conn-data.php';

// Check if the edit form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_id']) && isset($_POST['edit_content'])) {
    $edit_id = $_POST['edit_id'];
    $edit_content = $_POST['edit_content'];

    // Update the comment in the database
    $update_sql = "UPDATE comment SET content = '$edit_content' WHERE id = $edit_id";
    if ($conn->query($update_sql) === TRUE) {
        header('Location: comments.php');
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Retrieve the comment from the database based on the ID
    $select_sql = "SELECT * FROM comment WHERE id = $id";
    $result = $conn->query($select_sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $edit_content = $row['content'];
    } else {
        echo "Comment not found.";
        exit();
    }
}
?>

<?php include('includes/header.php'); ?>
<br><br><br><br>
<div class="container mt-4">
    <h3>Edit Comment</h3>
    <form action="edit_comment.php" method="post">
        <input type="hidden" name="edit_id" value="<?php echo $id; ?>">
        <div class="form-group">
            <label for="edit_content">Comment Content:</label>
            <textarea class="form-control" id="edit_content" name="edit_content" rows="4"><?php echo $edit_content; ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
    <a href="comments.php" class="btn btn-secondary mt-2">Cancel</a>
</div>
<?php include('includes/footer.php'); ?>
<?php include('includes/scripts.php'); ?>
