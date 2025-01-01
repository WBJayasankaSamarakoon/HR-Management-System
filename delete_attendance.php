<?php
include('db.php');

// Check if the delete form has been submitted
if (isset($_POST['delete_attendance_btn'])) {
    // Get the date to delete
    $delete_date = $_POST['delete_date'];

    // Sanitize the input
    $delete_date = mysqli_real_escape_string($conn, $delete_date);

    // SQL query to delete the record based on the date
    $sql = "DELETE FROM ueshrattendancedaily WHERE Date = '$delete_date'";

    if ($conn->query($sql) === TRUE) {
        // Redirect to the previous page or a success page
        header('Location: up_viewbtn.php');
    } else {
        // Handle errors
        echo "Error: " . $conn->error;
    }
}

$conn->close();
?>