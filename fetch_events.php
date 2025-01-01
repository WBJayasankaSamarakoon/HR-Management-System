<?php
// Database connection
$conn = new mysqli('localhost', 'ultieeel_ultieeel_ueshrdb_user', 'FLnQFozJp,Hj', 'ultieeel_ueshrdb');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch events
$sql = "SELECT Id, Title, Description, Date, StartTime, EndTime FROM event";
$result = $conn->query($sql);

$events = [];

while ($row = $result->fetch_assoc()) {
    $events[] = [
        'id' => $row['Id'],
        'title' => $row['Title'],
        'start' => $row['Date'] . 'T' . $row['StartTime'],
        'end' => $row['Date'] . 'T' . $row['EndTime'],
        'description' => $row['Description'],
    ];
}

// Return events in JSON format
echo json_encode($events);
?>
