<?php
include('security.php');

if (isset($_POST['add_shift'])) {
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $week = $_POST['week'];

    // Database connection
    include('db.php');

    // Insert shift data
    $query = "INSERT INTO shift (StartTime, EndTime, Week) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $start_time, $end_time, $week);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Shift added successfully!";
    } else {
        $_SESSION['status'] = "Shift addition failed!";
    }

    $stmt->close();
    $conn->close();

    header('Location: view_shift.php');
}
?>