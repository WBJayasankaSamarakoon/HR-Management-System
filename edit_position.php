<?php
include('security.php');

if (isset($_POST['update_position'])) {
    $position_id = $_POST['position_id'];
    $name = $_POST['name'];

    // Database connection
    include('db.php');
    
    // Update position data
    $query = "UPDATE position SET Name=? WHERE Id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $name, $position_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "position updated successfully!";
    } else {
        $_SESSION['status'] = "position update failed!";
    }

    $stmt->close();
    $conn->close();

    header('Location: view_position.php');
}
?>