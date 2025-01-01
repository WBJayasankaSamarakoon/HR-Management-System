<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $start = $_POST['start'];
    $end = $_POST['end'];

    // Database connection
    $conn = new mysqli('localhost', 'ultieeel_ultieeel_ueshrdb_user', 'FLnQFozJp,Hj', 'ultieeel_ueshrdb');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Insert new event into the database
    $sql = "INSERT INTO event (Title, Date, StartTime, EndTime) VALUES ('$title', '$start', '$start', '$end')";

    if ($conn->query($sql) === TRUE) {
        echo "Success";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>
