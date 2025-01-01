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
    $departmentId = $conn->real_escape_string($_GET['id']);

    // Prepare and execute the delete query
    $sql = "DELETE FROM Department WHERE Id='$departmentId'";

    if ($conn->query($sql) === TRUE) {
        // Redirect back to the department view page
        header("Location: view_department.php");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
} else {
    echo "No department ID provided.";
}

$conn->close();
?>
