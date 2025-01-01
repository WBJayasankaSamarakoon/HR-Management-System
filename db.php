<?php
$servername = "localhost";
$username = "ultieeel_ultieeel_ueshrdb_user";
$password = "FLnQFozJp,Hj";
$dbname = "ultieeel_ueshrdb";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>