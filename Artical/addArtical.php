<?php
session_start();
if(!isset($_SESSION['user'])){
    header('location:../login.php');
    exit();
};
include '../database/conn-data.php';
if(isset($_POST['submit']))
 {
    $tital=filter_var($_POST['title'],FILTER_SANITIZE_STRING);
    $content =filter_var($_POST['content'],FILTER_SANITIZE_STRING);
    $title= $_POST['title'];
    $content = $_POST['content'];
    $file= $_POST['file'];
    $created_at = date('Y-m-d H:i:s');
    $updated_at = date('Y-m-d H:i:s');
    $user_id = $_SESSION['user']['user_id'];
    $stm="INSERT INTO article (title,content,file_path ,user_id) VALUES ('$title','$content' ,'$file',$user_id  ) ";
$result=$conn->prepare($stm)->execute();
    if ($result) {
        header ('location:../index.php');

    } else {
        echo "Error: " . mysqli_error($conn);
    }
 }

?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../css/addComment.css">
    <title>Add Artical</title>
</head>
<body>
    <div class = "container">
    <h2>Add artical</h2>
    <form action="addArtical.php" method="POST">
        <label for="title">Title</label>
        <input type="text" name="title" id="title"><br><br>
        
        <label for="contnet">Contnet:</label>
        <textarea name="content" id="content" rows="4"></textarea>
        <label for="file">FileToUpload:</label>

        <input type="file" class ="file" name="file" id="fileToUpload">

        <input type="submit" name ="submit"  value="Submit">


    </form>
    </div>
</body>
</html>