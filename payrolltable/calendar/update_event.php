<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $start = $_POST['start'];
    $end = $_POST['end'];

    // Database connection
    $conn = new mysqli('localhost', 'ultieeel_ultieeel_ueshrdb_user', 'FLnQFozJp,Hj', 'ultieeel_ueshrdb');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("UPDATE event SET StartTime=?, EndTime=? WHERE Id=?");
    $stmt->bind_param("ssi", $start, $end, $id);

    if ($stmt->execute()) {
        echo "Success";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
