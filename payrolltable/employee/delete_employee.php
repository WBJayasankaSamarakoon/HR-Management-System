<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ueshrdb";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if 'id' is set in the GET request
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $employeeId = $conn->real_escape_string($_GET['id']);

    // Prepare and execute the delete query
    $sql = "DELETE FROM Employee WHERE Id='$employeeId'";

    if ($conn->query($sql) === TRUE) {
        // Redirect back to the employee view page
        header("Location: view_employee.php");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
} else {
    echo "No employee ID provided.";
}

$conn->close();
?>
