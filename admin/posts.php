<?php
session_start();
if (!isset($_SESSION['password'])) {
    header('location: login.php');
    exit();
}

include 'database/conn-data.php';

?>

<?php include('includes/header.php'); ?>

<?php
// Check if the delete button is pressed and the ID is provided
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    // Perform the delete query
    $delete_sql = "DELETE FROM article WHERE id = $id";
    if ($conn->query($delete_sql) === TRUE) {
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

// SQL query to fetch article data with user names
$sql = "SELECT article.id, article.title, article.content, article.file_path, 
               article.user_id, article.created_at, article.updated_at, user.username
        FROM article 
        LEFT JOIN user ON article.user_id = user.id
        ORDER BY article.title ASC"; // Order articles by title in ascending order

$result = $conn->query($sql);

// Check if the query was executed successfully
if (!$result) {
    echo "Error executing the query: " . $conn->error;
    exit();
}
?>

<br><br><br><br>
<div class="container mt-4">
    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Title</th>
                <th scope="col">Content</th>
                <th scope="col">File Path</th>
                <th scope="col">Username</th>
                <th scope="col">Created At</th>
                <th scope="col">Updated At</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Check if there are any rows in the result set
            if ($result->num_rows > 0) {
                // Output data of each row using while loop
                while ($row = $result->fetch_assoc()) {
                    ?>
                    <tr>
                        <td><?php echo $row["id"]; ?></td>
                        <td><?php echo $row["title"]; ?></td>
                        <td><?php echo $row["content"]; ?></td>
                        <td>
                            <?php
                                $imagePath = $row["file_path"];
                                if ($imagePath) {
                                    // If the file_path is a valid URL, display the image
                                    echo "<img src='./image/".$imagePath."' alt='Article Image' style='max-width: 100px; max-height: 100px;' />";
                                } else {
                                    // Otherwise, display a message indicating that the image is not available
                                    echo "Image not available";
                                }
                            ?>
                        </td>
                        <td><?php echo $row["username"]; ?></td> <!-- Display the username instead of user_id -->
                        <td><?php echo $row["created_at"]; ?></td>
                        <td><?php echo $row["updated_at"]; ?></td>
                        <td>
                            <!-- Edit button with link to edit.php page and passing the post ID -->
                            <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                            <!-- Add the Delete button with a link to delete the row -->
                            <a href="?action=delete&id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                        </td>
                    </tr>
                <?php
                }
            } else {
                // No data found in the table
                echo "<tr><td colspan='9'>No data found in the table.</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <a href="auth/logout.php" class="btn btn-primary btn-sm">Logout</a>
</div>

<?php include('includes/footer.php'); ?>
<?php include('includes/scripts.php'); ?>

<?php
// Close the connection
$conn->close();
?>
