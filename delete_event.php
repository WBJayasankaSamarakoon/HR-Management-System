<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    // Database connection
    $conn = new mysqli('localhost', 'ultieeel_ultieeel_ueshrdb_user', 'FLnQFozJp,Hj', 'ultieeel_ueshrdb');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Delete event from the database
    $sql = "DELETE FROM event WHERE Id='$id'";

    if ($conn->query($sql) === TRUE) {
        echo "Success";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>
