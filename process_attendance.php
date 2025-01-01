<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('security.php');
check_login();

include('includes/header.php');
include('includes/navbar.php'); 

// Database connection details
include('db.php');

// Handle form submission for filtering attendance records
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['delete_selected'])) {
    $personID = isset($_POST['personID']) ? $conn->real_escape_string($_POST['personID']) : '';
    $name = isset($_POST['name']) ? $conn->real_escape_string($_POST['name']) : '';
    $department = isset($_POST['department']) ? $conn->real_escape_string($_POST['department']) : '';
    $year = isset($_POST['year']) ? $conn->real_escape_string($_POST['year']) : '';
    $month = isset($_POST['month']) ? $conn->real_escape_string($_POST['month']) : '';

    $sql = "SELECT 
            a.PersonID,
            a.Name, 
            a.Department, 
            a.Date, 
            DAYNAME(a.Date) AS Day, 
            a.CheckIn, 
            a.CheckOut, 
            s.StartTime, 
            s.EndTime,
            TIMEDIFF(a.CheckIn, s.StartTime) AS LateIn,
            CASE
                WHEN a.CheckOut < s.EndTime THEN TIMEDIFF(s.EndTime, a.CheckOut)
                ELSE '00:00:00'
            END AS EarlyOut,
            (SELECT GROUP_CONCAT(Title) FROM event WHERE event.Date = a.Date) AS Holiday,
            (SELECT GROUP_CONCAT(reason) FROM employee_leaves el WHERE el.employee_id = a.PersonID AND a.Date BETWEEN el.start_date AND el.end_date) AS LeaveReason
        FROM 
            ueshrattendancedaily a 
        LEFT JOIN 
            shift s ON DAYNAME(a.Date) = s.Week
        WHERE a.Year = '$year' AND a.Month = '$month'";

    if ($personID)
        $sql .= " AND a.PersonID LIKE '%$personID%'";
    if ($name)
        $sql .= " AND a.Name LIKE '%$name%'";
    if ($department)
        $sql .= " AND a.Department LIKE '%$department%'";

    $result = $conn->query($sql);
    if (!$result) {
        echo "Error: " . $conn->error;
    }
}

// Handle form submission for deleting selected rows
if (isset($_POST['delete_selected'])) {
    $deleteIDs = $_POST['delete_ids'] ?? [];
    $deleteDates = $_POST['delete_dates'] ?? [];

    if (!empty($deleteIDs) && !empty($deleteDates)) {
        foreach ($deleteIDs as $index => $id) {
            $date = $deleteDates[$index];
            $deleteSql = "DELETE FROM ueshrattendancedaily WHERE PersonID = '$id' AND Date = '$date'";
            $conn->query($deleteSql);
        }
        echo "<script>alert('Selected records deleted successfully');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Process Attendance Records</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>

    <style>
        .dataTables_filter {
            float: right !important;
            margin-bottom: 10px;
        }

        .dataTables_length {
            float: left !important;
            margin-top: 10px;
        }

        .dt-bootstrap4 .row {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .table-responsive {
            margin: 0 auto;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="text-center mb-4">Attendance Records</h1>

        <!-- Filter Form -->
        <form method="post" class="mb-4">
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="year">Year</label>
                    <select name="year" id="year" class="form-control">
                        <option value="">Select Year</option>
                        <?php
                        $yearsResult = $conn->query("SELECT DISTINCT year FROM uploaded_files ORDER BY year DESC");
                        while ($row = $yearsResult->fetch_assoc()) {
                            echo "<option value='" . $row['year'] . "'>" . $row['year'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="month">Month</label>
                    <select name="month" id="month" class="form-control">
                        <option value="">Select Month</option>
                        <?php
                        $months = [
                            '01' => 'January',
                            '02' => 'February',
                            '03' => 'March',
                            '04' => 'April',
                            '05' => 'May',
                            '06' => 'June',
                            '07' => 'July',
                            '08' => 'August',
                            '09' => 'September',
                            '10' => 'October',
                            '11' => 'November',
                            '12' => 'December'
                        ];
                        foreach ($months as $num => $name) {
                            echo "<option value='$num'>$name</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="personID">Person ID</label>
                    <input type="text" name="personID" id="personID" class="form-control" placeholder="Enter Person ID">
                </div>
                <div class="form-group col-md-3">
                    <label for="department">Department</label>
                    <input type="text" name="department" id="department" class="form-control"
                        placeholder="Enter Department">
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Filter Records</button>
        </form>

        <!-- Attendance Table with Delete Functionality -->
        <form method="post">
            <div class="table-responsive">
                <table id="attendanceTable" class="table table-striped mt-4">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>Person ID</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Date</th>
                            <th>Day</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Shift Start</th>
                            <th>Shift End</th>
                            <th>Late In</th>
                            <th>Early Out</th>
                            <th>Holidays</th>
                            <th>Leave</th>
                            <th>No Pay</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (isset($result) && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $lateIn = ($row['CheckIn'] == '00:00:00' || $row['CheckIn'] == null) ? 'Not Work' : (($row['LateIn'] > '00:00:00') ? $row['LateIn'] : 'On Time');
                                $earlyOut = ($row['CheckOut'] == '00:00:00' || $row['CheckOut'] == null) ? 'Not Work' : (($row['EarlyOut'] < '00:00:00') ? str_replace("-", "", $row['EarlyOut']) : 'On Time');
                                $holiday = ($row['Holiday']) ? $row['Holiday'] : 'No Holiday';
                                $leave = ($row['LeaveReason']) ? $row['LeaveReason'] : 'No Leave';
                                $noPay = ($leave === 'No Leave' && ($lateIn === 'Not Work' || $earlyOut === 'Not Work')) ? 'No Pay' : '';

                                echo "<tr>
                                        <td><input type='checkbox' name='delete_ids[]' value='{$row['PersonID']}'>
                                            <input type='hidden' name='delete_dates[]' value='{$row['Date']}'></td>
                                        <td>{$row['PersonID']}</td>
                                        <td>{$row['Name']}</td>
                                        <td>{$row['Department']}</td>
                                        <td>{$row['Date']}</td>
                                        <td>{$row['Day']}</td>
                                        <td>{$row['CheckIn']}</td>
                                        <td>{$row['CheckOut']}</td>
                                        <td>{$row['StartTime']}</td>
                                        <td>{$row['EndTime']}</td>
                                        <td>{$lateIn}</td>
                                        <td>{$earlyOut}</td>
                                        <td>{$holiday}</td>
                                        <td>{$leave}</td>
                                        <td>{$noPay}</td>
                                        <td><a href='edit_attendance.php?personID={$row['PersonID']}&date={$row['Date']}' class='btn btn-sm btn-warning'>Edit</a></td>
                                    </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='16' class='text-center'>No records found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <button type="submit" name="delete_selected" class="btn btn-danger btn-sm mt-4">Delete Selected</button>
        </form>

    </div>
    
    <!-- jQuery, Bootstrap JS, and DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>


    <script>
        $(document).ready(function () {
            $('#attendanceTable').DataTable();

            // Select/Deselect all checkboxes
            $('#selectAll').on('click', function () {
                var rows = $(this).closest('table').find('tbody tr');
                $('input[type="checkbox"]', rows).prop('checked', this.checked);
            });
        });
    </script>
</body>

<!-- Include footer -->
<?php
include('includes/footer.php');
?>

</html>

