<?php
include('security.php');
check_login();
include('db.php');
include('includes/header.php');
include('includes/navbar.php');

// Retrieve filename, year, and month from the URL
$filename = isset($_GET['filename']) ? $conn->real_escape_string($_GET['filename']) : null;
$year = isset($_GET['year']) ? $conn->real_escape_string($_GET['year']) : null;
$month = isset($_GET['month']) ? $conn->real_escape_string($_GET['month']) : null;

// Fetch records from the ueshrattendancedaily table
if ($filename && $year && $month) {
    $query = "SELECT * FROM ueshrattendancedaily WHERE Year = ? AND Month = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $year, $month); // Use 'is' for int and string
    $stmt->execute();
    $result = $stmt->get_result();

    echo '<div class="card">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-calendar-alt"></i> Attendance Records for ' . htmlspecialchars($filename) . ' (' . htmlspecialchars($year) . '-' . htmlspecialchars($month) . ')
            </div>
            <div class="card-body">';

    // Updated buttons for "Process" and "Back"
    echo '<div class="mb-3">
            <a href="employee_payroll_report.php?filename=' . urlencode($filename) . '&year=' . urlencode($year) . '&month=' . urlencode($month) . '" class="btn btn-success">Process</a>
            <a href="view_uploads.php" class="btn btn-secondary">Back</a>
          </div>';

    if ($result->num_rows > 0) {
        // Add responsive table with Bootstrap
        echo '<div class="table-responsive">';
        echo "<table id='attendanceTable' class='table table-bordered table-striped table-sm'>";
        echo "<thead class='thead-dark'>
                <tr>
                    <th>Index</th>
                    <th>Person ID</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Position</th>
                    <th>Gender</th>
                    <th>Date</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Work</th>
                    <th>OT</th>
                    <th>Attended</th>
                    <th>Late</th>
                    <th>Early</th>
                    <th>Absent</th>
                    <th>Leave</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['Index']) . "</td>";
            echo "<td>" . htmlspecialchars($row['PersonID']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Department']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Position']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Gender']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Date']) . "</td>";
            echo "<td>" . htmlspecialchars($row['CheckIn']) . "</td>";
            echo "<td>" . htmlspecialchars($row['CheckOut']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Work']) . "</td>";
            echo "<td>" . htmlspecialchars($row['OT']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Attended']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Late']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Early']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Absent']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Leave']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Status']) . "</td>";
            echo "</tr>";
        }

        echo "</tbody></table>";
        echo '</div>';
    } else {
        echo '<div class="alert alert-warning">No attendance records found for the selected file.</div>';
    }

    echo '</div>';
    echo '</div>';
} else {
    echo "<div class='alert alert-danger'>Invalid parameters.</div>";
}

// Close the connection
$conn->close();
?>