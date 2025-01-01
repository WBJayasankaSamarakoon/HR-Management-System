<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    $_SESSION['username'] = null;
}

include('db.php');

// Initialize variables
$result = null;

$query = "SELECT e.EmpId AS EmpID, e.NameWithInitials AS EmployeeName, lt.LeaveType, COUNT(el.id) AS LeaveCount,
                 (SELECT COUNT(id) FROM employee_leaves WHERE employee_id = e.id) AS TotalLeaves
          FROM tblemployees e
          LEFT JOIN employee_leaves el ON e.id = el.employee_id
          LEFT JOIN tblleavetype lt ON el.leave_type_id = lt.id
          GROUP BY e.EmpId, lt.id";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Employee Leave</title>
    
    <!-- Bootstrap CSS -->
    <link href="src/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="src/dataTables.bootstrap4.min.css" rel="stylesheet">

</head>

<body>
    <?php include('includes/header.php'); ?>
    <?php include('includes/navbar.php'); ?>

    <div class="container mt-4">
        <h2>Employee Leave</h2>

        <!-- Total Leaves Section -->
        <div class="alert alert-info" role="alert">
            <h4 class="alert-heading">Total Leaves Count Per Employee</h4>
            <p>The total number of leaves for each employee is displayed in the table below.</p>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Emp ID & Name</th>
                    <th>Leave Type</th>
                    <th>Total Leaves by Type</th>
                    <th>Total Leaves</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Check if the query result has rows
                if ($result && mysqli_num_rows($result) > 0) {
                    $currentEmployeeId = null;
                    $totalLeavesCount = 0;

                    while ($row = mysqli_fetch_assoc($result)) {
                        if ($currentEmployeeId != $row['EmpID']) {
                            if ($currentEmployeeId !== null) {
                                echo "<tr><td colspan='3'><strong>Total Leaves for {$currentEmployeeName}:</strong> {$totalLeavesCount}</td></tr>";
                            }
                            // Update current employee ID and initialize count
                            $currentEmployeeId = $row['EmpID'];
                            $currentEmployeeName = $row['EmployeeName'];
                            $totalLeavesCount = 0;
                        }

                        $totalLeavesCount += $row['LeaveCount'];
                        ?>
                        <tr>
                            <td><?php echo $row['EmpID'] . " - " . $row['EmployeeName']; ?></td>
                            <td><?php echo $row['LeaveType']; ?></td>
                            <td><?php echo $row['LeaveCount']; ?></td>
                            <td><?php echo $totalLeavesCount; ?></td>
                            <td>
                                <a href="mng_em_lv_delete.php?id=<?php echo htmlentities($row['EmpID']); ?>"
                                    class="btn btn-sm btn-danger" title="Delete"
                                    onclick="return confirm('Do you want to delete this leave record?');">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                    <?php }
                    // Print the total leaves row for the last employee
                    if ($currentEmployeeId !== null) {
                        echo "<tr><td colspan='3'><strong>Total Leaves for {$currentEmployeeName}:</strong> {$totalLeavesCount}</td></tr>";
                    }
                } else { ?>
                    <tr>
                        <td colspan="5">No data available</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <!-- Include Bootstrap JS, jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>

<?php
// Close the database connection
$conn->close();
include('includes/scripts.php');
include('includes/footer.php');
?>