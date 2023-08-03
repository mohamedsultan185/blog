<?php
session_start();
if (!isset($_SESSION['password'])) {
    header('location: auth/login.php');
    exit();
}
include 'database/conn-data.php';

// Check if the delete button is pressed and the ID is provided
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    // Perform the delete query
    $delete_sql = "DELETE FROM user WHERE id = $id";
    if ($conn->query($delete_sql) === TRUE) {

        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

$sql = "SELECT * FROM user";
$result = $conn->query($sql);

// Close the connection
$conn->close();

?>
<?php include('includes/header.php'); ?>
<br><br><br><br>
<div class="container mt-4">
    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Username</th>
                <th scope="col">Email</th>
                <th scope="col">Password</th>
                <th scope="col">Phone</th>
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
                        <td><?php echo $row["username"]; ?></td>
                        <td><?php echo $row["email"]; ?></td>
                        <td><?php echo $row["password"]; ?></td>
                        <td><?php echo $row["phone"]; ?></td>
                        <td><?php echo $row["created_at"]; ?></td>
                        <td><?php echo $row["updated_at"]; ?></td>
                        <td>
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
