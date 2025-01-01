<?php
include('security.php');

if (isset($_POST['add_gender'])) {
    $name = $_POST['name'];

    // Database connection
    include('db.php');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Insert new gender
    $query = "INSERT INTO gender (Name) VALUES (?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $name);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Gender added successfully!";
    } else {
        $_SESSION['status'] = "Gender addition failed!";
    }

    $stmt->close();
    $conn->close();

    header('Location: view_gender.php');
}
?>