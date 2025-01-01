<?php
// Start session to manage success or error messages
session_start();

// Database connection details
include('db.php');

if (isset($_POST['add_machine'])) {
    // Get the form data
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $model = mysqli_real_escape_string($conn, $_POST['model']);
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);

    // SQL query to insert the machine data
    $sql = "INSERT INTO machine (Name, Model, Brand) VALUES ('$name', '$model', '$brand')";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['success'] = "Machine added successfully!";
    } else {
        $_SESSION['error'] = "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Close the connection
$conn->close();

// Redirect back to the view_machine.php page
header("Location: view_machine.php");
exit();
?>