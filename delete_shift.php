<?php
include('security.php');

if (isset($_POST['delete_shift_btn'])) {
    $shift_id = $_POST['delete_id'];

    // Database connection
    include('db.php');

    // Delete shift data
    $query = "DELETE FROM shift WHERE Id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $shift_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Shift deleted successfully!";
    } else {
        $_SESSION['status'] = "Shift deletion failed!";
    }

    $stmt->close();
    $conn->close();

    header('Location: view_shift.php');
}
?>