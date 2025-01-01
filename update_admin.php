<?php
include('db.php');

if (isset($_POST['update_btn'])) {
    $id = $_POST['edit_id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // If password is empty, don't update it
    if (!empty($password)) {
        $password = md5($password);
        $query = "UPDATE admin SET username='$username', email='$email', password='$password' WHERE id='$id'";
    } else {
        $query = "UPDATE admin SET username='$username', email='$email' WHERE id='$id'";
    }

    $query_run = mysqli_query($conn, $query);

    if ($query_run) {
        $_SESSION['success'] = "Admin Profile Updated";
        header("Location: register.php");
    } else {
        $_SESSION['status'] = "Admin Profile Not Updated";
        header("Location: register.php");
    }
}
?>