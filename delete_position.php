<?php
include('security.php');

if (isset($_POST['delete_position_btn'])) {
    $position_id = $_POST['delete_id'];

    // Database connection
    include('db.php');

    // Delete position
    $query = "DELETE FROM position WHERE Id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $position_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Position deleted successfully!";
    } else {
        $_SESSION['status'] = "Position deletion failed!";
    }

    $stmt->close();
    $conn->close();

    header('Location: view_position.php');
}
?>