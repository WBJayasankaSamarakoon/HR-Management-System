<?php
include('security.php');

if (isset($_POST['update_gender'])) {
    $gender_id = $_POST['gender_id'];
    $name = $_POST['name'];

    // Database connection
    include('db.php');

    // Update gender data
    $query = "UPDATE gender SET Name=? WHERE Id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $name, $gender_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Gender updated successfully!";
    } else {
        $_SESSION['status'] = "Gender update failed!";
    }

    $stmt->close();
    $conn->close();

    header('Location: view_gender.php');
}
?>