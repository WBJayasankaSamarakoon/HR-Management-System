<?php
session_start();
include('db.php');
include('security.php');

// Admin Registration Logic
if (isset($_POST['registerbtn'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirmpassword']);


    if ($password === $confirm_password) {

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);


        $query = "INSERT INTO admin (username, email, password) VALUES ('$username', '$email', '$hashed_password')";
        $query_run = mysqli_query($conn, $query);

        if ($query_run) {
            $_SESSION['success'] = "Admin profile added successfully";
            header('Location: register.php');
        } else {
            $_SESSION['status'] = "Admin profile not added";
            header('Location: register.php');
        }
    } else {
        $_SESSION['status'] = "Passwords do not match";
        header('Location: register.php');
    }
}

// Admin Login Logic
if (isset($_POST['login_btn'])) {
    $email = mysqli_real_escape_string($conn, $_POST['emaill']);
    $password = mysqli_real_escape_string($conn, $_POST['passwordd']);


    $query = "SELECT * FROM admin WHERE email='$email'";
    $query_run = mysqli_query($conn, $query);

    if (mysqli_num_rows($query_run) > 0) {
        $row = mysqli_fetch_assoc($query_run);
        $db_password = $row['password'];


        if (password_verify($password, $db_password)) {
            $_SESSION['username'] = $row['username'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['role'] = 'admin';

            header('Location: index.php');
        } else {
            $_SESSION['status'] = "Invalid Email or Password";
            header('Location: login.php');
        }
    } else {
        $_SESSION['status'] = "Email not found";
        header('Location: login.php');
    }
}

?>