<?php
include('security.php');

if (isset($_POST['add_position'])) {
    $name = $_POST['name'];

    // Database connection
    include('db.php');

    // Insert new position
    $query = "INSERT INTO position (Name) VALUES (?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $name);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Position added successfully!";
    } else {
        $_SESSION['status'] = "Position addition failed!";
    }

    $stmt->close();
    $conn->close();

    header('Location: view_position.php');
}
?>