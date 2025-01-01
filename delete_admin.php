<?php
include('db.php');

if (isset($_POST['delete_btn'])) {
    $id = $_POST['delete_id'];

    $query = "DELETE FROM admin WHERE id='$id'";
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {
        $_SESSION['success'] = "Admin Profile Deleted";
        header("Location: register.php");
    } else {
        $_SESSION['status'] = "Admin Profile Not Deleted";
        header("Location: register.php");
    }
}
?>