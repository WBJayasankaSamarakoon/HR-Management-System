<?php
// Enable error reporting to debug issues
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database connection
include('db.php');

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_attendance_btn'])) {
    // Get the date from the hidden input field
    $delete_date = $conn->real_escape_string($_POST['delete_date']);

    // SQL query to delete the attendance record
    $sql = "DELETE FROM ueshrattendancedaily WHERE Date = '$delete_date'";

    // Execute the query
    if ($conn->query($sql) === TRUE) {
        // Redirect to process_attendance.php with a success message
        header("Location: process_attendance.php?msg=Record deleted successfully");
        exit();
    } else {
        // If there's an error, redirect back with an error message
        header("Location: process_attendance.php?msg=Error deleting record: " . $conn->error);
        exit();
    }
} else {
    // If the request method is not POST, redirect to the attendance process page
    header("Location: process_attendance.php");
    exit();
}
?>