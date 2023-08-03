<?php
session_start();
if(isset($_SESSION['user'])){
    header('location:../index.php');
    exit();
}
require '../database/conn-data.php';

// Check if the "Remember Me" checkbox is checked
if(isset($_POST['submit']) && isset($_POST['remember'])){
    // Set a cookie to remember the user for a month
    $expiry = time() + (30 * 24 * 60 * 60); // 30 days
    setcookie('remember_me', $_POST['email'], $expiry);
}

if(isset($_POST['submit'])){
    $password=filter_var($_POST['password'],FILTER_SANITIZE_STRING);
    $email=filter_var($_POST['email'],FILTER_SANITIZE_EMAIL);

    $errors=[];
    // validate email
    if(empty($email)){
        $errors[]="يجب كتابة البريد الاكترونى";
    }
    // validate password
    if(empty($password)){
        $errors[]="يجب كتابة  كلمة المرور ";
    }

    // insert or errors
    if(empty($errors)){
        $stm="SELECT * FROM user WHERE email ='$email'";
        $q = mysqli_query($conn,$stm);
        $data = mysqli_fetch_array($q);

        if(!$data){
            $errors[] = "خطأ فى تسجيل الدخول";
        }else{
            $password_hash=$data['password'];
            if(!password_verify($password,$password_hash)){
                $errors[] = "خطأ فى تسجيل الدخول";
            }else{
                $_SESSION['user']=[
                    "email"=>$email,
                    "user_id"=>$data["id"],
                ];
                header('location:../index.php');
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSS Stylesheets -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/login.css">
    <title>Login</title>

</head>
<body>
    <div class="container">
        <form action="login.php" method="POST">
            <div class="error">
                <?php 
                if(isset($errors)){
                    if(!empty($errors)){
                        foreach($errors as $msg){
                            echo $msg . "<br>";
                        }
                    }
                }
                ?>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="text" value="<?php if(isset($_POST['email'])){echo $_POST['email'];} ?>" name="email" class="form-control" placeholder="Email">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Password">
            </div>
            <div class="form-group form-check">
                <input type="checkbox" name="remember" class="form-check-input">
                <label class="form-check-label" for="remember">Remember Me</label>
            </div>
            <div class="login">
                <input type="submit" name="submit" value="Login" class="btn btn-secondary">
            </div>
            <br>
            <div class="register">
                <button class="btn btn-secondary"><a href="register.php">Register</a></button>
            </div>
        </form>
    </div>
</body>
</html>
