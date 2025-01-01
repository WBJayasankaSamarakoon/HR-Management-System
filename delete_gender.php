<?php
include('security.php');

if (isset($_POST['delete_gender_btn'])) {
    $gender_id = $_POST['delete_id'];

    // Database connection
    include('db.php');

    // Delete gender record
    $query = "DELETE FROM gender WHERE Id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $gender_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Gender deleted successfully!";
    } else {
        $_SESSION['status'] = "Gender deletion failed!";
    }

    $stmt->close();
    $conn->close();

    header('Location: view_gender.php');
}
?>