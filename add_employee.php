<?php
session_start();
error_reporting(E_ALL); // Enable all error reporting for debugging
include('db.php');

$msg = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $empCode = $_POST['empcode'];
    $department = $_POST['department'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];

    // Validate input (you can add more validation as needed)
    if (empty($empCode) || empty($department) || empty($firstName) || empty($lastName) || empty($dob) || empty($gender)) {
        $error = "All fields are required.";
    } else {
        // Prepare an SQL statement to insert data
        $sql = "INSERT INTO tblemployees (EmpId, FirstName, LastName, Department, Dob, Gender) VALUES (?, ?, ?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssssss", $empCode, $firstName, $lastName, $department, $dob, $gender);
            if (mysqli_stmt_execute($stmt)) {
                $msg = "Employee added successfully.";
            } else {
                $error = "Error executing query: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        } else {
            $error = "Error preparing statement: " . mysqli_error($conn);
        }
    }

    // Redirect back to manage_employee.php with a message
    if ($msg) {
        $_SESSION['msg'] = $msg;
    } else if ($error) {
        $_SESSION['error'] = $error;
    }
    header("Location: manage_employee.php");
    exit();
}
?>