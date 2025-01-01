<?php
session_start();
include('db.php');

if (isset($_GET['id'])) {
    $leave_id = $_GET['id'];

    // Prepare the DELETE statement
    $sql = "DELETE FROM employee_leaves WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $leave_id);
        if (mysqli_stmt_execute($stmt)) {
            // Success message
            $_SESSION['msg'] = "Leave record deleted successfully.";
        } else {
            // Error message
            $_SESSION['error'] = "Error deleting record: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        // Prepare statement error
        $_SESSION['error'] = "Error preparing statement: " . mysqli_error($conn);
    }

    // Redirect to the management page
    header("Location: manage_leaveemp.php");
    exit();
} else {
    // Handle the case where no ID was provided
    $_SESSION['error'] = "No leave ID specified.";
    header("Location: manage_leaveemp.php");
    exit();
}
?>