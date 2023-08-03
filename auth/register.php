<?php
 include '../database/conn-data.php';
 if(isset($_POST['submit'])){
    //filter script
    $username=filter_var($_POST['username'],FILTER_SANITIZE_STRING);
    $password=filter_var($_POST['password'],FILTER_SANITIZE_STRING);
    $email=filter_var($_POST['email'],FILTER_SANITIZE_EMAIL);
    $phone=filter_var($_POST['username'],FILTER_SANITIZE_STRING);  
    $errors =[];
      // validate name
   if(empty($username)){
    $errors[]="يجب كتابة الاسم";
}elseif(strlen($username)>100){
    $errors[]="يجب ان لايكون الاسم اكبر من 100 حرف ";
}
 // validate email
 if(empty($email)){
    $errors[]="يجب كتابة البريد الاكترونى";
   }elseif(filter_var($email,FILTER_VALIDATE_EMAIL)==false){
    $errors[]="البريد الاكترونى غير صالح";
   }

   $stm="SELECT email FROM user WHERE email ='$email'";
   $q = mysqli_query($conn,$stm);
   $data = mysqli_fetch_array($q);

   if($data){
     $errors[]="البريد الاكترونى موجود بالفعل";
   }


   // validate password
   if(empty($password)){
        $errors[]="يجب كتابة  كلمة المرور ";
   }elseif(strlen($password)<6){
    $errors[]="يجب ان لايكون كلمة المرور  اقل  من 6 حرف ";
}
  // insert or errros 
  if(empty($errors)){
    // echo "insert db";

    $password=password_hash($password,PASSWORD_DEFAULT);
    $stm="INSERT INTO user (username ,email,password, phone) VALUES ('$username','$email','$password','$phone')";
    $conn->prepare($stm)->execute();
    $_POST['username']='';
    $_POST['email']='';
    $_POST['phone']='';

    $_SESSION['user']=[
        "username"=>$username,
        "email"=>$email,
      ];
      header('location:../home.php');
    } 
}

?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="../css/register.css">
    <title>User Registration</title>
</head>
<body><div class="container">

    <h2>User Registration</h2>
    <form action="register.php" method="POST">
    <?php 
        if(isset($errors)){
            if(!empty($errors)){
                foreach($errors as $msg){
                    echo $msg . "<br>";
                }
            }
        }
    ?>
        <label for="username">Username:</label>
        <input type="text" id="username"value="<?php if(isset($_POST['username'])){echo $_POST['username'];} ?>"  name="username" required><br><br>
        
        <label for="phone">Phone:</label>
        <input type="text" id="phone" name="phone"value="<?php if(isset($_POST['phone'])){echo $_POST['phone'];} ?>"  required><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email"value="<?php if(isset($_POST['email'])){echo $_POST['email'];} ?>"  name="email" required><br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>

        

        <input type="submit" value="Register" name= "submit">
    </form>
</div>


</body>


</html>