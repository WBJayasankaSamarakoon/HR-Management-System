<?php
// Database connection details
include('db.php');

// Check if 'id' is set in the GET request
if (isset($_POST['delete_id']) && !empty($_POST['delete_id'])) {
    $machineId = $conn->real_escape_string($_POST['delete_id']);

    // Prepare and execute the delete query
    $sql = "DELETE FROM machine WHERE Id='$machineId'";

    if ($conn->query($sql) === TRUE) {
        // Redirect back to the machine view page
        $_SESSION['success'] = "Machine deleted successfully!";
        header("Location: view_machine.php");
        exit();
    } else {
        $_SESSION['error'] = "Error deleting record: " . $conn->error;
        header("Location: view_machine.php");
        exit();
    }
} else {
    $_SESSION['error'] = "No machine ID provided.";
    header("Location: view_machine.php");
    exit();
}

?>