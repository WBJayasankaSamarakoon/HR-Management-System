<?php
// Database connection details
include('db.php');

// Check if the form is submitted
if (isset($_POST['add_company'])) {
    // Get form data
    $name = $conn->real_escape_string($_POST['name']);
    $address = $conn->real_escape_string($_POST['address']);
    $email = $conn->real_escape_string($_POST['email']);
    $telephone = $conn->real_escape_string($_POST['telephone']);
    $fax = $conn->real_escape_string($_POST['fax']);

    // SQL query to insert a new company
    $sql = "INSERT INTO company (Name, Address, Email, Telephone, Fax) VALUES ('$name', '$address', '$email', '$telephone', '$fax')";

    if ($conn->query($sql) === TRUE) {
        // Redirect to the view_company.php page with success message
        header("Location: view_company.php?status=success");
        exit();
    } else {
        // Redirect to the view_company.php page with error message
        header("Location: view_company.php?status=error");
        exit();
    }
}

// Close the connection
$conn->close();
?>