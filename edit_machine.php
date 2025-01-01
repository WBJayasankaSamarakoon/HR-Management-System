<?php
// Database connection details
include('db.php');

// Check if form is submitted
if (isset($_POST['update_machine'])) {
    // Retrieve form data
    $machineId = $conn->real_escape_string($_POST['machine_id']);
    $name = $conn->real_escape_string($_POST['name']);
    $model = $conn->real_escape_string($_POST['model']);
    $brand = $conn->real_escape_string($_POST['brand']);

    // Prepare and execute the update query
    $sql = "UPDATE Machine SET Name='$name', Model='$model', Brand='$brand' WHERE Id='$machineId'";

    if ($conn->query($sql) === TRUE) {
        // Redirect back to the machine view page with a success message
        $_SESSION['success'] = "Machine updated successfully!";
        header("Location: view_machine.php");
        exit();
    } else {
        // Display error message if the query fails
        $_SESSION['error'] = "Error updating record: " . $conn->error;
        header("Location: view_machine.php");
        exit();
    }
} else {
    // Redirect if the form is not submitted
    $_SESSION['error'] = "Form not submitted.";
    header("Location: view_machine.php");
    exit();
}

?>