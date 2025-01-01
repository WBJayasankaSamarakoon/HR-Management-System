<?php
include('security.php');

if (isset($_POST['update_shift'])) {
    $shift_id = $_POST['shift_id'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $week = $_POST['week'];

    // Database connection
    include('db.php');

    // Update shift data
    $query = "UPDATE shift SET StartTime=?, EndTime=?, Week=? WHERE Id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssi", $start_time, $end_time, $week, $shift_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Shift updated successfully!";
    } else {
        $_SESSION['status'] = "Shift update failed!";
    }

    $stmt->close();
    $conn->close();

    header('Location: view_shift.php');
}
?>