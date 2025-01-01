<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $title = $_POST['title'];

    // Database connection
    $conn = new mysqli('localhost', 'ultieeel_ultieeel_ueshrdb_user', 'FLnQFozJp,Hj', 'ultieeel_ueshrdb');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Update event in the database
    $sql = "UPDATE event SET Title='$title' WHERE Id='$id'";

    if ($conn->query($sql) === TRUE) {
        echo "Success";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>
