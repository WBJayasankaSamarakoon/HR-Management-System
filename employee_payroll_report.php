<?php
include('security.php');
check_login();


session_start();
include('db.php');

$year = '';
$month = '';

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' || isset($_GET['year']) && isset($_GET['month'])) {
    $year = isset($_POST['year']) ? $conn->real_escape_string($_POST['year']) : (isset($_GET['year']) ? $_GET['year'] : '');
    $month = isset($_POST['month']) ? $conn->real_escape_string($_POST['month']) : (isset($_GET['month']) ? $_GET['month'] : '');

    $salaryComponents = [];
    $componentQuery = "SELECT COLUMN_NAME FROM information_schema.columns 
                       WHERE TABLE_NAME = 'payroll' 
                       AND COLUMN_NAME NOT IN ('payroll_id', 'employee_id', 'basic_salary', 'deductions', 'net_salary', 'payment_date')";
    $componentResult = $conn->query($componentQuery);

    if ($componentResult && $componentResult->num_rows > 0) {
        while ($componentRow = $componentResult->fetch_assoc()) {
            $salaryComponents[] = $componentRow['COLUMN_NAME'];
        }
    }

    $componentColumns = implode(", p.", $salaryComponents);

    $query = "
        SELECT 
            e.EmpId as emp_id,
            e.NameWithInitials as name,
            COUNT(CASE 
                    WHEN (u.CheckIn IS NULL OR u.CheckIn = '00:00:00' OR u.CheckOut IS NULL OR u.CheckOut = '00:00:00')
                    THEN 1 
                 END) as no_pay_count,
            IFNULL(l.leave_days, 0) as leave_days,
            IFNULL(h.holiday_count, 0) as holidays,
            ROUND(SUM(u.Late)/60 + SUM(u.Early)/60, 2) as late_hours,
            p.basic_salary,
            p.deductions,
            p." . $componentColumns . ",
            (p.basic_salary + p." . implode(" + p.", $salaryComponents) . " - p.deductions) AS net_salary,
            (p.AttendanceIncentive + p.SuperAttendance + p.PerformanceIncentive + p.BRA1 + p.BRA2) AS total_allowances
        FROM ueshrattendancedaily u
        JOIN tblemployees e ON u.PersonID = e.EmpId
        LEFT JOIN (
            SELECT employee_id, SUM(DATEDIFF(end_date, start_date) + 1) AS leave_days 
            FROM employee_leaves 
            WHERE status = 'Approved' 
            AND ((YEAR(start_date) = '$year' AND MONTH(start_date) = '$month')
                  OR (YEAR(end_date) = '$year' AND MONTH(end_date) = '$month'))
            GROUP BY employee_id
        ) l ON e.id = l.employee_id
        LEFT JOIN (
            SELECT COUNT(*) as holiday_count 
            FROM event 
            WHERE YEAR(Date) = '$year' AND MONTH(Date) = '$month'
        ) h ON 1=1
        LEFT JOIN payroll p ON e.id = p.employee_id
        WHERE u.Year = '$year' AND u.Month = '$month'
        GROUP BY e.EmpId
    ";

    $result = mysqli_query($conn, $query);

    if (!$result) {
        die('Query Failed: ' . mysqli_error($conn));
    }
} else {
    $result = null;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Payroll Report</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include('includes/header.php'); ?>
    <?php include('includes/navbar.php'); ?>

    <div class="container mt-5">
        <h2>Employee Payroll and Attendance Report</h2>

        <!-- Filter Form -->
        <form method="post" id="filterForm" class="mb-4">
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="year">Year</label>
                    <select name="year" id="year" class="form-control">
                        <option value="">Select Year</option>
                        <?php
                        $yearsResult = $conn->query("SELECT DISTINCT Year FROM ueshrattendancedaily ORDER BY Year DESC");
                        while ($row = $yearsResult->fetch_assoc()) {
                            $selected = ($row['Year'] == $year) ? 'selected' : '';
                            echo "<option value='" . $row['Year'] . "' $selected>" . $row['Year'] . "</option>";
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
                            $selected = ($num == $month) ? 'selected' : '';
                            echo "<option value='$num' $selected>$name</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
        </form>

        <!-- Display Results -->
        <?php if ($result && mysqli_num_rows($result) > 0): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Emp ID</th>
                        <th>Name</th>
                        <th>No Pay Days</th>
                        <th>Leave Days</th>
                        <th>Holidays</th>
                        <th>Late Hours</th>
                        <th>Basic Salary</th>
                        <th>Allowances</th>
                        <th>Deductions</th>
                        <th>Net Salary</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo htmlentities($row['emp_id']); ?></td>
                            <td><?php echo htmlentities($row['name']); ?></td>
                            <td><?php echo htmlentities($row['no_pay_count']); ?></td>
                            <td><?php echo htmlentities($row['leave_days']); ?></td>
                            <td><?php echo htmlentities($row['holidays']); ?></td>
                            <td><?php echo number_format((float) $row['late_hours'], 2); ?>h</td>
                            <td><?php echo number_format((float) $row['basic_salary'], 2); ?></td>
                            <td>
                                <?php
                                $total_allowances = 0;
                                foreach ($salaryComponents as $component) {
                                    $total_allowances += (float) $row[$component];
                                }
                                echo number_format((float) $total_allowances, 2);
                                ?>
                            </td>
                            <td><?php echo number_format((float) $row['deductions'], 2); ?></td>
                            <td><?php echo number_format((float) $row['net_salary'], 2); ?></td>
                            <td>
                                <form action="generate_report.php" method="POST">
                                    <input type="hidden" name="emp_id" value="<?php echo $row['emp_id']; ?>">
                                    <input type="hidden" name="year" value="<?php echo $year; ?>">
                                    <input type="hidden" name="month" value="<?php echo $month; ?>">
                                    <button type="submit" class="btn btn-info btn-sm">Report</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No records found for the selected month and year.</p>
        <?php endif; ?>
    </div>

    <script>
        document.getElementById('year').addEventListener('change', function () {
            document.getElementById('filterForm').submit();
        });

        document.getElementById('month').addEventListener('change', function () {
            document.getElementById('filterForm').submit();
        });
    </script>

    <!-- Include Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>