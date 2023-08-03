<?php
$conn = mysqli_connect("localhost","root","","blog_phpdp");

// Check connection
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  exit();
}
?>
