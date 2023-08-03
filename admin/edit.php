<?php
session_start();
if (!isset($_SESSION['password'])) {
    header('location: auth/login.php');
    exit();
}
include 'database/conn-data.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the post details from the database based on the provided ID
    $sql = "SELECT * FROM article WHERE id = $id";
    $result = $conn->query($sql);
    $post = $result->fetch_assoc();
}

// Handle the form submission for updating the post
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newTitle = $_POST['new_title'];
    $newContent = $_POST['new_content'];
    $newFilePath = $_POST['new_file_path'];

    // File upload handling
    if ($_FILES['new_image']['error'] === UPLOAD_ERR_OK) {
        $newImage = $_FILES['new_image']['name'];
        $imageTempPath = $_FILES['new_image']['tmp_name'];
        move_uploaded_file($imageTempPath, 'images/' . $newImage);
    } else {
        $newImage = $post['file_path']; // If no new image uploaded, use the current one
    }

    // Perform the update query
    $update_sql = "UPDATE article SET title = '$newTitle', content = '$newContent', file_path = '$newImage' WHERE id = $id";

    if ($conn->query($update_sql) === TRUE) {
        // Redirect to the page where the posts are listed after successful update
        header('Location: posts.php');
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

// Close the connection
$conn->close();
?>

<?php include('includes/header.php'); ?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Post</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Styles as before */

        img {
            max-width: 10%;
            height: auto;
        }
    </style>
</head>
<body>

<?php include('includes/header.php'); ?>

<div class="container mt-4">
    <br>
    <h2>Edit Post</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="new_title">Title</label>
            <input type="text" class="form-control" name="new_title" value="<?php echo $post['title']; ?>">
        </div>
        <div class="form-group">
            <label for="new_content">Content</label>
            <textarea class="form-control" name="new_content"><?php echo $post['content']; ?></textarea>
        </div>
        <div class="form-group">
            <label for="new_file_path">File Path</label>
            <input type="text" class="form-control" name="new_file_path" value="<?php echo $post['file_path']; ?>">
        </div>
        <div class="form-group">
            <label for="new_image">Current Image</label>
            <br>
            <img src="image/<?php echo $post['file_path']; ?>" alt="Current Image">
        </div>
        <div class="form-group">
            <label for="new_image">Upload New Image</label>
            <input type="file" class="form-control-file" name="new_image">
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>

<?php include('includes/footer.php'); ?>

</body>
</html>
