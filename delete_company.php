<?php
// Start the session
session_start();

// Database connection details
include('db.php');

// Check if 'delete_id' is set in the POST request
if (isset($_POST['delete_id']) && !empty($_POST['delete_id'])) {
    $companyId = $conn->real_escape_string($_POST['delete_id']);

    // Prepare and execute the delete query
    $sql = "DELETE FROM company WHERE Id='$companyId'";

    if ($conn->query($sql) === TRUE) {
        // Set success message in session
        $_SESSION['success'] = "Company deleted successfully!";
        header("Location: view_company.php");
        exit();
    } else {
        // Set error message in session
        $_SESSION['error'] = "Error deleting record: " . $conn->error;
        header("Location: view_company.php");
        exit();
    }
} else {
    // Set error message in session
    $_SESSION['error'] = "No company ID provided.";
    header("Location: view_company.php");
    exit();
}

?>